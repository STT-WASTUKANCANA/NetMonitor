"""
Device Discovery Router
API endpoints for network device auto-discovery.
"""
import subprocess
import json
import os
import sys
from pathlib import Path
from fastapi import APIRouter, Depends, HTTPException, BackgroundTasks
from sqlalchemy.orm import Session
from typing import Dict, List
from datetime import datetime

from app.database import get_db
from app.models.user import User
from app.models.device import Device
from app.middleware.auth import get_current_user


router = APIRouter(prefix="/api/devices", tags=["Device Discovery"])


# Global state for discovery (in production, use Redis or similar)
discovery_state = {
    "running": False,
    "progress": 0,
    "total": 0,
    "devices": [],
    "error": None
}


def run_discovery_scan(network: str = None):
    """Run device discovery in background."""
    global discovery_state
    
    try:
        discovery_state["running"] = True
        discovery_state["progress"] = 0
        discovery_state["error"] = None
        discovery_state["devices"] = []
        
        # Path to detect_ip.py script - use absolute path
        
        # Get project root
        current_file = Path(__file__).resolve()
        project_root = current_file.parent.parent.parent
        script_path = project_root / 'scripts' / 'detect_ip.py'
        
        print(f"DEBUG: Script path: {script_path}", flush=True)
        print(f"DEBUG: Script exists: {script_path.exists()}", flush=True)
        
        if not script_path.exists():
            discovery_state["error"] = f"Script not found: {script_path}"
            return
        
        # Run discovery script with json, quiet flags, and save-db
        # Use sys.executable to ensure we use the same python environment
        cmd = [sys.executable, str(script_path), '--json', '--quiet', '--save-db']
        if network:
            cmd.extend(['--range', network])
        
        print(f"DEBUG: Running command: {' '.join(cmd)}", flush=True)
        
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=300,  # 5 minutes max
            cwd=str(project_root)
        )
        
        print(f"DEBUG: Return code: {result.returncode}", flush=True)
        print(f"DEBUG: Stdout length: {len(result.stdout)}", flush=True)
        print(f"DEBUG: Stderr: {result.stderr[:500] if result.stderr else 'None'}", flush=True)
        
        if result.returncode == 0:
            # Parse JSON output
            try:
                data = json.loads(result.stdout)
                discovery_state["devices"] = data.get("devices", [])
                discovery_state["total"] = data.get("total_devices", 0)
                discovery_state["progress"] = 100
                print(f"DEBUG: Parsed {len(discovery_state['devices'])} devices", flush=True)
            except json.JSONDecodeError as e:
                discovery_state["error"] = f"Failed to parse JSON: {e}. Output: {result.stdout[:200]}"
                print(f"DEBUG: JSON parse error: {e}", flush=True)
        else:
            discovery_state["error"] = f"Discovery failed (exit {result.returncode}): {result.stderr}"
            print(f"DEBUG: Script failed: {result.stderr}", flush=True)
            
    except subprocess.TimeoutExpired:
        discovery_state["error"] = "Discovery timeout (>5 minutes)"
        print("DEBUG: Timeout expired", flush=True)
    except Exception as e:
        discovery_state["error"] = f"Discovery error: {str(e)}"
        print(f"DEBUG: Exception: {e}", flush=True)
        import traceback
        traceback.print_exc()
    finally:
        discovery_state["running"] = False


@router.post("/discover", response_model=dict)
async def start_discovery(
    background_tasks: BackgroundTasks,
    network: str = None,
    current_user: User = Depends(get_current_user)
):
    """
    Start network device discovery.
    
    - **network**: Network CIDR to scan (optional, auto-detect if not provided)
    """
    global discovery_state
    
    if discovery_state["running"]:
        raise HTTPException(status_code=409, detail="Discovery already running")
    
    # Only admin can run discovery
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    # Start discovery in background
    background_tasks.add_task(run_discovery_scan, network)
    
    return {
        "success": True,
        "message": "Device discovery started",
        "data": {
            "status": "running"
        }
    }


@router.get("/discover/status", response_model=dict)
async def get_discovery_status(current_user: User = Depends(get_current_user)):
    """Get current discovery status."""
    global discovery_state
    
    return {
        "success": True,
        "data": {
            "running": discovery_state["running"],
            "progress": discovery_state["progress"],
            "total_found": len(discovery_state["devices"]),
            "error": discovery_state["error"]
        }
    }


@router.get("/discover/results", response_model=dict)
async def get_discovery_results(current_user: User = Depends(get_current_user)):
    """Get discovered devices."""
    global discovery_state
    
    return {
        "success": True,
        "data": {
            "devices": discovery_state["devices"],
            "total": len(discovery_state["devices"])
        }
    }


