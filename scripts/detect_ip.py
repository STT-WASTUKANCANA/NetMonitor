#!/usr/bin/env python3
"""
Network Device Detector for NetMonitor
Enhanced with streaming output, MAC detection, and real-time progress reporting.
"""

import argparse
import subprocess
import ipaddress
import json
import socket
import os
import sys
import re
import concurrent.futures
from datetime import datetime
from typing import List, Dict, Optional, Callable

# Add project root and scripts to path for imports
script_dir = os.path.dirname(os.path.abspath(__file__))
sys.path.append(os.path.dirname(script_dir))  # Project root
sys.path.append(script_dir)  # Scripts directory

from utils.mac_vendor import MACVendorLookup
from utils.device_classifier import DeviceClassifier
from dotenv import load_dotenv
import requests

# Load environment variables
# Load environment variables
# Check multiple locations for .env
possible_env_paths = [
    os.path.join(script_dir, '.env'),
    os.path.join(os.path.dirname(script_dir), '.env')
]

env_path = None
for path in possible_env_paths:
    if os.path.exists(path):
        env_path = path
        break

if env_path:
    load_dotenv(env_path)

API_EMAIL = os.getenv("API_EMAIL", "admin@monitor.local")
API_PASSWORD = os.getenv("API_PASSWORD", "password")
API_BASE_URL = os.getenv("API_BASE_URL", "http://localhost:8000")


