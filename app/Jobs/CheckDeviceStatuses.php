<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Log;
use App\Models\Alert;
use App\Events\DeviceStatusUpdated;
use App\Events\DeviceAlertCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as Logger;

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
        
        foreach ($devices as $device) {
            try {
                // Perform ping to the device
                $startTime = microtime(true);
                $response = Http::timeout(10)->get("http://{$device->ip_address}");
                $responseTime = round((microtime(true) - $startTime) * 1000, 2); // Convert to milliseconds
                
                $status = $response->successful() ? 'up' : 'down';
            } catch (\Exception $e) {
                // Device is likely down if there's an exception during ping
                $status = 'down';
                $responseTime = null;
            }
            
            // Create a new log entry
            $log = Log::create([
                'device_id' => $device->id,
                'status' => $status,
                'response_time' => $responseTime,
                'checked_at' => now(),
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

            Logger::info("Device {$device->name} status: {$status}, response time: {$responseTime}ms");

        }
        
        Logger::info('Device status check job completed');
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
            Log::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'checked_at' => now(),
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
