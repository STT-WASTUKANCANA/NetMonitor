#!/usr/bin/env python3
"""
NetMonitor - Real-time Network Monitoring Script
Enhanced with multi-ping verification and statistical analysis
Pings network devices every 30 seconds and reports status to FastAPI backend.
Auto-creates alerts for down devices.

Version: 3.0 - Enhanced accuracy with statistical analysis
"""
import os
import sys
import time
import json
import socket
import logging
import statistics
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple, NamedTuple
import requests
from dotenv import load_dotenv
import pytz

# Add project root and scripts to path for imports
script_dir = os.path.dirname(os.path.abspath(__file__))
sys.path.append(os.path.dirname(script_dir))  # Project root
sys.path.append(script_dir)  # Scripts directory

from app.utils.time_manager import TimeManager
from utils.statistics import NetworkStatistics

# Try to import ping3, fallback to subprocess ping
try:
    from ping3 import ping
    USE_PING3 = True
except ImportError:
    import subprocess
    USE_PING3 = False

# Load environment variables from .env file in scripts directory
env_path = os.path.join(script_dir, '.env')
load_dotenv(env_path)

# Cached devices path for offline start
DEVICES_CACHE_PATH = os.path.join(script_dir, 'devices_cache.json')

# Configuration - API Settings
API_BASE_URL = os.getenv('API_BASE_URL', 'http://localhost:8001')
API_EMAIL = os.getenv('API_EMAIL', 'admin@wastukancana.ac.id')
API_PASSWORD = os.getenv('API_PASSWORD', 'password123')

# Configuration - Monitoring Settings
MONITOR_INTERVAL = 30  # Enforce 30 seconds real-time update
PING_TIMEOUT = int(os.getenv('PING_TIMEOUT', '2'))
PING_VERIFICATION_COUNT = int(os.getenv('PING_VERIFICATION_COUNT', '5'))
PING_PACKET_SIZES = [int(x) for x in os.getenv('PING_PACKET_SIZES', '64,512,1024').split(',')]

# Configuration - Accuracy Settings
DOWN_CONFIRMATION_REQUIRED = int(os.getenv('DOWN_CONFIRMATION_REQUIRED', '2'))
UP_CONFIRMATION_REQUIRED = int(os.getenv('UP_CONFIRMATION_REQUIRED', '2'))
STATISTICAL_WINDOW_SIZE = int(os.getenv('STATISTICAL_WINDOW_SIZE', '10'))

# Configuration - Alert Settings
ALERT_RESPONSE_TIME_THRESHOLD = int(os.getenv('ALERT_RESPONSE_TIME_THRESHOLD', '1000'))
ALERT_USE_STATISTICAL_THRESHOLD = os.getenv('ALERT_USE_STATISTICAL_THRESHOLD', 'true').lower() == 'true'
ALERT_FLAPPING_DETECTION = os.getenv('ALERT_FLAPPING_DETECTION', 'true').lower() == 'true'
ALERT_FLAPPING_THRESHOLD = int(os.getenv('ALERT_FLAPPING_THRESHOLD', '5'))
ALERT_CONSECUTIVE_FAILURES = int(os.getenv('ALERT_CONSECUTIVE_FAILURES', '3'))

# Timezone - GMT+7 Jakarta
# JAKARTA_TZ = pytz.timezone('Asia/Jakarta') # Used TimeManager instead

# Setup logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(),
        logging.FileHandler('monitor.log')
    ]
)
logger = logging.getLogger(__name__)


class PingResult(NamedTuple):
    """Enhanced ping result with statistical data"""
    status: str  # 'up' or 'down'
    response_time: Optional[float]  # average response time in ms
    response_time_min: Optional[float]
    response_time_max: Optional[float]
    response_time_median: Optional[float]
    response_time_std_dev: Optional[float]
    jitter: Optional[float]
    packet_loss: float  # percentage
    successful_pings: int
    total_pings: int
    confidence: int  # confidence score 0-100


def now_jakarta() -> datetime:
    """Get current time in Jakarta timezone (GMT+7)."""
    return TimeManager.get_current_time()


