<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use App\Events\DeviceStatusUpdated;
use App\Events\DeviceAlertCreated;
use App\Events\NetworkMetricUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as Logger;
use App\Services\PingService;

class CheckDeviceStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job to check all device statuses.
     */
    public function handle(): void
    {
        Logger::info('Starting device status check job');
        
        $devices = Device::all();
        $responseTimeData = [];
        
        foreach ($devices as $device) {
            try {
                // Use the PingService instead of HTTP calls for more reliable network monitoring
                $pingService = new PingService();
                $result = $pingService->ping($device);
                
                $status = $result['status'];
                $responseTime = $result['response_time'];
            } catch (\Exception $e) {
                // Device is likely down if there's an exception during ping
                $status = 'down';
                $responseTime = null;
            }
            
            // Create a new log entry
            $log = DeviceLog::create([
                'device_id' => $device->id,
                'status' => $status,
                'response_time' => $responseTime,
                'checked_at' => now(),
                'is_manual_check' => false, // This is an automated check
            ]);

            // Update the device's status and response time
            $device->update([
                'status' => $status,
                'response_time' => $responseTime,
                'last_checked_at' => now(),
            ]);

            // Check if an alert needs to be created based on status change
            $this->checkForAlert($device, $status);

            // If device is down and it's a 'utama' or 'sub' level device, mark all children as down too
            if ($status === 'down' && in_array($device->hierarchy_level, ['utama', 'sub'])) {
                $this->markChildrenAsDown($device);
            }

            // Broadcast the device status update
            event(new DeviceStatusUpdated($device, $status, $responseTime));
            
            // Collect response time data for overall network metrics
            if ($responseTime !== null) {
                $responseTimeData[] = [
                    'device_id' => $device->id,
                    'device_name' => $device->name,
                    'response_time' => $responseTime,
                    'timestamp' => now()->format('H:i:s'),
                ];
            }

            Logger::info("Device {$device->name} status: {$status}, response time: {$responseTime}ms");
        }
        
        // After checking all devices, broadcast the complete hierarchy data
        $hierarchyData = $this->getCompleteHierarchyData();
        event(new \App\Events\DeviceHierarchyUpdated($hierarchyData));
        
        // Broadcast the network metrics update after checking all devices
        if (!empty($responseTimeData)) {
            $avgResponseTime = array_sum(array_column($responseTimeData, 'response_time')) / count($responseTimeData);
            
            event(new NetworkMetricUpdated([
                'total_devices' => count($devices),
                'online_devices' => $devices->where('status', 'up')->count(),
                'offline_devices' => $devices->where('status', 'down')->count(),
                'average_response_time' => round($avgResponseTime, 2),
                'response_time_data' => $responseTimeData,
                'timestamp' => now()->toISOString(),
            ]));
        }
        
        // After checking all devices, broadcast the complete hierarchy data
        $hierarchyData = $this->getCompleteHierarchyData();
        event(new \App\Events\DeviceHierarchyUpdated($hierarchyData));
        
        Logger::info('Device status check job completed');
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

    /**
     * Check for alert when status changes
     */
    private function checkForAlert($device, string $newStatus): void
    {
        $lastLog = $device->logs()->latest('checked_at')->first();
        
        if (!$lastLog) {
            // If no previous log exists, create alert if current status is down
            if ($newStatus === 'down') {
                $alert = $device->alerts()->create([
                    'message' => "Device is {$newStatus}",
                    'status' => 'active',
                ]);
                
                // Broadcast the alert creation
                event(new DeviceAlertCreated($alert));
            }
            return;
        }

        $lastStatus = $lastLog->status;
        
        // Create alert if status changed from up to down
        if ($lastStatus === 'up' && $newStatus === 'down') {
            $alert = $device->alerts()->create([
                'message' => "Device went {$newStatus}",
                'status' => 'active',
            ]);
            
            // Broadcast the alert creation
            event(new DeviceAlertCreated($alert));
        } 
        // Mark any active alerts as resolved when device comes back up
        elseif ($lastStatus === 'down' && $newStatus === 'up') {
            $device->alerts()
                ->where('status', 'active')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
        }
    }
    
    /**
     * Mark all child devices as down when parent is down
     */
    private function markChildrenAsDown($parent): void
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
                'is_manual_check' => false, // This is an automated check
            ]);

            // Create alert for the child if needed
            $lastChildLog = $child->logs()->latest('checked_at')->first();
            if (!$lastChildLog || $lastChildLog->status === 'up') {
                $alert = $child->alerts()->create([
                    'message' => "Device went down due to parent device failure ({$parent->name})",
                    'status' => 'active',
                ]);
                
                // Broadcast the alert creation
                event(new DeviceAlertCreated($alert));
            }

            // If child also has children, mark them down recursively
            if ($child->children()->exists()) {
                $this->markChildrenAsDown($child);
            }
        }
    }
}
