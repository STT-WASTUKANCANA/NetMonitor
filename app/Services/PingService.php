<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PingService
{
    /**
     * Ping a specific device and return the result
     * 
     * @param Device $device
     * @return array
     */
    public function ping(Device $device): array
    {
        $ipAddress = $device->ip_address;
        
        // Measure start time for response time calculation
        $startTime = microtime(true);

        try {
            // Attempt ping using the system's ping command
            $command = "ping -c 1 -W 2 " . escapeshellarg($ipAddress);
            $output = [];
            $returnCode = 0;
            
            // Execute ping command
            exec($command, $output, $returnCode);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

            if ($returnCode === 0) {
                // Parse response time from ping output if possible
                foreach ($output as $line) {
                    if (preg_match('/time=(\d+\.?\d*)\s*ms/', $line, $matches)) {
                        $responseTime = floatval($matches[1]);
                        break;
                    }
                }

                return [
                    'status' => 'up',
                    'response_time' => $responseTime,
                    'message' => 'Device responded to ping'
                ];
            } else {
                return [
                    'status' => 'down',
                    'response_time' => null,
                    'message' => 'Device did not respond to ping'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error pinging device: ' . $e->getMessage(), [
                'device_id' => $device->id,
                'ip_address' => $ipAddress
            ]);
            
            return [
                'status' => 'down',
                'response_time' => null,
                'message' => 'Error during ping: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ping a device and update its status
     * 
     * @param Device $device
     * @return array
     */
    public function pingAndRecord(Device $device): array
    {
        $result = $this->ping($device);
        
        // Create a new log entry
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $result['status'],
            'response_time' => $result['response_time'],
            'checked_at' => now(),
            'is_manual_check' => true, // Mark this as a manual check
        ]);

        // Update the device's status and last checked time
        $device->update([
            'status' => $result['status'],
            'last_checked_at' => now(),
        ]);

        // Check if an alert needs to be created based on status change
        $this->checkForAlert($device, $result['status']);

        // If device is down and it's a 'utama' level device, mark all children as down too
        if ($result['status'] === 'down' && $device->hierarchy_level === 'utama') {
            $this->markChildrenAsDown($device);
        }

        return [
            'device' => $device,
            'log' => $log,
            'result' => $result
        ];
    }

    /**
     * Check for alert when status changes
     */
    private function checkForAlert(Device $device, string $newStatus): void
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
                        'resolved_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Mark all child devices as down when parent is down
     */
    private function markChildrenAsDown(Device $parent): void
    {
        // Get all children of this device
        $children = Device::where('parent_id', $parent->id)->get();
        
        foreach ($children as $child) {
            // Update child device status to down
            $child->update([
                'status' => 'down',
                'last_checked_at' => now(),
            ]);

            // Create a log entry for the child
            DeviceLog::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'checked_at' => now(),
                'is_manual_check' => true, // Mark as manual check
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
}