class NetworkMonitor:
    """Real-time network monitoring with enhanced accuracy and statistical analysis."""
    
    def __init__(self):
        self.api_base_url = API_BASE_URL
        self.token: Optional[str] = None
        self.token_expires_at: Optional[datetime] = None
        self.devices: List[Dict] = []
        
        # Offline resilience
        self.alert_queue: List[Dict] = []  # Buffer for offline alerts
        self.status_queue: List[Dict] = []  # Buffer for status updates
        self.api_available = True
        self.offline_mode = False
        
        # Enhanced device state tracking
        self.device_states: Dict[int, Dict] = {}  # Extended state information
        # State structure:
        # {
        #     'status': 'up'|'down',
        #     'last_response_time': float,
        #     'response_time_history': List[float],  # Last N measurements
        #     'baseline_response_time': float,
        #     'response_time_std_dev': float,
        #     'consecutive_failures': int,
        #     'consecutive_successes': int,
        #     'last_status_change': datetime,
        #     'jitter': float,
        #     'packet_loss_avg': float,
        #     'reliability_score': float,
        #     'status_change_history': List[tuple],  # For flapping detection
        # }
        
        # Alert thresholds
        self.response_time_threshold = ALERT_RESPONSE_TIME_THRESHOLD
        self.consecutive_failures_threshold = ALERT_CONSECUTIVE_FAILURES
        self.use_statistical_threshold = ALERT_USE_STATISTICAL_THRESHOLD
        self.flapping_detection = ALERT_FLAPPING_DETECTION
        self.flapping_threshold = ALERT_FLAPPING_THRESHOLD
        
        # Statistical analysis
        self.stats = NetworkStatistics()
        
        # Load buffered data from disk if exists
        self._load_queues()
        self._load_cached_devices()
    
    def _load_queues(self):
        """Load buffered alerts and status updates from disk."""
        try:
            if os.path.exists('alert_queue.json'):
                with open('alert_queue.json', 'r') as f:
                    self.alert_queue = json.load(f)
                logger.info(f"📥 Loaded {len(self.alert_queue)} buffered alerts")
            
            if os.path.exists('status_queue.json'):
                with open('status_queue.json', 'r') as f:
                    self.status_queue = json.load(f)
                logger.info(f"📥 Loaded {len(self.status_queue)} buffered status updates")
                    
            # Load device states
            if os.path.exists('device_states.json'):
                with open('device_states.json', 'r') as f:
                    self.device_states = json.load(f)
                    # Convert string keys back to int
                    self.device_states = {int(k): v for k, v in self.device_states.items()}
                logger.info(f"📥 Loaded state for {len(self.device_states)} devices")
        except Exception as e:
            logger.warning(f"Could not load queues: {e}")

    def _load_cached_devices(self) -> bool:
        """Load cached devices for offline start."""
        try:
            if os.path.exists(DEVICES_CACHE_PATH):
                with open(DEVICES_CACHE_PATH, 'r') as f:
                    self.devices = json.load(f)
                logger.info(f"📥 Loaded {len(self.devices)} cached devices")
                return True
        except Exception as e:
            logger.warning(f"Could not load cached devices: {e}")
        return False
    
    def _save_queues(self):
        """Save buffered data to disk for persistence."""
        try:
            with open('alert_queue.json', 'w') as f:
                json.dump(self.alert_queue, f)
            
            with open('status_queue.json', 'w') as f:
                json.dump(self.status_queue, f)
                
            # Save device states
            with open('device_states.json', 'w') as f:
                json.dump(self.device_states, f)
        except Exception as e:
            logger.warning(f"Could not save queues: {e}")

    def _save_cached_devices(self):
        """Persist last fetched devices for offline mode."""
        try:
            with open(DEVICES_CACHE_PATH, 'w') as f:
                json.dump(self.devices, f)
        except Exception as e:
            logger.warning(f"Could not cache devices: {e}")
    
    def _check_api_health(self) -> bool:
        """Check if API is reachable."""
        try:
            response = requests.get(
                f"{self.api_base_url}/health",
                timeout=5
            )
            return response.status_code == 200
        except:
            return False
    
    def _headers(self) -> Dict[str, str]:
        """Get request headers with authentication."""
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
        if self.token:
            headers["Authorization"] = f"Bearer {self.token}"
        return headers
    
    def _is_token_expired(self) -> bool:
        """Check if token is expired or will expire soon (within 2 minutes)."""
        if not self.token or not self.token_expires_at:
            return True
        # Refresh 2 minutes before expiry
        return now_jakarta() >= (self.token_expires_at - timedelta(minutes=2))
    
    def login(self) -> bool:
        """Authenticate with the API and get JWT token."""
        try:
            response = requests.post(
                f"{self.api_base_url}/api/auth/login",
                json={"email": API_EMAIL, "password": API_PASSWORD},
                headers={"Content-Type": "application/json"},
                timeout=30
            )
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.token = data["data"]["token"]
                    # Token expires in 30 minutes (from config)
                    self.token_expires_at = now_jakarta() + timedelta(minutes=30)
                    logger.info(f"✅ Login successful as {API_EMAIL}")
                    return True
            
            logger.error(f"❌ Login failed: {response.text}")
            return False
            
        except requests.exceptions.RequestException as e:
            logger.error(f"❌ Login request failed: {e}")
            return False
    
    def ensure_authenticated(self) -> bool:
        """Ensure we have a valid token, refresh if needed."""
        if self._is_token_expired():
            logger.info("🔄 Token expired or missing, re-authenticating...")
            return self.login()
        return True
    
    def fetch_devices(self) -> bool:
        """Fetch all devices from API. Continue with cached devices if offline."""
        # Try to flush buffered updates first if we have auth
        if self.token:
            self._flush_status_queue()
        
        if not self.ensure_authenticated():
            # Try to use existing devices in memory
            if self.devices:
                logger.warning("⚠️ Using cached device list (authentication unavailable)")
                self.offline_mode = True
                return True
            # Try to load from disk cache
            if self._load_cached_devices():
                logger.warning("⚠️ Using cached device list from disk (authentication unavailable)")
                self.offline_mode = True
                return True
            # No devices available - cannot monitor yet
            logger.warning("⚠️ No cached devices available - will retry on next cycle")
            self.offline_mode = True
            return False
        
        try:
            response = requests.get(
                f"{self.api_base_url}/api/devices",
                headers=self._headers(),
                params={"per_page": 100},
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.devices = data["data"]["data"]
                    logger.info(f"📡 Fetched {len(self.devices)} devices")
                    self._save_cached_devices()
                    
                    # Mark API as available
                    if self.offline_mode:
                        logger.info("🌐 API connection restored!")
                        self.offline_mode = False
                    self.api_available = True
                    
                    return True
            elif response.status_code == 401:
                # Token invalid, retry with new login
                logger.warning("🔑 Token invalid, re-authenticating...")
                if self.login():
                    return self.fetch_devices()
            
            logger.error(f"❌ Failed to fetch devices: {response.text}")
            
            # Use cached devices if available
            if self.devices:
                logger.warning("⚠️ Using cached device list")
                return True
            return False
            
        except requests.exceptions.RequestException as e:
            logger.warning(f"⚠️ Network error - using cached devices: {e}")
            self.api_available = False
            self.offline_mode = True
            
            # Continue with cached devices
            if self.devices:
                logger.info(f"📡 Continuing with {len(self.devices)} cached devices (offline mode)")
                return True
            
            logger.error("❌ No cached devices available")
            return False
    
    def ping_device_advanced(self, ip_address: str) -> PingResult:
        """
        Effectively ping a device multiple times and return statistical result.
        """
        response_times = []
        successful_pings = 0
        total_pings = PING_VERIFICATION_COUNT
        
        # Use ping3 if available
        if USE_PING3:
            for _ in range(total_pings):
                try:
                    rtt = ping(ip_address, timeout=PING_TIMEOUT)
                    if rtt is not None:
                        successful_pings += 1
                        response_times.append(rtt * 1000)  # Convert to ms
                except Exception:
                    pass
                time.sleep(0.1)  # Brief pause between pings
        else:
            # Fallback to subprocess
            # We run ping command multiple times manually to get individual RTTs
            # instead of relying on ping command summary which varies by OS
            for _ in range(total_pings):
                try:
                    cmd = ["ping", "-c", "1", "-W", str(PING_TIMEOUT), ip_address]
                    result = subprocess.run(cmd, capture_output=True, text=True, timeout=PING_TIMEOUT + 1)
                    if result.returncode == 0:
                        # Parse output
                        output = result.stdout
                        if "time=" in output or "time<" in output:
                            # Extract time
                            parts = output.split("time=")
                            if len(parts) > 1:
                                ms_part = parts[1].split(" ")[0].replace("ms", "")
                                try:
                                    response_times.append(float(ms_part))
                                    successful_pings += 1
                                except ValueError:
                                    pass
                except Exception:
                    pass
                time.sleep(0.1)
        
        # Calculate statistics
        packet_loss = ((total_pings - successful_pings) / total_pings) * 100
        status = "up" if successful_pings > 0 else "down"
        
        # Determine strict status based on packet loss
        # If packet loss > 50%, consider it unreliable/down
        if packet_loss > 50:
            status = "down"
        
        # Calculate metrics
        avg_time = statistics.mean(response_times) if response_times else None
        stats = self.stats.calculate_percentiles(response_times) if response_times else None
        std_dev = self.stats.calculate_std_dev(response_times)
        jitter = self.stats.calculate_jitter(response_times)
        
        # Calculate confidence
        confidence = 100
        if packet_loss > 0:
            confidence -= packet_loss
        if jitter and jitter > 50:
            confidence -= 20
        confidence = max(0, int(confidence))
        
        return PingResult(
            status=status,
            response_time=round(avg_time, 2) if avg_time is not None else None,
            response_time_min=stats['min'] if stats else None,
            response_time_max=stats['max'] if stats else None,
            response_time_median=stats['p50'] if stats else None,
            response_time_std_dev=round(std_dev, 2) if std_dev is not None else None,
            jitter=jitter,
            packet_loss=round(packet_loss, 2),
            successful_pings=successful_pings,
            total_pings=total_pings,
            confidence=confidence
        )
    
    def check_port(self, ip_address: str, port: int) -> bool:
        """Check if a port is open on the device."""
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(5)
            result = sock.connect_ex((ip_address, port))
            sock.close()
            return result == 0
        except socket.error:
            return False
    
    def _flush_status_queue(self):
        """Try to send all buffered status updates."""
        if not self.status_queue:
            return
        
        # Only try to flush if we have authentication
        if not self.token:
            logger.debug(f"📦 {len(self.status_queue)} updates buffered (no auth token)")
            return
        
        logger.info(f"🔄 Attempting to flush {len(self.status_queue)} buffered status updates...")
        
        failed_updates = []
        for payload in self.status_queue:
            try:
                # Use existing token, don't retry auth here
                response = requests.post(
                    f"{self.api_base_url}/api/devices/status",
                    headers=self._headers(),
                    json=payload,
                    timeout=10
                )
                
                if response.status_code != 200:
                    failed_updates.append(payload)
                else:
                    logger.info(f"✅ Flushed buffered update for device {payload['device_id']}")
                    
            except:
                failed_updates.append(payload)
        
        # Keep only failed updates
        self.status_queue = failed_updates
        self._save_queues()
        
        if not failed_updates:
            logger.info("✅ All buffered updates sent successfully!")
        else:
            logger.warning(f"⚠️ {len(failed_updates)} updates still buffered")
    
    def update_device_status(self, device_id: int, result: PingResult, reliability_score: float) -> bool:
        """
        Send device status update to API with enhanced statistical payload.
        """
        payload = {
            "device_id": device_id,
            "status": result.status,
            "response_time": result.response_time,
            "packet_loss": result.packet_loss,
            "checked_at": now_jakarta().isoformat(),  # This will send as local Jakarta time
            # Enhanced metrics
            "response_time_min": result.response_time_min,
            "response_time_max": result.response_time_max,
            "response_time_median": result.response_time_median,
            "response_time_std_dev": result.response_time_std_dev,
            "jitter": result.jitter,
            "reliability_score": reliability_score,
            "confidence_score": result.confidence
        }
        
        # Check API availability
        if not self.api_available and not self._check_api_health():
            logger.warning("⚠️ API unreachable - buffering status update")
            self.status_queue.append(payload)
            self._save_queues()
            self.offline_mode = True
            return False
        
        # Try to send current update
        if not self.ensure_authenticated():
            logger.warning("⚠️ Cannot authenticate - buffering status update")
            self.status_queue.append(payload)
            self._save_queues()
            return False
        
        try:
            response = requests.post(
                f"{self.api_base_url}/api/devices/status",
                headers=self._headers(),
                json=payload,
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                alert_info = data.get("data", {})
                
                # Log alert status
                if alert_info.get("alert_created"):
                    logger.warning(f"🚨 ALERT CREATED: Device {device_id} is DOWN!")
                elif alert_info.get("alert_resolved"):
                    logger.info(f"✅ Alert resolved: Device {device_id} is back UP")
                
                # Mark API as available
                if self.offline_mode:
                    logger.info("🌐 API connection restored!")
                    self.offline_mode = False
                self.api_available = True
                
                return True
                
            elif response.status_code == 401:
                # Token expired, retry once
                if self.login():
                    return self.update_device_status(device_id, result, reliability_score)
                else:
                    # Buffer if can't authenticate
                    self.status_queue.append(payload)
                    self._save_queues()
                    return False
            else:
                logger.error(f"❌ Status update failed: {response.text}")
                self.status_queue.append(payload)
                self._save_queues()
                return False
                
        except requests.exceptions.RequestException as e:
            logger.warning(f"⚠️ Network error - buffering update: {e}")
            self.api_available = False
            self.offline_mode = True
            self.status_queue.append(payload)
            self._save_queues()
            return False
    
    def _create_alert_via_api(self, device_id: int, device_name: str, alert_type: str, 
                              severity: str, message: str, metadata: Optional[Dict] = None):
        """Create alert directly via API endpoint."""
        if not self.ensure_authenticated():
            logger.warning(f"Cannot create alert - not authenticated")
            return
        
        payload = {
            "device_id": device_id,
            "message": message,
            "severity": severity,
            "status": "active",
            "metadata": json.dumps(metadata or {})
        }
        
        try:
            response = requests.post(
                f"{self.api_base_url}/api/alerts",
                headers=self._headers(),
                json=payload,
                timeout=10
            )
            
            if response.status_code in [200, 201]:
                logger.info(f"✅ Alert created via API: {alert_type} - {severity}")
            else:
                logger.warning(f"⚠️ Alert creation failed: {response.status_code} - {response.text}")
                
        except requests.exceptions.RequestException as e:
            logger.warning(f"⚠️ Error creating alert: {e}")
    
    def check_device(self, device: Dict) -> None:
        """Check a single device with sophisticated alert logic and statistical verification."""
        device_id = device.get("id")
        ip_address = device.get("ip_address")
        port = device.get("port")
        name = device.get("name", "Unknown")
        hierarchy = device.get("hierarchy_level", "device")
        
        logger.info(f"🔍 Checking {name} ({ip_address})...")
        
        # Check if parent is down (hierarchical check)
        parent_id = device.get("parent_id")
        if parent_id:
            parent = next((d for d in self.devices if d.get("id") == parent_id), None)
            # Find parent state in local tracking as reliable source
            parent_state = self.device_states.get(parent_id, {})
            parent_status = parent_state.get('status', parent.get('status') if parent else 'unknown')
            
            if parent_status == "down":
                logger.info(f"  ⏭️ Skipping - parent device is down")
                # Construct a logical down result
                logical_down = PingResult(
                    status="down", response_time=None, response_time_min=None, 
                    response_time_max=None, response_time_median=None, 
                    response_time_std_dev=None, jitter=None, packet_loss=100.0,
                    successful_pings=0, total_pings=PING_VERIFICATION_COUNT, confidence=100
                )
                self.update_device_status(device_id, logical_down, 0.0)
                return
        
        # Ping the device with advanced verification
        result = self.ping_device_advanced(ip_address)
        
        # Get previous state
        prev_state = self.device_states.get(device_id, {
            'status': 'unknown',
            'last_response_time': None,
            'response_time_history': [],
            'consecutive_failures': 0,
            'consecutive_successes': 0,
            'baseline_response_time': None,
            'reliability_score': 100.0
        })
        
        prev_status = prev_state.get('status', 'unknown')
        consecutive_failures = prev_state.get('consecutive_failures', 0)
        consecutive_successes = prev_state.get('consecutive_successes', 0)
        response_time_history = prev_state.get('response_time_history', [])
        
        # Update response time history
        if result.response_time is not None:
            response_time_history.append(result.response_time)
            # Keep only last N records
            if len(response_time_history) > STATISTICAL_WINDOW_SIZE:
                response_time_history.pop(0)
        
        # Calculate statistics
        baseline = self.stats.calculate_baseline(response_time_history)
        std_dev = self.stats.calculate_std_dev(response_time_history)
        
        # Determine logical status (with confirmation threshold)
        if result.status == 'down' or result.response_time is None:
            consecutive_failures += 1
            consecutive_successes = 0
            logical_status = 'down' if consecutive_failures >= DOWN_CONFIRMATION_REQUIRED else prev_status
            if logical_status == 'unknown': logical_status = 'down'
        else:
            consecutive_successes += 1
            consecutive_failures = 0
            # Require confirmation for recovery if previously down
            if prev_status == 'down':
                logical_status = 'up' if consecutive_successes >= UP_CONFIRMATION_REQUIRED else 'down'
            else:
                logical_status = 'up'
        
        # Calculate reliability score
        reliability_score = self.stats.calculate_reliability_score(
            consecutive_successes, consecutive_failures, result.packet_loss, result.jitter
        )
        
        # ============================================
        # SOPHISTICATED ALERT LOGIC
        # ============================================
        
        # CASE 1: Device went DOWN (confirmed)
        if logical_status == 'down' and prev_status != 'down':
            # Determine severity based on hierarchy
            severity_map = {'utama': 'critical', 'sub': 'high', 'device': 'medium'}
            severity = severity_map.get(hierarchy, 'medium')
            
            logger.warning(f"🚨 ALERT: Device {name} confirmed DOWN after {consecutive_failures} checks")
            
            # Customize message for No Response vs specific error
            error_reason = "No Response / Request Timed Out"
            if result.packet_loss < 100 and result.status == 'down':
                error_reason = f"High Packet Loss ({result.packet_loss}%)"
            
            self._create_alert_via_api(
                device_id=device_id,
                device_name=name,
                alert_type='device_down',
                severity=severity,
                message=f"Device '{name}' ({ip_address}) is DOWN - {error_reason}. Confirmed after {consecutive_failures} failures.",
                metadata={
                    'ip_address': ip_address,
                    'previous_status': prev_status,
                    'hierarchy': hierarchy,
                    'packet_loss': result.packet_loss,
                    'reason': error_reason
                }
            )
        
        # CASE 2: Device RECOVERED (confirmed)
        elif logical_status == 'up' and prev_status == 'down':
            logger.info(f"✅ RECOVERY: Device {name} is back online")
            
            self._create_alert_via_api(
                device_id=device_id,
                device_name=name,
                alert_type='device_recovery',
                severity='info',
                message=f"Device '{name}' ({ip_address}) has RECOVERED and is now online",
                metadata={
                    'ip_address': ip_address,
                    'response_time': result.response_time,
                    'downtime_checks': consecutive_failures
                }
            )
        
        # CASE 3: Performance Degradation (High Latency)
        elif logical_status == 'up' and result.response_time is not None:
            # Check for high latency using statistical or static threshold
            is_high_latency = False
            threshold_used = self.response_time_threshold
            
            if self.use_statistical_threshold and baseline and std_dev:
                # Use Z-score anomaly detection
                is_anomaly, z_score = self.stats.is_anomaly(result.response_time, baseline, std_dev, threshold=3.0)
                if is_anomaly and result.response_time > self.response_time_threshold:
                    is_high_latency = True
                    threshold_used = f"baseline({baseline}ms) + 3σ({round(std_dev*3,1)}ms)"
            else:
                # Use static threshold
                if result.response_time and result.response_time > self.response_time_threshold:
                    is_high_latency = True
            
            if is_high_latency:
                logger.warning(f"⚠️ HIGH LATENCY: Device {name} - {result.response_time}ms")
                
                # Only alert if not recently alerted for this (avoid spam)
                # Ideally we track last_alert_time in state, but simplified here
                percentage_over = 0
                if isinstance(threshold_used, (int, float)):
                    percentage_over = round(((result.response_time - threshold_used) / threshold_used) * 100, 2)
                
                self._create_alert_via_api(
                    device_id=device_id,
                    device_name=name,
                    alert_type='high_response_time',
                    severity='low',
                    message=f"High latency: {result.response_time}ms (Threshold: {threshold_used})",
                    metadata={
                        'current': result.response_time,
                        'threshold': str(threshold_used),
                        'jitter': result.jitter
                    }
                )

        # Update device state for next cycle
        self.device_states[device_id] = {
            'status': logical_status,
            'last_response_time': result.response_time,
            'response_time_history': response_time_history,
            'consecutive_failures': consecutive_failures,
            'consecutive_successes': consecutive_successes,
            'baseline_response_time': baseline,
            'response_time_std_dev': std_dev,
            'reliability_score': reliability_score
        }
        
        # Save states to disk
        self._save_queues()
        
        # Log result
        status_icon = "✅" if logical_status == "up" else "❌"
        if prev_status != logical_status: status_icon = "🔄 " + status_icon
        
        response_str = f"{result.response_time}ms" if result.response_time is not None else "N/A"
        logger.info(f"  {status_icon} {logical_status.upper()} - Response: {response_str}, Loss: {result.packet_loss}%, Score: {reliability_score}")
        
        # Update API with current status
        self.update_device_status(device_id, result, reliability_score)
    

    
def main():
    """Main monitoring loop - Real-time (30 seconds interval)."""
    logger.info("🚀 Starting NetMonitor Real-time Monitoring Service...")
    logger.info(f"📡 API URL: {API_BASE_URL}")
    logger.info(f"⏱️ Check interval: {MONITOR_INTERVAL} seconds (REAL-TIME)")
    logger.info(f"🕒 Timezone: GMT+7 (Asia/Jakarta)")
    
    monitor = NetworkMonitor()
    
    # Initial login attempt (don't exit if fails)
    if not monitor.login():
        logger.warning("⚠️ Initial login failed - will continue in offline mode")
        logger.info("🔄 Script will attempt to reconnect and fetch devices on each cycle")
    
    try:
        while True:
            logger.info("=" * 50)
            jakarta_time = TimeManager.format_timestamp(TimeManager.get_current_time(), '%Y-%m-%d %H:%M:%S %Z')
            logger.info(f"🔄 Starting monitoring cycle at {jakarta_time}")
            logger.info("=" * 50)
            
            # Attempt to fetch devices (will retry auth if needed)
            devices_available = monitor.fetch_devices()
            
            if not devices_available:
                logger.warning("⚠️ No devices available to monitor - waiting for API/cache")
                logger.info(f"💤 Retrying in {MONITOR_INTERVAL} seconds...")
                time.sleep(MONITOR_INTERVAL)
                continue
            
            # Check each device
            logger.info(f"📊 Monitoring {len(monitor.devices)} devices...")
            checked_count = 0
            for device in monitor.devices:
                monitor.check_device(device)
                checked_count += 1
            
            logger.info(f"✅ Monitoring cycle completed - checked {checked_count} devices")
            logger.info(f"💤 Next check in {MONITOR_INTERVAL} seconds...")
            time.sleep(MONITOR_INTERVAL)
            
    except KeyboardInterrupt:
        logger.info("👋 Received interrupt signal. Shutting down...")
        sys.exit(0)
    except Exception as e:
        logger.error(f"💥 Fatal error: {e}", exc_info=True)
        logger.info(f"🔄 Attempting to restart in {MONITOR_INTERVAL} seconds...")
        time.sleep(MONITOR_INTERVAL)
        # Recursive restart (could also be a while loop)
        main()


if __name__ == "__main__":
    main()