class IPDetector:
    def __init__(
        self,
        timeout: float = 0.6,
        max_workers: int = 40,
        network_override: Optional[str] = None,
        quiet: bool = False,
        json_only: bool = False,
        stream: bool = False,
        save_db: bool = False,
        progress_callback: Optional[Callable] = None,
    ):
        self.timeout = timeout
        self.max_workers = max_workers
        self.quiet = quiet
        self.json_only = json_only
        self.stream = stream  # Output devices as they're found (JSON Lines)
        self.save_db = save_db
        self.progress_callback = progress_callback
        self.network_range = network_override or self.detect_network_range()
        self.gateway_ip = self.get_default_gateway()
        self.found_devices = []
        self.arp_cache = {}
        self.mac_cache = {}
        self.scanned_count = 0
        self.total_hosts = 0
        
        self.mac_cache = {}
        self.scanned_count = 0
        self.total_hosts = 0
        
        # Initialize enhanced detection utilities
        self.mac_vendor_lookup = MACVendorLookup()
        self.device_classifier = DeviceClassifier(gateway_ip=self.gateway_ip)
        
        # Pre-populate ARP cache for faster lookups
        if not self.json_only:
            self._pre_populate_arp_cache()
        self._load_mac_cache()

    def log(self, message: str):
        """Print log message only if not in quiet or json-only mode."""
        if not self.quiet and not self.json_only and not self.stream:
            print(message, file=sys.stderr)

    def emit_progress(self, scanned: int, total: int, found: int):
        """Emit progress update in streaming mode."""
        if self.stream:
            progress_data = {
                "type": "progress",
                "scanned": scanned,
                "total": total,
                "found": found,
                "percentage": round((scanned / total) * 100, 1) if total > 0 else 0
            }
            print(json.dumps(progress_data), flush=True)
        if self.progress_callback:
            self.progress_callback(scanned, total, found)

    def emit_device(self, device: Dict):
        """Emit a discovered device in streaming mode."""
        if self.stream:
            device_data = {
                "type": "device",
                "data": device
            }
            print(json.dumps(device_data), flush=True)

    def detect_network_range(self) -> Optional[str]:
        """Automatically detect current network range using multiple methods."""
        methods = [
            self._detect_network_method1,
            self._detect_network_method2,
            self._detect_network_method3
        ]
        
        for method in methods:
            try:
                network = method()
                if network:
                    self.log(f"[+] Detected network range: {network}")
                    return network
            except Exception as e:
                self.log(f"[!] Method failed: {e}")
        
        self.log("[!] Failed to detect network range")
        return None

    def _detect_network_method1(self) -> Optional[str]:
        """Method 1: Using hostname command"""
        try:
            local_ip_result = subprocess.check_output(
                "hostname -I", shell=True, text=True, timeout=5
            ).strip()
            if local_ip_result:
                local_ip = local_ip_result.split()[0]
                ip_parts = local_ip.split('.')
                if len(ip_parts) >= 3:
                    network = f"{ip_parts[0]}.{ip_parts[1]}.{ip_parts[2]}.0/24"
                    return network
        except Exception:
            pass
        return None

    def _detect_network_method2(self) -> Optional[str]:
        """Method 2: Using ip route command"""
        try:
            route_output = subprocess.check_output(
                "ip route | grep -E 'proto kernel|scope link' | grep -v 'lo' | head -n 1", 
                shell=True, text=True, timeout=5
            ).strip()
            
            if route_output and 'dev' in route_output:
                parts = route_output.split()
                for i, part in enumerate(parts):
                    if part == 'dev':
                        for j in range(i+2, len(parts)):
                            potential_network = parts[j]
                            if '/' in potential_network and '.' in potential_network:
                                return potential_network
        except Exception:
            pass
        return None

    def _detect_network_method3(self) -> Optional[str]:
        """Method 3: Using socket to determine local IP"""
        try:
            with socket.socket(socket.AF_INET, socket.SOCK_DGRAM) as s:
                s.connect(("8.8.8.8", 80))
                local_ip = s.getsockname()[0]
                
            ip_parts = local_ip.split('.')
            if len(ip_parts) >= 3:
                network = f"{ip_parts[0]}.{ip_parts[1]}.{ip_parts[2]}.0/24"
                self.log(f"[+] Determined local IP: {local_ip}")
                return network
        except Exception:
            pass
        return None

    def get_default_gateway(self) -> Optional[str]:
        """Detect default gateway IP address."""
        methods = [self._get_gateway_method1, self._get_gateway_method2]
        
        for method in methods:
            try:
                gateway_ip = method()
                if gateway_ip:
                    self.log(f"[+] Default gateway detected: {gateway_ip}")
                    return gateway_ip
            except Exception:
                pass
        
        self.log("[!] Could not detect default gateway")
        return None
    
    def _get_gateway_method1(self) -> Optional[str]:
        """Method 1: Using ip route command"""
        try:
            output = subprocess.check_output(
                "ip route | grep default | awk '{print $3}' | head -n 1", 
                shell=True, text=True, timeout=5
            ).strip()
            if output:
                return output
        except Exception:
            pass
        return None

    def _get_gateway_method2(self) -> Optional[str]:
        """Method 2: Using route command"""
        try:
            output = subprocess.check_output(
                "route -n | grep 'UG[ \\t]' | awk '{print $2}' | head -n 1", 
                shell=True, text=True, timeout=5
            ).strip()
            if output:
                return output
        except Exception:
            pass
        return None

    def _load_mac_cache(self):
        """Load MAC addresses from ARP table."""
        try:
            output = subprocess.check_output(
                "arp -an 2>/dev/null || ip neigh show 2>/dev/null", 
                shell=True, text=True, timeout=5
            )
            
            # Parse ARP output for MAC addresses
            for line in output.splitlines():
                # Format: ? (192.168.1.1) at aa:bb:cc:dd:ee:ff [ether] on eth0
                # Or: 192.168.1.1 dev eth0 lladdr aa:bb:cc:dd:ee:ff REACHABLE
                mac_match = re.search(r'([0-9a-fA-F:]{17}|[0-9a-fA-F-]{17})', line)
                ip_match = re.search(r'(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})', line)
                
                if mac_match and ip_match:
                    mac = mac_match.group(1).upper().replace('-', ':')
                    ip = ip_match.group(1)
                    self.mac_cache[ip] = mac
        except Exception:
            pass

    def _pre_populate_arp_cache(self):
        """Pre-populate ARP cache."""
        # ARP cache is now populated via _load_mac_cache
        pass

    def get_mac_address(self, ip: str) -> Optional[str]:
        """Get MAC address for an IP from cache or fresh lookup."""
        if ip in self.mac_cache:
            return self.mac_cache[ip]
        
        # Try fresh ARP lookup
        try:
            # Ping first to populate ARP table
            subprocess.run(['ping', '-c', '1', '-W', '1', ip], 
                          capture_output=True, timeout=2)
            
            output = subprocess.check_output(
                f"arp -n {ip} 2>/dev/null | grep -v incomplete", 
                shell=True, text=True, timeout=2
            )
            
            mac_match = re.search(r'([0-9a-fA-F:]{17})', output)
            if mac_match:
                mac = mac_match.group(1).upper()
                self.mac_cache[ip] = mac
                return mac
        except Exception:
            pass
        
        return None

    def ping_host(self, ip: str) -> Dict:
        """Ping a specific IP address to check connectivity."""
        try:
            status = 'inactive'
            response_time = None

            # Try fping first (faster)
            try:
                result = subprocess.run(
                    ['fping', '-c', '1', '-t', f'{int(self.timeout*1000)}', ip], 
                    capture_output=True, text=True, timeout=self.timeout + 1
                )
                if result.returncode == 0:
                    status = 'active'
                    # Parse response time
                    match = re.search(r'(\d+\.?\d*)\s*ms', result.stdout + result.stderr)
                    if match:
                        response_time = float(match.group(1))
            except (subprocess.TimeoutExpired, FileNotFoundError):
                # Fallback to standard ping
                return self._ping_fallback(ip)

            return {'ip': ip, 'status': status, 'response_time': response_time}

        except Exception:
            return {'ip': ip, 'status': 'error', 'response_time': None}

    def _ping_fallback(self, ip: str) -> Dict:
        """Fallback ping method using regular ping command."""
        try:
            command = ['ping', '-c', '1', '-W', str(int(self.timeout)), ip]
            result = subprocess.run(command, capture_output=True, text=True, timeout=self.timeout + 1)

            if result.returncode == 0:
                response_time = None
                for line in result.stdout.split('\n'):
                    if 'time=' in line:
                        try:
                            time_str = line.split('time=')[1].split()[0]
                            if 'ms' in time_str:
                                time_str = time_str.replace('ms', '')
                            response_time = float(time_str)
                            break
                        except Exception:
                            pass
                return {'ip': ip, 'status': 'active', 'response_time': response_time}
            else:
                return {'ip': ip, 'status': 'inactive', 'response_time': None}
        except Exception:
            return {'ip': ip, 'status': 'error', 'response_time': None}

    def get_device_info(self, ip: str) -> Dict:
        """Get hostname, open ports, MAC address, and other details using enhanced logic."""
        device_info = {
            'ip': ip, 
            'hostname': 'Unknown', 
            'ports': [], 
            'mac_address': None,
            'vendor': None
        }

        # Common ports to scan
        common_ports = [
            22, 80, 443, 8080, 9000, 3389, 53, 139, 445, 135, 1900, 
            5900, 32400, 161, 23, 21, 554, 37777, 8883, 1883, 548, 2049,
            9100, 631, 515, 5060, 2000, 5000, 5001, 8123, 8443
        ]
        open_ports = []
        
        # Scan ports
        for port in common_ports:
            try:
                with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
                    sock.settimeout(0.2)
                    result = sock.connect_ex((ip, port))
                    if result == 0:
                        open_ports.append(port)
            except Exception:
                pass

        device_info['ports'] = open_ports
        device_info['hostname'] = self._get_hostname(ip, open_ports)
        device_info['mac_address'] = self.get_mac_address(ip)
        
        # Get Vendor
        if device_info['mac_address']:
            device_info['vendor'] = self.mac_vendor_lookup.lookup(device_info['mac_address'])
        
        # Enhanced Classification
        classification = self.device_classifier.classify(
            ip=ip,
            ports=open_ports,
            mac_vendor=device_info['vendor'],
            hostname=device_info['hostname'],
            is_gateway=(ip == self.gateway_ip)
        )
        
        device_info['hierarchy_level'] = classification.hierarchy
        device_info['type'] = classification.device_type
        device_info['classification_confidence'] = classification.confidence
        device_info['connection_type'] = self._determine_connection_type(ip)
        
        # Refine hostname if generic
        if device_info['hostname'].startswith('Device-') or device_info['hostname'] == 'Unknown':
             # Try to generate better name from vendor/type
             if device_info['vendor']:
                 short_vendor = device_info['vendor'].split()[0]
                 device_info['hostname'] = f"{short_vendor}-{classification.device_type.title()}-{ip.split('.')[-1]}"

        return device_info

    def _determine_connection_type(self, ip: str) -> str:
        """Determine if connection is local (LAN) or public (WAN)."""
        try:
            ip_obj = ipaddress.ip_address(ip)
            if ip_obj.is_private:
                return 'local'
            return 'public'
        except Exception:
            return 'local'

    def _get_hostname(self, ip: str, ports: List[int] = None) -> str:
        """Get hostname using multiple methods."""
        if ports is None:
            ports = []

        # 1. Reverse DNS
        hostname = self._reverse_dns_lookup(ip)
        if hostname and hostname != ip and not hostname.startswith('Unknown'):
            return hostname

        # 2. NetBIOS lookup (Windows)
        hostname = self._get_hostname_from_nmblookup(ip)
        if hostname and hostname != ip and not hostname.startswith('Unknown'):
            return hostname

        # 3. Guess based on ports is now handled by DeviceClassifier refinement
        # But we return a placeholder here if needed
        return f"Device-{ip.split('.')[-1]}"




    def _reverse_dns_lookup(self, ip: str) -> str:
        try:
            return socket.gethostbyaddr(ip)[0]
        except Exception:
            return 'Unknown'

    def _get_hostname_from_nmblookup(self, ip: str) -> str:
        try:
            result = subprocess.run(['nmblookup', '-A', ip], capture_output=True, text=True, timeout=2)
            if result.returncode == 0:
                for line in result.stdout.split('\n'):
                    if '<00>' in line and 'GROUP' not in line:
                        parts = line.strip().split()
                        for i, part in enumerate(parts):
                            if '<00>' in part and i > 0:
                                return parts[i-1]
        except Exception:
            pass
        return 'Unknown'



    def generate_description(self, device: Dict, response_time: Optional[float]) -> str:
        """Generate detailed description for the device."""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M')
        parts = [f"Auto-detected on {timestamp}"]
        
        if device.get('vendor'):
            parts.append(f"Vendor: {device['vendor']}")
            
        if device.get('ports'):
            ports_str = ', '.join(map(str, device['ports']))
            parts.append(f"Open ports: {ports_str}")
        
        if device.get('mac_address'):
            parts.append(f"MAC: {device['mac_address']}")
        
        if response_time:
            parts.append(f"Response: {response_time}ms")
        
        parts.append(f"Type: {device.get('type', 'other')}")
        
        if device.get('classification_confidence'):
            parts.append(f"Confidence: {device['classification_confidence']}%")
        
        return ' | '.join(parts)

    def scan_single_ip(self, ip: str) -> Optional[Dict]:
        """Scan a single IP."""
        status = self.ping_host(ip)
        if status['status'] == 'active':
            info = self.get_device_info(ip)
            device = {
                'name': info['hostname'],
                'ip_address': ip,
                'type': info['type'],
                'hierarchy_level': info['hierarchy_level'],
                'connection_type': info['connection_type'],
                'location': 'Auto Detected',
                'status': 'up',
                'response_time': status['response_time'],
                'mac_address': info.get('mac_address'),
                'vendor': info.get('vendor'),
                'open_ports': info['ports'],
                'description': self.generate_description(info, status['response_time']),
                'detected_at': datetime.now().isoformat()
            }
            return device
        return None

    def scan_network(self) -> List[Dict]:
        """Scan detected network range."""
        if not self.network_range:
            self.log("[!] No valid network range found.")
            if self.stream:
                print(json.dumps({"type": "error", "message": "No valid network range found"}), flush=True)
            return []

        self.log(f"[+] Scanning {self.network_range} with {self.max_workers} threads...")
        
        try:
            network = ipaddress.IPv4Network(self.network_range, strict=False)
        except ValueError as e:
            self.log(f"[!] Invalid network range: {e}")
            return []

        # Limit to /24 if too big
        if network.num_addresses > 1024:
            network = ipaddress.IPv4Network(f"{network.network_address}/24", strict=False)

        hosts = list(network.hosts())
        self.total_hosts = len(hosts)
        self.scanned_count = 0

        # Emit initial progress
        if self.stream:
            print(json.dumps({
                "type": "start",
                "network_range": self.network_range,
                "total_hosts": self.total_hosts,
                "gateway": self.gateway_ip,
                "timestamp": datetime.now().isoformat()
            }), flush=True)

        active_devices = []
        
        with concurrent.futures.ThreadPoolExecutor(max_workers=self.max_workers) as executor:
            future_to_ip = {executor.submit(self.scan_single_ip, str(ip)): str(ip) for ip in hosts}
            
            for future in concurrent.futures.as_completed(future_to_ip):
                self.scanned_count += 1
                
                try:
                    dev = future.result()
                    if dev:
                        active_devices.append(dev)
                        self.log(f"   > Found: {dev['ip_address']} ({dev['name']})")
                        self.emit_device(dev)
                except Exception:
                    pass
                
                # Emit progress every 10 hosts or on completion
                if self.scanned_count % 10 == 0 or self.scanned_count == self.total_hosts:
                    self.emit_progress(self.scanned_count, self.total_hosts, len(active_devices))
        
        return active_devices

    def output_json(self, devices: List[Dict]):
        """Output final results as JSON."""
        output = {
            "success": True,
            "timestamp": datetime.now().isoformat(),
            "network_range": self.network_range,
            "gateway": self.gateway_ip,
            "total_scanned": self.total_hosts,
            "devices_found": len(devices),
            "devices": devices
        }
        
        if self.stream:
            # Emit completion event in streaming mode
            completion = {
                "type": "complete",
                "total_scanned": self.total_hosts,
                "devices_found": len(devices),
                "timestamp": datetime.now().isoformat()
            }
            print(json.dumps(completion), flush=True)
        else:
            # Output full JSON
            print(json.dumps(output, indent=2 if not self.json_only else None))

    def save_results_to_db(self, devices: List[Dict]) -> bool:
        """Save discovered devices to database via API."""
        """Save discovered devices to database via API."""
        # FILTER: Only save 'utama' and 'sub' devices. Ignore 'device' level (end user devices).
        filtered_devices = [
            d for d in devices 
            if d.get('hierarchy_level') in ['utama', 'sub']
        ]
        
        if not filtered_devices:
            self.log(f"[!] No infrastructure devices (utama/sub) found to save. (Ignored {len(devices)} end-user devices)")
            return False
            
        self.log(f"[+] Attempting to save {len(filtered_devices)} infrastructure devices to database (filtered from {len(devices)} total)...")
        
        try:
            # 1. Login to get token
            auth_response = requests.post(
                f"{API_BASE_URL}/api/auth/login",
                json={"email": API_EMAIL, "password": API_PASSWORD},
                timeout=10
            )
            
            if auth_response.status_code != 200:
                self.log(f"[!] Login failed: {auth_response.text}")
                return False
                
            token = auth_response.json()["data"]["token"]
            
            # 2. Save devices
            headers = {
                "Authorization": f"Bearer {token}",
                "Content-Type": "application/json"
            }
            
            save_response = requests.post(
                f"{API_BASE_URL}/api/devices/discover/save",
                json=filtered_devices,
                headers=headers,
                timeout=30
            )
            
            if save_response.status_code == 200:
                result = save_response.json()
                self.log(f"[+] Successfully saved devices: {result['message']}")
                if self.stream:
                    print(json.dumps({
                        "type": "save_status",
                        "success": True,
                        "saved": result["data"]["saved"],
                        "skipped": result["data"]["skipped"]
                    }), flush=True)
                return True
            else:
                self.log(f"[!] Save failed ({save_response.status_code}): {save_response.text}")
                if self.stream:
                    print(json.dumps({"type": "save_status", "success": False, "error": save_response.text}), flush=True)
                return False
                
        except Exception as e:
            self.log(f"[!] API Error: {str(e)}")
            if self.stream:
                print(json.dumps({"type": "save_status", "success": False, "error": str(e)}), flush=True)
            return False



