<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Services\PingService;
use App\Events\PerSecondDeviceStatusUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonitorDevicesPerSecond implements ShouldQueue
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
     * Execute the job to check all device statuses per second.
     */
    public function handle(): void
    {
        Log::info('Starting per-second device monitoring job');
        
        $devices = Device::all();
        
        foreach ($devices as $device) {
            try {
                // Use the PingService to check device status
                $pingService = new PingService();
                $result = $pingService->ping($device);
                
                $status = $result['status'];
                $responseTime = $result['response_time'];
                $message = $result['message'];

                // Create a new log entry for per-second monitoring
                $log = DeviceLog::create([
                    'device_id' => $device->id,
                    'status' => $status,
                    'response_time' => $responseTime,
                    'message' => $message,
                    'checked_at' => now(),
                    'is_manual_check' => false, // This is an automated check
                ]);

                // Update the device's status and response time
                $device->update([
                    'status' => $status,
                    'response_time' => $responseTime,
                    'last_checked_at' => now(),
                ]);

                // Broadcast the device status update immediately
                event(new PerSecondDeviceStatusUpdated($device, $status, $responseTime));

                Log::debug("Per-second monitoring - Device {$device->name} status: {$status}, response time: {$responseTime}ms");
                
            } catch (\Exception $e) {
                Log::error('Error during per-second device monitoring: ' . $e->getMessage(), [
                    'device_id' => $device->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        Log::info('Per-second device monitoring job completed');
    }
}
