#!/usr/bin/env python3
"""
Network Monitoring Script for Laravel Monitoring System
This script checks device connectivity and reports status to the Laravel API.
"""

import requests
import subprocess
import time
import json
import os
import sys
from datetime import datetime
from typing import Dict, List, Optional

class NetworkMonitor:
    def __init__(self, api_base_url: str, api_token: Optional[str] = None):
        self.api_base_url = api_base_url.rstrip('/')
        self.headers = {
            'Content-Type': 'application/json',
            'User-Agent': 'NetworkMonitor/1.0'
        }
        
        if api_token:
            self.headers['Authorization'] = f'Bearer {api_token}'
    
    def get_devices(self) -> List[Dict]:
        """
        Fetch active devices from the Laravel API
        """
        try:
            response = requests.get(
                f"{self.api_base_url}/api/devices",
                headers=self.headers,
                timeout=30
            )
            
            if response.status_code == 200:
                return response.json()
            else:
                print(f"Failed to fetch devices: {response.status_code} - {response.text}")
                return []
        except requests.RequestException as e:
            print(f"Error fetching devices: {e}")
            return []
    
    def ping_device(self, ip_address: str, timeout: int = 2) -> Dict:
        """
        Ping a device to check connectivity
        """
        try:
            start_time = time.time()
            
            # Try ping command (Linux/Unix)
            command = ['ping', '-c', '1', '-W', str(timeout), ip_address]
            result = subprocess.run(
                command,
                capture_output=True,
                text=True,
                timeout=timeout + 1
            )
            
            if result.returncode == 0:
                end_time = time.time()
                response_time = round((end_time - start_time) * 1000, 2)  # Convert to milliseconds
                
                # Extract response time from ping output if available
                lines = result.stdout.strip().split('\n')
                for line in lines:
                    if 'time=' in line:
                        try:
                            time_part = line.split('time=')[1].split()[0]
                            response_time = float(time_part)
                            break
                        except:
                            pass
                
                return {
                    'status': 'up',
                    'response_time': response_time,
                    'message': 'Device responded to ping'
                }
            else:
                return {
                    'status': 'down',
                    'response_time': None,
                    'message': 'Device did not respond to ping'
                }
        except (subprocess.TimeoutExpired, subprocess.SubprocessError) as e:
            return {
                'status': 'down',
                'response_time': None,
                'message': f'Ping failed: {str(e)}'
            }
        except Exception as e:
            return {
                'status': 'down',
                'response_time': None,
                'message': f'Monitoring error: {str(e)}'
            }
    
    def check_port(self, ip_address: str, port: int = 80, timeout: int = 5) -> bool:
        """
        Check if a specific port is open on the device
        """
        try:
            import socket
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(timeout)
            result = sock.connect_ex((ip_address, port))
            sock.close()
            return result == 0
        except Exception:
            return False
    
    def check_device(self, device: Dict) -> Dict:
        """
        Check the status of a single device
        """
        ip_address = device.get('ip_address')
        device_id = device.get('id')
        device_name = device.get('name')
        
        if not ip_address or not device_id:
            return {
                'device_id': device_id,
                'status': 'unknown',
                'response_time': None,
                'message': 'Invalid device data'
            }
        
        print(f"Checking device {device_name} ({ip_address})...")
        
        # Perform connectivity checks
        ping_result = self.ping_device(ip_address)
        
        # If ping succeeds, we're done
        if ping_result['status'] == 'up':
            return {
                'device_id': device_id,
                **ping_result
            }
        
        # If ping fails, try a basic HTTP check
        try:
            http_start = time.time()
            response = requests.get(f"http://{ip_address}", timeout=5)
            http_end = time.time()
            
            if response.status_code < 500:  # Consider anything below 500 as "up"
                response_time = round((http_end - http_start) * 1000, 2)
                return {
                    'device_id': device_id,
                    'status': 'up',
                    'response_time': response_time,
                    'message': f'Device responded with HTTP {response.status_code}'
                }
        except requests.RequestException:
            pass
        
        # Device is down
        return {
            'device_id': device_id,
            'status': 'down',
            'response_time': None,
            'message': 'Device did not respond to ping or HTTP request'
        }
    
    def report_status(self, device_id: int, status: str, response_time: Optional[float], message: str) -> bool:
        """
        Report device status to the Laravel API
        """
        try:
            data = {
                'status': status,
                'response_time': response_time,
                'message': message
            }
            
            response = requests.post(
                f"{self.api_base_url}/api/devices/{device_id}/status",
                headers=self.headers,
                json=data,
                timeout=30
            )
            
            if response.status_code in [200, 201]:
                print(f"Status reported successfully for device {device_id}")
                return True
            else:
                print(f"Failed to report status for device {device_id}: {response.status_code} - {response.text}")
                return False
        except requests.RequestException as e:
            print(f"Error reporting status for device {device_id}: {e}")
            return False
    
    def run_monitoring_cycle(self) -> None:
        """
        Run a complete monitoring cycle
        """
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Starting monitoring cycle...")
        
        devices = self.get_devices()
        
        if not devices:
            print("No devices found to monitor")
            return
        
        print(f"Found {len(devices)} active devices to monitor")
        
        up_count = 0
        down_count = 0
        
        for device in devices:
            # Check device status
            result = self.check_device(device)
            
            # Report status to API
            success = self.report_status(
                result['device_id'],
                result['status'],
                result['response_time'],
                result['message']
            )
            
            if success:
                if result['status'] == 'up':
                    up_count += 1
                elif result['status'] == 'down':
                    down_count += 1
            
            # Small delay between checks to avoid overwhelming
            time.sleep(0.1)
        
        print(f"Monitoring cycle completed:")
        print(f"  - {up_count} devices UP")
        print(f"  - {down_count} devices DOWN")
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Cycle finished")

def main():
    # Configuration
    api_base_url = os.getenv('API_BASE_URL', 'http://localhost:8000')
    api_token = os.getenv('API_TOKEN', None)  # Optional API token
    
    # Create monitor instance
    monitor = NetworkMonitor(api_base_url=api_base_url, api_token=api_token)
    
    # Run monitoring
    try:
        monitor.run_monitoring_cycle()
    except KeyboardInterrupt:
        print("\nMonitoring interrupted by user")
        sys.exit(0)
    except Exception as e:
        print(f"Error during monitoring: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()