def main():
    parser = argparse.ArgumentParser(description="Network Device Detector for NetMonitor")
    parser.add_argument("--json", action="store_true", help="Output only JSON for machine parsing")
    parser.add_argument("--stream", action="store_true", help="Stream devices as JSON Lines for real-time parsing")
    parser.add_argument("--range", type=str, help="Specific network range (e.g., 192.168.1.0/24)")
    parser.add_argument("--timeout", type=float, default=0.6, help="Timeout per host in seconds")
    parser.add_argument("--workers", type=int, default=40, help="Number of concurrent workers")
    parser.add_argument("--save-db", action="store_true", help="Automatically save results to database")
    parser.add_argument("--quiet", action="store_true", help="Suppress non-essential output")
    args = parser.parse_args()

    detector = IPDetector(
        timeout=args.timeout,
        max_workers=args.workers,
        network_override=args.range,
        json_only=args.json,
        stream=args.stream,
        save_db=args.save_db,
        quiet=args.quiet
    )
    
    devices = detector.scan_network()
    
    if args.save_db:
        detector.save_results_to_db(devices)
    
    if args.json or args.stream:
        detector.output_json(devices)
    else:
        # Human readable report
        print("\n" + "="*60)
        print(f"Scan Complete. Found {len(devices)} devices in {detector.network_range}")
        print("="*60)
        for d in devices:
            mac_info = f" | MAC: {d['mac_address']}" if d.get('mac_address') else ""
            ports_info = f" | Ports: {d['open_ports']}" if d.get('open_ports') else ""
            print(f"[{d['status'].upper()}] {d['ip_address']} - {d['name']} ({d['type']}){mac_info}{ports_info}")


if __name__ == "__main__":
    main()
