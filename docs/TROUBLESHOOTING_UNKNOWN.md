# Troubleshooting: Device Status "Unknown" in NetMonitor

## Overview
This document provides guidance on resolving the issue of device status showing as "unknown" in the NetMonitor system.

## Understanding Status Types

The NetMonitor system uses three status types for network devices:
- **up**: Device is responding to monitoring requests
- **down**: Device is not responding to monitoring requests
- **unknown**: Device status is unknown due to various reasons

## Common Causes of "Unknown" Status

1. **Invalid IP Address**: The IP address stored in the database is incorrect or unreachable
2. **Network Connectivity Issues**: Monitoring server cannot reach the device
3. **Firewall Blocking**: Device is blocking ICMP ping requests
4. **Offline Device**: The device is powered off or disconnected from the network
5. **Configuration Issues**: Device is not properly configured for monitoring

## Solutions

### 1. Identify Devices with "Unknown" Status

First, let's identify which devices have unknown status:

```bash
# Using Laravel Tinker
php artisan tinker
>>> App\Models\Device::where('status', 'unknown')->get()

# Or check via database query
php artisan db:query "SELECT * FROM devices WHERE status = 'unknown'"
```

### 2. Verify IP Addresses

The most important step is to ensure all devices have correct IP addresses:

#### Method A: Manual Verification
1. Identify the actual IP address of each device using network tools
2. Update the database with correct IP addresses
3. Use the NetMonitor interface to update device information

#### Method B: Automated IP Detection
1. Use the newly created IP detection script:
   ```bash
   # Make the script executable
   chmod +x scripts/detect_ip.py
   
   # Scan your network (adjust subnet as needed)
   python3 scripts/detect_ip.py --network 192.168.1.0/24
   ```

2. Compare the detected IPs with what's in your NetMonitor database

### 3. Update Device Information

Once you have the correct IP addresses, update them in the system:

```bash
# Manually update a device via command line
php artisan tinker
>>> $device = App\Models\Device::find($id);
>>> $device->ip_address = '192.168.1.100';
>>> $device->save();
```

### 4. Test Device Connectivity

After updating IP addresses, test connectivity:

#### Using the new bulk ping endpoint:
```bash
# This will ping all devices and update their status
curl -X POST http://your-netmonitor-domain/api/devices/bulk-ping \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d "{}"
```

#### Using the individual ping endpoint:
```bash
# Test a specific device
curl -X POST http://your-netmonitor-domain/api/device/scan \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"device_id": 1}'
```

### 5. Run the Custom IP Detection Command

Use the new Laravel command to audit all devices:

```bash
# Run the IP detection command
php artisan network:detect-ips

# Specify a specific network range
php artisan network:detect-ips --network 10.0.0.0/24
```

### 6. Verify Status Updates

After performing the above steps, check if the "unknown" status issue has been resolved:

```bash
# Check the count of devices with various statuses
php artisan tinker
>>> App\Models\Device::groupBy('status')->selectRaw('status, count(*) as count')->get()
```

## Prevention Strategies

### 1. Use Static IP Addresses
For critical devices (utama and sub levels), assign static IP addresses to ensure consistency.

### 2. Implement DHCP Reservation
For devices that require dynamic IPs, use DHCP reservation to ensure they always get the same IP address.

### 3. Regular IP Audits
Periodically run the IP detection command to identify potential IP conflicts or changes:

```bash
# Schedule regular IP audits using cron
0 2 * * * cd /path/to/netmonitor && php artisan network:detect-ips
```

### 4. Network Documentation
Maintain a network map with IP assignments:

```
Main Router: 192.168.1.1
Sub Router 1: 192.168.1.2
Sub Router 2: 192.168.1.3
Switch 1: 192.168.1.10
Server 1: 192.168.1.20
```

## Additional Tools

### IP Detection Script
The `scripts/detect_ip.py` script can be used to scan your network and discover active devices:

```bash
# Scan network and save results to file
python3 scripts/detect_ip.py --network 192.168.1.0/24 --output detected_devices.json

# Adjust timeout if needed
python3 scripts/detect_ip.py --network 192.168.1.0/24 --timeout 3
```

### Bulk Status Update
The new `/api/devices/bulk-ping` endpoint allows you to update all device statuses at once, which can help quickly resolve unknown status issues.

## Troubleshooting Tips

If devices still show as "unknown":

1. **Check Network Connectivity**: Ensure the monitoring server can reach the device:
   ```bash
   ping <device_ip_address>
   ```

2. **Check Firewall Settings**: Some devices block ICMP ping requests by default

3. **Verify Device Status**: Ensure the device is physically powered on and connected

4. **Check Network Segmentation**: Ensure monitoring server is on a network segment that can reach all monitored devices

5. **Review Logs**: Check Laravel logs for detailed error information:
   ```bash
   tail -f storage/logs/laravel.log
   ```

By following these steps and implementing the provided tools, you should be able to resolve the "unknown" status issue and maintain accurate device monitoring in the NetMonitor system.