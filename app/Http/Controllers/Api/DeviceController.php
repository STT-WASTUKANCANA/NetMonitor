<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use App\Services\PingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    private $pingService;

    public function __construct(PingService $pingService)
    {
        $this->pingService = $pingService;
    }
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
        $device = Device::with([
            'parent', 
            'children' => function($query) {
                $query->orderBy('hierarchy_level')->orderBy('name');
            }, 
            'logs', 
            'alerts'
        ])->findOrFail($id);
        
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
            'message' => 'nullable|string',
        ]);

        // Create a new log entry
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $validated['status'],
            'response_time' => $validated['response_time'] ?? null,
            'message' => $validated['message'] ?? null,
            'checked_at' => now(), // This will use the application timezone
        ]);

        // Update the device's status and last checked time
        $device->update([
            'status' => $validated['status'],
            'last_checked_at' => now(), // This will use the application timezone
        ]);

        // Check if an alert needs to be created based on status change
        $this->checkForAlert($device, $validated['status']);

        // If device is down and it's a 'utama' or 'sub' level device, mark all children as down too
        if ($validated['status'] === 'down' && in_array($device->hierarchy_level, ['utama', 'sub'])) {
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
                $alert = $device->alerts()->create([
                    'message' => "Device is {$newStatus}",
                    'status' => 'active',
                ]);
                
                // Broadcast the alert creation
                event(new \App\Events\DeviceAlertCreated($alert));
            }
            return;
        }

        $lastStatus = $lastLog->status;
        
        // Create alert if status changed from up to down
        if ($lastStatus === 'up' && $newStatus === 'down') {
            $alert = $device->alerts()->create([
                'message' => "Device went {$newStatus}",
                'status' => 'active',
            ]);
            
            // Broadcast the alert creation
            event(new \App\Events\DeviceAlertCreated($alert));
        } 
        // Mark any active alerts as resolved when device comes back up
        elseif ($lastStatus === 'down' && $newStatus === 'up') {
            $device->alerts()
                ->where('status', 'active')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(), // This will use the application timezone
                ]);
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
                'last_checked_at' => now(), // This will use the application timezone
            ]);

            // Create a log entry for the child
            \App\Models\Log::create([
                'device_id' => $child->id,
                'status' => 'down',
                'response_time' => null,
                'checked_at' => now(), // This will use the application timezone
            ]);

            // Create alert for the child if needed
            $lastChildLog = $child->logs()->latest('checked_at')->first();
            if (!$lastChildLog || $lastChildLog->status === 'up') {
                $alert = $child->alerts()->create([
                    'message' => "Device went down due to parent device failure ({$parent->name})",
                    'status' => 'active',
                ]);
                
                // Broadcast the alert creation
                event(new \App\Events\DeviceAlertCreated($alert));
            }

            // If child also has children, mark them down recursively
            if ($child->children()->exists()) {
                $this->markChildrenAsDown($child);
            }
        }
    }

    /**
     * Update device status from monitoring script (NEW endpoint)
     */
    public function updateStatus(Request $request): JsonResponse
    {
        // Validate the incoming request - expects an array of device statuses
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'status' => 'required|in:up,down',
            'response_time' => 'nullable|numeric|min:0|max:9999.99',
        ]);

        $device = Device::findOrFail($validated['device_id']);

        // Create a new log entry
        $log = \App\Models\Log::create([
            'device_id' => $validated['device_id'],
            'status' => $validated['status'],
            'response_time' => $validated['response_time'] ?? null,
            'checked_at' => now(), // This will use the application timezone
        ]);

        // Update the device's status and response time
        $device->update([
            'status' => $validated['status'],
            'response_time' => $validated['response_time'] ?? null,
            'last_checked_at' => now(), // This will use the application timezone
        ]);

        // Check if an alert needs to be created based on status change
        $this->checkForAlert($device, $validated['status']);

        // If device is down and it's a 'utama' or 'sub' level device, mark all children as down too
        if ($validated['status'] === 'down' && in_array($device->hierarchy_level, ['utama', 'sub'])) {
            $this->markChildrenAsDown($device);
        }

        // Broadcast the device status update
        event(new \App\Events\DeviceStatusUpdated($device, $validated['status'], $validated['response_time'] ?? null));

        return response()->json([
            'message' => 'Device status updated successfully',
            'log_id' => $log->id,
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'status' => $device->status,
                'response_time' => $device->response_time,
                'last_checked_at' => $device->last_checked_at,
            ]
        ], 201);
    }

    /**
     * Batch update multiple device statuses
     */
    public function batchUpdateStatus(Request $request): JsonResponse
    {
        // Validate the incoming request - expects an array of device statuses
        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*.device_id' => 'required|exists:devices,id',
            'devices.*.status' => 'required|in:up,down',
            'devices.*.response_time' => 'nullable|numeric|min:0|max:9999.99',
        ]);

        $results = [];
        foreach ($validated['devices'] as $deviceData) {
            $device = Device::findOrFail($deviceData['device_id']);

            // Create a new log entry
            $log = \App\Models\Log::create([
                'device_id' => $deviceData['device_id'],
                'status' => $deviceData['status'],
                'response_time' => $deviceData['response_time'] ?? null,
                'checked_at' => now(), // This will use the application timezone
            ]);

            // Update the device's status and response time
            $device->update([
                'status' => $deviceData['status'],
                'response_time' => $deviceData['response_time'] ?? null,
                'last_checked_at' => now(), // This will use the application timezone
            ]);

            // Check if an alert needs to be created based on status change
            $this->checkForAlert($device, $deviceData['status']);

            // If device is down and it's a 'utama' or 'sub' level device, mark all children as down too
            if ($deviceData['status'] === 'down' && in_array($device->hierarchy_level, ['utama', 'sub'])) {
                $this->markChildrenAsDown($device);
            }

            // Broadcast the device status update
            event(new \App\Events\DeviceStatusUpdated($device, $deviceData['status'], $deviceData['response_time'] ?? null));

            $results[] = [
                'device_id' => $device->id,
                'status' => 'success',
                'log_id' => $log->id,
            ];
        }

        return response()->json([
            'message' => 'Device statuses updated successfully',
            'results' => $results
        ], 201);
    }

    /**
     * Manually scan a device
     */
    public function scanDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        $device = Device::findOrFail($validated['device_id']);

        // Authorize the action - user must have permission to view or edit devices
        if (!$request->user()->can('view devices') && !$request->user()->can('edit devices')) {
            return response()->json([
                'message' => 'Unauthorized to scan device',
                'error' => true
            ], 403);
        }

        try {
            // Ping the device and record the result
            $pingResult = $this->pingService->pingAndRecord($device);

            return response()->json([
                'message' => 'Device scanned successfully',
                'device' => new DeviceResource($device),
                'result' => $pingResult['result'],
                'timestamp' => now()->toISOString(), // This will automatically use the application timezone
                'datetime_info' => [
                    'current' => now()->format('l, d F Y H:i:s'),
                    'date' => now()->format('d/m/Y'),
                    'time' => now()->format('H:i:s'),
                    'day' => now()->format('l')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error scanning device: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Manually ping a specific device
     */
    public function pingDevice(Request $request, string $id): JsonResponse
    {
        $device = Device::findOrFail($id);

        // Authorize the action - user must have permission to view or edit devices
        if (!$request->user()->can('view devices') && !$request->user()->can('edit devices')) {
            return response()->json([
                'message' => 'Unauthorized to ping device',
                'error' => true
            ], 403);
        }

        try {
            // Ping the device and record the result
            $pingResult = $this->pingService->pingAndRecord($device);

            return response()->json([
                'message' => 'Device pinged successfully',
                'device' => new DeviceResource($device),
                'result' => $pingResult['result'],
                'timestamp' => now()->toISOString(), // This will automatically use the application timezone
                'datetime_info' => [
                    'current' => now()->format('l, d F Y H:i:s'),
                    'date' => now()->format('d/m/Y'),
                    'time' => now()->format('H:i:s'),
                    'day' => now()->format('l')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error pinging device: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }
}
