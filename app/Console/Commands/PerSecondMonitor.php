<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MonitorDevicesPerSecond;
use Illuminate\Support\Facades\Log;

class PerSecondMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:per-second';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run per-second monitoring for all devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting per-second monitoring...');
        
        // This is a long-running command that continuously monitors devices every second
        while (true) {
            try {
                // Dispatch the job to check all device statuses
                dispatch(new MonitorDevicesPerSecond());
                
                // Wait for 1 second before the next check
                sleep(1);
                
                // For logging and monitoring the command itself
                $this->output->write('.');
            } catch (\Exception $e) {
                Log::error('Error in per-second monitor command: ' . $e->getMessage());
                $this->error('Error: ' . $e->getMessage());
                
                // Wait for a bit before trying again
                sleep(5);
            }
        }
    }
}
