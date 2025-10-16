<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Services\PingService;
use Illuminate\Support\Facades\Http;

class DetectDeviceIPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:detect-ips 
                            {--network=192.168.1.0/24 : Network range to scan}
                            {--timeout=2 : Timeout for ping in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detects active IP addresses in the network and verifies existing devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting IP detection process...');
        
        $networkRange = $this->option('network');
        $timeout = $this->option('timeout');
        
        // Get all devices from the database
        $devices = Device::all();
        
        $this->info("Scanning network range: {$networkRange}");
        $this->info("Found " . $devices->count() . " devices in the database");
        
        // For each device, test if the IP is reachable
        $reachableDevices = [];
        $unreachableDevices = [];
        $unknownIPDevices = [];
        
        foreach ($devices as $device) {
            if (empty($device->ip_address) || $device->ip_address === 'unknown' || $device->ip_address === 'Unknown') {
                $unknownIPDevices[] = $device;
                $this->warn("Device {$device->name} has unknown IP address: {$device->ip_address}");
                continue;
            }
            
            $pingService = new PingService();
            $result = $pingService->ping($device);
            
            if ($result['status'] === 'up') {
                $reachableDevices[] = [
                    'device' => $device,
                    'response_time' => $result['response_time']
                ];
                $this->info("✓ Device {$device->name} ({$device->ip_address}) is reachable - Response: {$result['response_time']}ms");
            } else {
                $unreachableDevices[] = $device;
                $this->error("✗ Device {$device->name} ({$device->ip_address}) is unreachable");
            }
        }
        
        // Summary
        $this->newLine();
        $this->info("SUMMARY:");
        $this->info("- Reachable devices: " . count($reachableDevices));
        $this->info("- Unreachable devices: " . count($unreachableDevices));
        $this->info("- Devices with unknown IP: " . count($unknownIPDevices));
        
        // Handle unknown IP devices
        if (!empty($unknownIPDevices)) {
            $this->newLine();
            $this->warn("Devices with unknown IP addresses:");
            foreach ($unknownIPDevices as $device) {
                $this->line("- {$device->name} (ID: {$device->id}) - Current IP: {$device->ip_address}");
            }
        }
        
        // Optionally, try to find the correct IP for unknown devices
        if ($this->confirm('Do you want to try to find IP addresses for unknown devices?', true)) {
            $this->findIPForUnknownDevices($unknownIPDevices);
        }
        
        $this->info('IP detection process completed.');
    }
    
    /**
     * Try to find IP addresses for devices with unknown IPs
     */
    private function findIPForUnknownDevices($unknownIPDevices)
    {
        $this->info("Attempting to find IP addresses for devices with unknown IPs...");
        
        // This is a simplified approach - in a real scenario, you might need more sophisticated methods
        // such as MAC address lookup, DHCP logs, or network scanning
        
        foreach ($unknownIPDevices as $device) {
            $this->info("Looking for IP for: {$device->name}");
            
            // Here you could implement specific logic based on your network setup
            // For example, if you know the device type or location, you might be able to guess the IP
            
            // You could also scan the network and try to match based on device characteristics
            // like open ports, device type, etc.
            
            // Placeholder for IP detection logic
            $this->warn("Manual intervention required for device: {$device->name} (ID: {$device->id})");
            $newIP = $this->ask("Enter the correct IP address for {$device->name} (leave empty to skip)");
            
            if (!empty($newIP) && filter_var($newIP, FILTER_VALIDATE_IP)) {
                $device->update(['ip_address' => $newIP]);
                $this->info("Updated IP for {$device->name} to {$newIP}");
            } else {
                $this->error("Invalid IP address provided for {$device->name}");
            }
        }
    }
}