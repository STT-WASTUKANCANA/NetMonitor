<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use Illuminate\Support\Facades\Http;

class DeviceMonitoringService
{
    /**
     * Check the connectivity status of a device using ping and port check
     */
    public function checkDeviceStatus(Device $device)
    {
        $startTime = microtime(true);
        $status = 'down';
        $responseTime = null;

        try {
            // Attempt to ping the device using PHP's exec function
            $output = [];
            $returnCode = 0;
            
            // Try ping command (Linux/Unix)
            $command = 'ping -c 1 -W 2 ' . escapeshellarg($device->ip_address);
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $status = 'up';
                $responseTime = round((microtime(true) - $startTime) * 1000, 2); // Convert to milliseconds with 2 decimals
            } else {
                // If ping fails, try a basic HTTP request as alternative
                try {
                    $response = Http::timeout(5)->get('http://' . $device->ip_address);
                    if ($response->successful()) {
                        $status = 'up';
                        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                    }
                } catch (\Exception $e) {
                    // Status remains 'down'
                }
            }
        } catch (\Exception $e) {
            // Status remains 'down'
        }

        // Log the result
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $status,
            'response_time' => $responseTime,
            'message' => $status === 'up' ? 'Device responded to ping' : 'Device did not respond to ping',
            'checked_at' => now(), // This will use the application timezone
        ]);

        // Update device's status and last checked time
        $device->update([
            'status' => $status,
            'last_checked_at' => now(), // This will use the application timezone
        ]);

        // Create alert if status changed from up to down (or vice versa)
        $this->createAlertIfNeeded($device, $status);

        return [
            'status' => $status,
            'response_time' => $responseTime,
            'log' => $log,
            'timestamp' => now(),
            'datetime_info' => [
                'current' => now()->format('l, d F Y H:i:s'),
                'date' => now()->format('d/m/Y'),
                'time' => now()->format('H:i:s'),
                'day' => now()->format('l')
            ]
        ];
    }

    /**
     * Check all active devices in hierarchical order
     * If an 'utama' device is down, mark all its children as down without pinging them
     */
    public function checkAllDevices()
    {
        $results = [];
        
        // First, check all 'utama' devices
        $utamaDevices = Device::where('is_active', true)
            ->where('hierarchy_level', 'utama')
            ->get();
        
        foreach ($utamaDevices as $device) {
            $result = $this->checkDeviceStatus($device);
            $results[] = $result;
            
            // If utama device is down, mark all children as down without pinging
            if ($result['status'] === 'down') {
                $this->markChildrenAsDown($device);
            } else {
                // If utama device is up, check its children
                $this->checkChildren($device, $results);
            }
        }
        
        // Check other devices that are not under 'utama' hierarchy
        $otherDevices = Device::where('is_active', true)
            ->where('hierarchy_level', '!=', 'utama')
            ->whereNull('parent_id') // Those without a parent
            ->get();
        
        foreach ($otherDevices as $device) {
            $result = $this->checkDeviceStatus($device);
            $results[] = $result;
            
            if ($result['status'] === 'down' && $device->hierarchy_level === 'sub') {
                $this->markChildrenAsDown($device);
            } else {
                $this->checkChildren($device, $results);
            }
        }
        
        return $results;
    }

    /**
     * Recursively check child devices of a parent
     */
    private function checkChildren(Device $parent, array &$results)
    {
        $children = $parent->children()->where('is_active', true)->get();
        
        foreach ($children as $child) {
            $result = $this->checkDeviceStatus($child);
            $results[] = $result;
            
            // If child is a 'sub' level device and it's down, mark its children as down
            if ($result['status'] === 'down' && $child->hierarchy_level === 'sub') {
                $this->markChildrenAsDown($child);
            } elseif ($result['status'] === 'up') {
                // If child is up, continue checking its children
                $this->checkChildren($child, $results);
            }
        }
    }

    /**
     * Mark all child devices as down when parent is down
     */
    private function markChildrenAsDown(Device $parent): void
    {
        // Get all children of this device
        $children = $parent->children()->where('is_active', true)->get();
        
        foreach ($children as $child) {
            // Update child device status to down
            $child->update([
                'status' => 'down',
                'last_checked_at' => now(), // This will use the application timezone
            ]);

            // Create a log entry for the child
            DeviceLog::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'message' => "Device went down due to parent device failure ({$parent->name})",
                'checked_at' => now(), // This will use the application timezone
            ]);

            // Create alert for the child if needed
            $lastChildLog = $child->logs()->latest('checked_at')->first();
            if (!$lastChildLog || $lastChildLog->status === 'up') {
                $child->alerts()->create([
                    'message' => "Device went down due to parent device failure ({$parent->name})",
                    'status' => 'active',
                ]);
            }

            // If child also has children, mark them down recursively
            if ($child->children()->exists()) {
                $this->markChildrenAsDown($child);
            }
        }
    }

    /**
     * Create an alert if the status has changed significantly
     */
    private function createAlertIfNeeded(Device $device, string $newStatus)
    {
        $lastLog = $device->logs()->latest('checked_at')->first();
        
        if (!$lastLog) {
            // If no previous log exists, create alert if current status is down
            if ($newStatus === 'down') {
                $device->alerts()->create([
                    'message' => "Device is {$newStatus}",
                    'status' => 'active',
                ]);
            }
            return;
        }

        $lastStatus = $lastLog->status;
        
        // Create alert if status changed from up to down or down to up
        if (($lastStatus === 'up' && $newStatus === 'down') || 
            ($lastStatus === 'down' && $newStatus === 'up')) {
            
            if ($newStatus === 'down') {
                // Create new alert for device down
                $device->alerts()->create([
                    'message' => "Device went {$newStatus}",
                    'status' => 'active',
                ]);
            } else {
                // Mark any active alerts as resolved when device comes back up
                $device->alerts()
                    ->where('status', 'active')
                    ->update([
                        'status' => 'resolved',
                        'resolved_at' => now(), // This will use the application timezone
                    ]);
            }
        }
    }
}