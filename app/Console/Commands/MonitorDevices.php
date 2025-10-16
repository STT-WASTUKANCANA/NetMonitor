<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckDeviceStatuses;

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
    public function handle()
    {
        $this->info('Starting device monitoring...');

        try {
            // Dispatch the job to check device statuses
            $job = new CheckDeviceStatuses();
            // Execute immediately rather than queuing for cron - this is for cron scheduling
            $job->handle();
            
            $this->info('Device monitoring completed.');
            
        } catch (\Exception $e) {
            $this->error('Error during monitoring: ' . $e->getMessage());
            \Log::error('Device monitoring error: ' . $e->getMessage());
            return 1;
        }

        $this->info('Device monitoring finished successfully!');

        return 0;
    }
}