@router.post("/discover/save", response_model=dict)
async def save_discovered_devices(
    devices: List[Dict],
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    """
    Save discovered devices to database.
    Only saves devices with hierarchy_level 'utama' or 'sub'.
    Devices with hierarchy 'device' are skipped.
    Sub devices are automatically linked to utama (gateway) as parent.
    
    - **devices**: List of devices to save
    """
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    saved_count = 0
    skipped_count = 0
    filtered_count = 0
    errors = []
    
    # Helper to map detailed type to allowed Enum
    valid_types = ['router', 'switch', 'access_point', 'server', 'firewall', 'other']
    
    def map_type(detailed_type: str) -> str:
        if not detailed_type:
            return 'other'
        detailed = detailed_type.lower()
        if detailed in valid_types:
            return detailed
        if 'server' in detailed or 'nas' in detailed:
            return 'server'
        if 'printer' in detailed:
            return 'other'
        return 'other'

    # First pass: Save utama device and get its ID
    utama_device_id = None
    
    # We use nested transactions (savepoints) for each insert to prevent one failure from killing the whole batch
    for device_data in devices:
        try:
            hierarchy = device_data.get("hierarchy_level", "device")
            if hierarchy != "utama":
                continue
            
            ip_address = device_data.get("ip_address")
            
            # Check if already exists
            existing = db.query(Device).filter(Device.ip_address == ip_address).first()
            if existing:
                utama_device_id = existing.id
                skipped_count += 1
                continue
            
            # Map type correctly
            device_type = map_type(device_data.get("type", "router"))

            # Create utama device
            device = Device(
                name=device_data.get("name"),
                ip_address=ip_address,
                type=device_type,
                location=device_data.get("location", "Auto Detected"),
                description=device_data.get("description", ""),
                port=device_data.get("port"),
                hierarchy_level="utama",
                parent_id=None,  # Utama has no parent
                status="up",
                created_by=current_user.id
            )
            
            # Use begin_nested for safe partial commits
            with db.begin_nested():
                db.add(device)
                db.flush()
                utama_device_id = device.id
                saved_count += 1
            
        except Exception as e:
            errors.append(f"{device_data.get('ip_address')} (Utama): {str(e)}")
            # No need to manual rollback as begin_nested handle it or we continue

    # Second pass: Save all other devices (sub and device)
    for device_data in devices:
        try:
            hierarchy = device_data.get("hierarchy_level", "device")
            
            # Skip utama (already processed)
            if hierarchy == "utama":
                continue
            
            # Determine parent
            # If sub, parent is utama. If device, parent is also utama (flat hierarchy for now)
            parent_id = utama_device_id
            
            ip_address = device_data.get("ip_address")
            
            # Check if device already exists
            existing = db.query(Device).filter(Device.ip_address == ip_address).first()
            
            if existing:
                skipped_count += 1
                continue
            
            # Validate and map type
            device_type = map_type(device_data.get("type", "other"))
            
            # Create device
            device = Device(
                name=device_data.get("name"),
                ip_address=ip_address,
                type=device_type,
                location=device_data.get("location", "Auto Detected"),
                description=device_data.get("description", ""),
                port=device_data.get("port"), # Note: might be None if not provided
                hierarchy_level=hierarchy,
                parent_id=parent_id,
                status="up",
                created_by=current_user.id
            )
            
            with db.begin_nested():
                db.add(device)
                db.flush()
                saved_count += 1
            
        except Exception as e:
            errors.append(f"{device_data.get('ip_address')}: {str(e)}")
    
    try:
        db.commit()
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Database error: {str(e)}")
    
    return {
        "success": True,
        "message": f"Saved {saved_count} devices (utama/sub only), skipped {skipped_count} existing, filtered {filtered_count} 'device' hierarchy",
        "data": {
            "saved": saved_count,
            "skipped": skipped_count,
            "filtered": filtered_count,
            "errors": errors,
            "utama_id": utama_device_id
        }
    }


@router.post("/discover/clear", response_model=dict)
async def clear_discovery_results(current_user: User = Depends(get_current_user)):
    """Clear discovery results."""
    global discovery_state
    
    if discovery_state["running"]:
        raise HTTPException(status_code=409, detail="Cannot clear while discovery is running")
    
    discovery_state["devices"] = []
    discovery_state["progress"] = 0
    discovery_state["total"] = 0
    discovery_state["error"] = None
    
    return {
        "success": True,
        "message": "Discovery results cleared"
    }
