<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // This endpoint is used by the Python script to get all active devices
        $devices = Device::where('is_active', true)
            ->select('id', 'name', 'ip_address', 'type', 'hierarchy_level', 'parent_id', 'location', 'description', 'status', 'last_checked_at')
            ->get();

        return response()->json(DeviceResource::collection($devices));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $device = Device::with(['parent', 'children', 'logs', 'alerts'])->findOrFail($id);
        
        return response()->json(new DeviceResource($device));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Record device status from monitoring script
     */
    public function recordStatus(Request $request, string $id): JsonResponse
    {
        $device = Device::with('parent')->findOrFail($id);

        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|in:up,down',
            'response_time' => 'nullable|numeric|min:0',
        ]);

        // Create a new log entry
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $validated['status'],
            'response_time' => $validated['response_time'] ?? null,
            'checked_at' => now(),
        ]);

        // Update the device's status and last checked time
        $device->update([
            'status' => $validated['status'],
            'last_checked_at' => now(),
        ]);

        // Check if an alert needs to be created based on status change
        $this->checkForAlert($device, $validated['status']);

        // If device is down and it's a 'utama' level device, mark all children as down too
        if ($validated['status'] === 'down' && $device->hierarchy_level === 'utama') {
            $this->markChildrenAsDown($device);
        }

        return response()->json([
            'message' => 'Device status recorded successfully',
            'log_id' => $log->id
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Check for alert when status changes
     */
    private function checkForAlert(Device $device, string $newStatus): void
    {
        $lastLog = $device->logs()->latest('checked_at')->first();
        
        if (!$lastLog) {
            // If no previous log exists, create alert if current status is down
            if ($newStatus === 'down') {
                $device->alerts()->create([
                    'message' => "Device is {$newStatus}",
                    'status' => 'active',
                ]);
            }
            return;
        }

        $lastStatus = $lastLog->status;
        
        // Create alert if status changed from up to down or down to up
        if (($lastStatus === 'up' && $newStatus === 'down') || 
            ($lastStatus === 'down' && $newStatus === 'up')) {
            
            if ($newStatus === 'down') {
                // Create new alert for device down
                $device->alerts()->create([
                    'message' => "Device went {$newStatus}",
                    'status' => 'active',
                ]);
            } else {
                // Mark any active alerts as resolved when device comes back up
                $device->alerts()
                    ->where('status', 'active')
                    ->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Mark all child devices as down when parent is down
     */
    private function markChildrenAsDown(Device $parent): void
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
            DeviceLog::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'checked_at' => now(),
            ]);

            // Create alert for the child if needed
            $lastChildLog = $child->logs()->latest('checked_at')->first();
            if (!$lastChildLog || $lastChildLog->status === 'up') {
                $child->alerts()->create([
                    'message' => "Device went down due to parent device failure ({$parent->name})",
                    'status' => 'active',
                ]);
            }

            // If child also has children, mark them down recursively
            if ($child->children()->exists()) {
                $this->markChildrenAsDown($child);
            }
        }
    }
}
