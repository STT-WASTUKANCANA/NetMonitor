<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample devices with hierarchical structure
        $devices = [
            [
                'name' => 'Router Utama STT',
                'ip_address' => '192.168.1.1',
                'type' => 'router',
                'hierarchy_level' => 'utama',
                'location' => 'Server Room Lt. 1',
                'description' => 'Router utama kampus STT Wastukancana',
                'status' => 'up',
                'is_active' => true,
            ],
            [
                'name' => 'Switch Gedung A',
                'ip_address' => '192.168.1.10',
                'type' => 'switch',
                'hierarchy_level' => 'sub',
                'location' => 'Gedung A Lt. 2',
                'description' => 'Switch distribusi untuk Gedung A',
                'status' => 'up',
                'is_active' => true,
            ],
            [
                'name' => 'AP Ruang Kuliah 101',
                'ip_address' => '192.168.1.101',
                'type' => 'access_point',
                'hierarchy_level' => 'device',
                'location' => 'Gedung A Lt. 2 - Ruang 101',
                'description' => 'Access Point untuk ruang kuliah 101',
                'status' => 'up',
                'is_active' => true,
            ],
            [
                'name' => 'AP Ruang Kuliah 102',
                'ip_address' => '192.168.1.102',
                'type' => 'access_point',
                'hierarchy_level' => 'device',
                'location' => 'Gedung A Lt. 2 - Ruang 102',
                'description' => 'Access Point untuk ruang kuliah 102',
                'status' => 'down',
                'is_active' => true,
            ],
            [
                'name' => 'Switch Gedung B',
                'ip_address' => '192.168.1.20',
                'type' => 'switch',
                'hierarchy_level' => 'sub',
                'location' => 'Gedung B Lt. 1',
                'description' => 'Switch distribusi untuk Gedung B',
                'status' => 'up',
                'is_active' => true,
            ],
            [
                'name' => 'Server Web',
                'ip_address' => '192.168.1.50',
                'type' => 'server',
                'hierarchy_level' => 'device',
                'location' => 'Server Room Lt. 1',
                'description' => 'Server web utama',
                'status' => 'up',
                'is_active' => true,
            ],
        ];

        // Create devices and establish hierarchy
        $createdDevices = [];
        
        foreach ($devices as $deviceData) {
            $device = Device::create($deviceData);
            $createdDevices[$deviceData['name']] = $device;
        }

        // Set up parent-child relationships
        $createdDevices['Switch Gedung A']->update([
            'parent_id' => $createdDevices['Router Utama STT']->id
        ]);
        
        $createdDevices['AP Ruang Kuliah 101']->update([
            'parent_id' => $createdDevices['Switch Gedung A']->id
        ]);
        
        $createdDevices['AP Ruang Kuliah 102']->update([
            'parent_id' => $createdDevices['Switch Gedung A']->id
        ]);
        
        $createdDevices['Switch Gedung B']->update([
            'parent_id' => $createdDevices['Router Utama STT']->id
        ]);
        
        $createdDevices['Server Web']->update([
            'parent_id' => $createdDevices['Router Utama STT']->id
        ]);

        $this->command->info('Sample devices created successfully!');
    }
}
