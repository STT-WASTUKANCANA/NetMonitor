<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateRealTimeHierarchy;

class UpdateRealTimeHierarchyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hierarchy:update-realtime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update real-time hierarchy data for all devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating real-time hierarchy data...');
        
        // Dispatch the real-time hierarchy update job
        UpdateRealTimeHierarchy::dispatch();
        
        $this->info('Real-time hierarchy update job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}