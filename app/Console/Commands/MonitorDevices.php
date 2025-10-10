<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DeviceMonitoringService;

class MonitorDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor all active devices and log their status';

    /**
     * Execute the console command.
     */
    public function handle(DeviceMonitoringService $monitoringService)
    {
        $this->info('Starting device monitoring...');

        try {
            $results = $monitoringService->checkAllDevices();
            
            $upCount = collect($results)->where('status', 'up')->count();
            $downCount = collect($results)->where('status', 'down')->count();
            
            $this->info("Monitoring completed:");
            $this->info("- {$upCount} devices are UP");
            $this->info("- {$downCount} devices are DOWN");
            
            // Log to Laravel's log system
            \Log::info("Device monitoring completed. {$upCount} up, {$downCount} down.");
        } catch (\Exception $e) {
            $this->error('Error during monitoring: ' . $e->getMessage());
            \Log::error('Device monitoring error: ' . $e->getMessage());
            return 1;
        }

        $this->info('Device monitoring finished successfully!');

        return 0;
    }
}
