<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Services\PingService;

class ScanUnknownDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:scan-unknown 
                            {--all : Scan all devices, not just those with unknown status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan all devices with unknown status and update their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting device scan for unknown status devices...');
        
        $pingService = new PingService();
        
        // Get all devices with unknown status or all devices if --all flag is used
        if ($this->option('all')) {
            $devices = Device::all();
            $this->info('Scanning all devices...');
        } else {
            $devices = Device::where('status', 'unknown')->get();
            $this->info('Scanning devices with unknown status...');
        }
        
        $totalDevices = $devices->count();
        $this->info("Found {$totalDevices} devices to scan.");
        
        if ($totalDevices === 0) {
            $this->info('No devices to scan.');
            return 0;
        }
        
        $upCount = 0;
        $downCount = 0;
        
        foreach ($devices as $device) {
            $this->info("Scanning device: {$device->name} ({$device->ip_address})");
            
            try {
                // Ping the device and record the result
                $result = $pingService->pingAndRecord($device);
                
                if ($result['result']['status'] === 'up') {
                    $upCount++;
                } else {
                    $downCount++;
                }
                
                $this->line("  -> Status: {$result['result']['status']}");
                
                // Small delay to prevent overwhelming the network
                usleep(100000); // 0.1 second delay
                
            } catch (\Exception $e) {
                $this->error("  -> Error scanning device {$device->name}: " . $e->getMessage());
                $downCount++; // Consider as down if there's an error
            }
        }
        
        $this->info("Scan completed!");
        $this->info("Summary: {$upCount} devices up, {$downCount} devices down");
        
        return 0;
    }
}
