<?php

namespace App\Jobs;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\RealTimeHierarchyUpdated;

class UpdateRealTimeHierarchy implements ShouldQueue
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
     * Execute the job to update real-time hierarchy data.
     */
    public function handle(): void
    {
        // Get all devices with their relationships and latest logs
        $allDevices = Device::with(['parent', 'children', 'logs' => function($query) {
            $query->latest()->limit(10);
        }])->get();
        
        // Find root devices (those without parents)
        $rootDevices = $allDevices->where('parent_id', null);
        
        $hierarchyData = [];
        
        foreach ($rootDevices as $rootDevice) {
            $hierarchyData[] = $this->buildRealTimeHierarchyForBroadcast($rootDevice, $allDevices);
        }
        
        // Group by hierarchy level for dashboard visualization
        $levelStats = [
            'utama' => $allDevices->where('hierarchy_level', 'utama')->map(function($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $device->ip_address,
                    'type' => $device->type,
                    'hierarchy_level' => $device->hierarchy_level,
                    'status' => $device->status,
                    'response_time' => $device->response_time,
                    'last_checked_at' => $device->last_checked_at,
                    'location' => $device->location,
                ];
            }),
            'sub' => $allDevices->where('hierarchy_level', 'sub')->map(function($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $device->ip_address,
                    'type' => $device->type,
                    'hierarchy_level' => $device->hierarchy_level,
                    'status' => $device->status,
                    'response_time' => $device->response_time,
                    'last_checked_at' => $device->last_checked_at,
                    'location' => $device->location,
                ];
            }),
            'device' => $allDevices->where('hierarchy_level', 'device')->map(function($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $device->ip_address,
                    'type' => $device->type,
                    'hierarchy_level' => $device->hierarchy_level,
                    'status' => $device->status,
                    'response_time' => $device->response_time,
                    'last_checked_at' => $device->last_checked_at,
                    'location' => $device->location,
                ];
            })
        ];
        
        $realTimeHierarchyData = [
            'hierarchy_tree' => $hierarchyData,
            'level_stats' => $levelStats,
            'summary' => [
                'total_devices' => $allDevices->count(),
                'online_devices' => $allDevices->where('status', 'up')->count(),
                'offline_devices' => $allDevices->where('status', 'down')->count(),
                'utama_count' => $allDevices->where('hierarchy_level', 'utama')->count(),
                'sub_count' => $allDevices->where('hierarchy_level', 'sub')->count(),
                'device_count' => $allDevices->where('hierarchy_level', 'device')->count(),
            ],
            'timestamp' => now()->toISOString()
        ];
        
        // Broadcast the real-time hierarchy update
        event(new RealTimeHierarchyUpdated($realTimeHierarchyData));
    }
    
    /**
     * Build real-time hierarchy data for a single device with detailed metrics
     */
    private function buildRealTimeHierarchyForBroadcast($device, $allDevices)
    {
        // Get recent logs for response time history
        $recentLogs = $device->logs->sortByDesc('checked_at')->take(5);
        
        $latencyHistory = [];
        foreach ($recentLogs as $log) {
            $latencyHistory[] = [
                'timestamp' => $log->checked_at->toISOString(),
                'response_time' => $log->response_time,
                'status' => $log->status
            ];
        }
        
        $deviceData = [
            'id' => $device->id,
            'name' => $device->name,
            'ip_address' => $device->ip_address,
            'type' => $device->type,
            'hierarchy_level' => $device->hierarchy_level,
            'status' => $device->status,
            'response_time' => $device->response_time,
            'location' => $device->location,
            'last_checked_at' => $device->last_checked_at ? $device->last_checked_at->format('c') : null,
            'latency_history' => $latencyHistory,
            'children' => []
        ];
        
        // Find direct children of this device
        $children = $allDevices->where('parent_id', $device->id);
        
        foreach ($children as $child) {
            $deviceData['children'][] = $this->buildRealTimeHierarchyForBroadcast($child, $allDevices);
        }
        
        return $deviceData;
    }
}