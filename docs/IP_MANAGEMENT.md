# Network Monitoring System - IP Address Management

## Getting IP Addresses for Network Devices

This document explains how to properly obtain IP addresses for network devices (main, sub, and regular devices) to ensure they are not misidentified as "unknown" by the system.

## Problem: Device Status "Unknown"

The NetMonitor system may display a device's status as "unknown" due to various reasons:

1. **Invalid or missing IP address** in the database
2. **Device is offline** or not responding to monitoring requests
3. **Network connectivity issues** between the monitoring system and the device
4. **Incorrect IP configuration** on the device

## Solution: Proper IP Address Acquisition

### 1. Manual IP Assignment

The most reliable method is to manually assign IP addresses based on your network architecture:

```bash
# Example network structure:
# - Main Device (Gateway/Router): 192.168.1.1
# - Sub Devices (Access Points, Switches): 192.168.1.2, 192.168.1.3, etc.
# - Regular Devices (Servers, Workstations): 192.168.1.10, 192.168.1.11, etc.
```

### 2. Network Scanning to Discover Active IPs

Use the included IP detection script to identify active devices on your network:

```bash
# First, make the script executable
chmod +x scripts/detect_ip.py

# Scan your network range (adjust subnet as needed)
python3 scripts/detect_ip.py --network 192.168.1.0/24

# Save results to a file
python3 scripts/detect_ip.py --network 192.168.1.0/24 --output detected_devices.json
```

### 3. Using the Laravel Command to Audit Devices

The system includes a command to audit existing devices and identify those with unknown IPs:

```bash
# Run the IP detection command
php artisan network:detect-ips

# Specify a different network range
php artisan network:detect-ips --network 10.0.0.0/24

# Adjust ping timeout
php artisan network:detect-ips --network 192.168.1.0/24 --timeout 3
```

### 4. Best Practices for IP Management

1. **Use Static IPs for Critical Devices**: Assign static IP addresses to main and sub devices to ensure consistent monitoring.

2. **Document Network Architecture**: Maintain a map of your network with IP assignments:
   ```
   Main Router: 192.168.1.1
   Sub Router 1: 192.168.1.2
   Sub Router 2: 192.168.1.3
   Switch 1: 192.168.1.10
   Server 1: 192.168.1.20
   ```

3. **DHCP Reservation**: For devices that require dynamic IPs, use DHCP reservation to ensure they always get the same IP address.

4. **Regular IP Audits**: Periodically run the IP detection command to identify and correct any IP address issues.

### 5. Troubleshooting Tips

If devices still appear as "unknown":

1. **Check IP Connectivity**: Verify that the monitoring system can reach the device via ping:
   ```bash
   ping <device_ip_address>
   ```

2. **Verify Device Status**: Ensure the device is powered on and connected to the network.

3. **Check Firewall Settings**: Some devices may have firewalls blocking ICMP ping requests.

4. **Validate Configuration**: Confirm that the IP address in the NetMonitor system matches the device's actual IP.

5. **Check Network Segmentation**: Ensure the monitoring system is on a network segment that can reach all monitored devices.

### 6. Adding Devices to the System

When adding new devices to NetMonitor:

1. Determine the device's IP address using network scanning or manual configuration
2. Verify the IP is reachable from the monitoring system
3. Add the device to NetMonitor with the correct IP address
4. Set the appropriate hierarchy level (utama, sub, device)
5. Verify the device appears as "up" in the monitoring dashboard

By following these practices, you can ensure that all network devices are properly identified and monitored, preventing the "unknown" status issue.