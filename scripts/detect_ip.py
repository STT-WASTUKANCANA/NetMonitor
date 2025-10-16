#!/usr/bin/env python3
"""
IP Detection Script for Network Monitoring System
This script helps detect and verify IP addresses of network devices.
"""

import subprocess
import ipaddress
import json
import requests
import os
import sys
from datetime import datetime
from typing import List, Dict, Optional
import argparse

class IPDetector:
    def __init__(self, network_range: str, timeout: int = 2):
        self.network_range = network_range
        self.timeout = timeout
        self.found_devices = []
        
    def ping_host(self, ip: str) -> Dict:
        """
        Ping a specific IP address to check connectivity
        """
        try:
            command = ['ping', '-c', '1', '-W', str(self.timeout), ip]
            result = subprocess.run(
                command,
                capture_output=True,
                text=True,
                timeout=self.timeout + 1
            )
            
            if result.returncode == 0:
                # Extract response time from ping output
                response_time = None
                for line in result.stdout.split('\n'):
                    if 'time=' in line:
                        try:
                            time_part = line.split('time=')[1].split()[0]
                            response_time = float(time_part)
                            break
                        except:
                            pass
                
                return {
                    'ip': ip,
                    'status': 'active',
                    'response_time': response_time,
                    'timestamp': datetime.now().isoformat()
                }
            else:
                return {
                    'ip': ip,
                    'status': 'inactive',
                    'response_time': None,
                    'timestamp': datetime.now().isoformat()
                }
        except (subprocess.TimeoutExpired, subprocess.SubprocessError) as e:
            return {
                'ip': ip,
                'status': 'error',
                'response_time': None,
                'message': str(e),
                'timestamp': datetime.now().isoformat()
            }
        except Exception as e:
            return {
                'ip': ip,
                'status': 'error',
                'response_time': None,
                'message': f'Unexpected error: {str(e)}',
                'timestamp': datetime.now().isoformat()
            }
    
    def scan_network(self) -> List[Dict]:
        """
        Scan network range to find active devices
        """
        print(f"Scanning network {self.network_range}...")
        
        try:
            network = ipaddress.IPv4Network(self.network_range, strict=False)
        except ValueError as e:
            print(f"Invalid network range: {e}")
            return []
        
        active_devices = []
        total_ips = len([str(ip) for ip in network])
        current = 0
        
        for ip in network:
            if str(ip).endswith('.0') or str(ip).endswith('.255'):
                continue  # Skip network and broadcast addresses
                
            current += 1
            status = self.ping_host(str(ip))
            
            if status['status'] == 'active':
                active_devices.append(status)
                
            # Progress indicator
            if current % 10 == 0 or current == total_ips:
                print(f"Progress: {current}/{total_ips} ({current*100//total_ips}%)")
        
        self.found_devices = active_devices
        return active_devices
    
    def get_device_info(self, ip: str) -> Dict:
        """
        Get additional information about the device at IP address
        """
        import socket
        
        device_info = {
            'ip': ip,
            'hostname': None,
            'ports': []
        }
        
        # Get hostname
        try:
            hostname = socket.gethostbyaddr(ip)[0]
            device_info['hostname'] = hostname
        except:
            device_info['hostname'] = 'Unknown'
        
        # Check common ports
        common_ports = [22, 80, 443, 8080, 9000]
        for port in common_ports:
            try:
                sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                sock.settimeout(1)
                result = sock.connect_ex((ip, port))
                if result == 0:
                    device_info['ports'].append(port)
                sock.close()
            except:
                pass
        
        return device_info
    
    def print_results(self):
        """
        Print formatted results
        """
        print(f"\nFound {len(self.found_devices)} active devices:")
        print("-" * 60)
        
        for device in self.found_devices:
            print(f"IP: {device['ip']}")
            print(f"Status: {device['status']}")
            if device['response_time']:
                print(f"Response Time: {device['response_time']}ms")
            print(f"Last Checked: {device['timestamp']}")
            
            # Get and show additional info
            additional_info = self.get_device_info(device['ip'])
            if additional_info['hostname']:
                print(f"Hostname: {additional_info['hostname']}")
            if additional_info['ports']:
                print(f"Open Ports: {additional_info['ports']}")
                
            print("-" * 60)

def main():
    parser = argparse.ArgumentParser(description="IP Detection Tool for Network Monitoring")
    parser.add_argument("--network", required=True, help="Network range to scan (e.g., 192.168.1.0/24)")
    parser.add_argument("--output", help="Output file to save results as JSON")
    parser.add_argument("--timeout", type=int, default=2, help="Timeout for ping (default: 2)")
    
    args = parser.parse_args()
    
    # Create detector instance
    detector = IPDetector(args.network, args.timeout)
    
    # Scan network
    devices = detector.scan_network()
    
    # Print results
    detector.print_results()
    
    # Output to file if specified
    if args.output:
        with open(args.output, 'w') as f:
            json.dump({
                'scan_results': devices,
                'network_range': args.network,
                'scan_timestamp': datetime.now().isoformat(),
                'total_active': len(devices)
            }, f, indent=2)
        print(f"\nResults saved to {args.output}")

if __name__ == "__main__":
    main()