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
        
        Log::info("Attempting to ping device: " . $device->name . " at IP: " . $ipAddress, [
            'device_id' => $device->id,
            'ip_address' => $ipAddress
        ]);
        
        // Measure start time for response time calculation
        $startTime = microtime(true);

        try {
            // Attempt ping using the system's ping command
            $command = "ping -c 1 -W 2 " . escapeshellarg($ipAddress);
            $output = [];
            $returnCode = 0;
            
            Log::debug("Executing ping command: " . $command);
            
            // Execute ping command and capture any errors
            exec($command . ' 2>&1', $output, $returnCode);
            
            Log::debug("Ping command output:", [
                'output' => $output,
                'return_code' => $returnCode
            ]);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

            if ($returnCode === 0) {
                // Parse response time from ping output if possible
                foreach ($output as $line) {
                    if (preg_match('/time=(\d+\.?\d*)\s*ms/', $line, $matches)) {
                        $responseTime = floatval($matches[1]);
                        Log::debug("Parsed response time from output: " . $responseTime . "ms");
                        break;
                    }
                }

                Log::info("Device responded to ping: " . $device->name, [
                    'device_id' => $device->id,
                    'response_time' => $responseTime,
                    'status' => 'up'
                ]);

                return [
                    'status' => 'up',
                    'response_time' => $responseTime,
                    'message' => 'Device responded to ping'
                ];
            } else {
                Log::info("Device did not respond to ping: " . $device->name, [
                    'device_id' => $device->id,
                    'status' => 'down'
                ]);
                
                return [
                    'status' => 'down',
                    'response_time' => null,
                    'message' => 'Device did not respond to ping'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error pinging device: ' . $e->getMessage(), [
                'device_id' => $device->id,
                'ip_address' => $ipAddress,
                'trace' => $e->getTraceAsString()
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
        Log::info("Starting pingAndRecord for device: " . $device->name, [
            'device_id' => $device->id,
            'current_status' => $device->status,
            'ip_address' => $device->ip_address
        ]);
        
        $result = $this->ping($device);
        
        Log::info("Ping result for device: " . $device->name, [
            'device_id' => $device->id,
            'result_status' => $result['status'],
            'response_time' => $result['response_time']
        ]);
        
        // Create a new log entry
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $result['status'],
            'response_time' => $result['response_time'],
            'message' => $result['message'],
            'checked_at' => now(), // This will use the application timezone
            'is_manual_check' => true, // Mark this as a manual check
        ]);

        // Update the device's status and last checked time
        $device->update([
            'status' => $result['status'],
            'last_checked_at' => now(), // This will use the application timezone
        ]);

        Log::info("Updated device status: " . $device->name, [
            'device_id' => $device->id,
            'new_status' => $result['status']
        ]);

        // Check if an alert needs to be created based on status change
        $this->checkForAlert($device, $result['status']);

        // If device is down and it's a 'utama' or 'sub' level device, mark all children as down too
        if ($result['status'] === 'down' && in_array($device->hierarchy_level, ['utama', 'sub'])) {
            $this->markChildrenAsDown($device);
        }

        // Broadcast the complete hierarchy data after device status update
        $hierarchyData = $this->getCompleteHierarchyData();
        event(new \App\Events\DeviceHierarchyUpdated($hierarchyData, $device->id, $result['status']));

        return [
            'device' => $device,
            'log' => $log,
            'result' => $result,
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
                        'resolved_at' => now(), // This will use the application timezone
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
                'last_checked_at' => now(), // This will use the application timezone
            ]);

            // Create a log entry for the child
            DeviceLog::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'message' => "Device went down due to parent device failure ({$parent->name})",
                'checked_at' => now(), // This will use the application timezone
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
    
    /**
     * Get complete hierarchy data for all devices
     */
    private function getCompleteHierarchyData()
    {
        $allDevices = Device::with(['parent', 'children'])->get();
        
        // Find root devices (those without parents)
        $rootDevices = $allDevices->where('parent_id', null);
        
        $hierarchyData = [];
        
        foreach ($rootDevices as $rootDevice) {
            $hierarchyData[] = $this->buildHierarchyForBroadcast($rootDevice, $allDevices);
        }
        
        return $hierarchyData;
    }
    
    /**
     * Build hierarchy data for a single device and its children
     */
    private function buildHierarchyForBroadcast($device, $allDevices)
    {
        $deviceData = [
            'id' => $device->id,
            'name' => $device->name,
            'ip_address' => $device->ip_address,
            'type' => $device->type,
            'hierarchy_level' => $device->hierarchy_level,
            'status' => $device->status,
            'response_time' => $device->response_time,
            'location' => $device->location,
            'last_checked_at' => $device->last_checked_at,
            'children' => []
        ];
        
        // Find direct children of this device
        $children = $allDevices->where('parent_id', $device->id);
        
        foreach ($children as $child) {
            $deviceData['children'][] = $this->buildHierarchyForBroadcast($child, $allDevices);
        }
        
        return $deviceData;
    }
}