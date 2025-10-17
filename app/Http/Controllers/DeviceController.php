<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\PingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    private $pingService;
    
    public function __construct(PingService $pingService)
    {
        $this->middleware('auth');
        $this->pingService = $pingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view devices');
        
        $devices = Device::with(['children', 'logs', 'alerts', 'parent'])
            ->whereNull('parent_id')
            ->orderBy('hierarchy_level')
            ->orderBy('name')
            ->get();
            
        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create devices');
        
        // Get only active devices that can be parents (utama or sub level devices only)
        $parentDevices = Device::active()
            ->whereIn('hierarchy_level', ['utama', 'sub'])
            ->orderBy('hierarchy_level')
            ->orderBy('name')
            ->get();
        return view('devices.create', compact('parentDevices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create devices');
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:router,switch,access_point,server,other',
            'hierarchy_level' => 'required|in:utama,sub,device',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];

        // Validation for parent requirement
        if ($request->hierarchy_level !== 'utama') {
            $validationRules['parent_id'] = 'required|exists:devices,id';
        }

        $request->validate($validationRules);

        $deviceData = $request->all();

        // Validation: Hanya satu utama - Only one device can have hierarchy_level = 'utama'
        if ($deviceData['hierarchy_level'] === 'utama') {
            $existingUtama = Device::where('hierarchy_level', 'utama')->first();
            if ($existingUtama) {
                return redirect()->back()->withErrors(['hierarchy_level' => 'Hanya boleh ada satu perangkat utama.'])->withInput();
            }
        }

        // Validation: sub harus punya induk - If hierarchy_level = 'sub', parent_id must point to 'utama'
        if ($deviceData['hierarchy_level'] === 'sub' && isset($deviceData['parent_id'])) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if (!$parentDevice || $parentDevice->hierarchy_level !== 'utama') {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat sub harus memiliki induk berupa perangkat utama.'])->withInput();
            }
        }

        // Validation: device punya induk - If hierarchy_level = 'device', parent_id must point to 'sub' or 'utama'
        if ($deviceData['hierarchy_level'] === 'device' && isset($deviceData['parent_id'])) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if (!$parentDevice || !in_array($parentDevice->hierarchy_level, ['utama', 'sub'])) {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat device harus memiliki induk berupa perangkat utama atau sub.'])->withInput();
            }
        }

        // Additional validation to ensure parent device is not of 'device' level (legacy check)
        if (isset($deviceData['parent_id']) && $deviceData['parent_id']) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if ($parentDevice && $parentDevice->hierarchy_level === 'device') {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat induk harus berupa perangkat utama atau sub, bukan perangkat biasa.'])->withInput();
            }
        }

        $device = Device::create($deviceData);
        
        // Immediately check the device status after creation
        try {
            $this->pingService->pingAndRecord($device);
        } catch (\Exception $e) {
            \Log::error('Error pinging newly created device: ' . $e->getMessage(), [
                'device_id' => $device->id,
                'ip_address' => $device->ip_address
            ]);
        }

        return redirect()->route('devices.index')->with('success', 'Device created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        $this->authorize('view devices');
        
        $device->load(['parent', 'children', 'logs', 'alerts']);
        return view('devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        $this->authorize('edit devices');
        
        // Get only active devices that can be parents (utama or sub level devices only, excluding the current device to prevent circular reference)
        $parentDevices = Device::active()
            ->whereIn('hierarchy_level', ['utama', 'sub'])
            ->where('id', '!=', $device->id)
            ->orderBy('hierarchy_level')
            ->orderBy('name')
            ->get();
            
        return view('devices.edit', compact('device', 'parentDevices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $this->authorize('edit devices');
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:router,switch,access_point,server,other',
            'hierarchy_level' => 'required|in:utama,sub,device',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];

        if ($request->hierarchy_level !== 'utama') {
            $validationRules['parent_id'] = 'required|exists:devices,id';
        }

        $request->validate($validationRules);

        $deviceData = $request->all();

        // Validation: Hanya satu utama - Only one device can have hierarchy_level = 'utama'
        if ($deviceData['hierarchy_level'] === 'utama') {
            $existingUtama = Device::where('hierarchy_level', 'utama')->where('id', '!=', $device->id)->first();
            if ($existingUtama) {
                return redirect()->back()->withErrors(['hierarchy_level' => 'Hanya boleh ada satu perangkat utama.'])->withInput();
            }
        }

        // Validation: sub harus punya induk - If hierarchy_level = 'sub', parent_id must point to 'utama'
        if ($deviceData['hierarchy_level'] === 'sub' && isset($deviceData['parent_id'])) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if (!$parentDevice || $parentDevice->hierarchy_level !== 'utama') {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat sub harus memiliki induk berupa perangkat utama.'])->withInput();
            }
        }

        // Validation: device punya induk - If hierarchy_level = 'device', parent_id must point to 'sub' or 'utama'
        if ($deviceData['hierarchy_level'] === 'device' && isset($deviceData['parent_id'])) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if (!$parentDevice || !in_array($parentDevice->hierarchy_level, ['utama', 'sub'])) {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat device harus memiliki induk berupa perangkat utama atau sub.'])->withInput();
            }
        }

        // Additional validation to ensure parent device is not of 'device' level (legacy check)
        if (isset($deviceData['parent_id']) && $deviceData['parent_id']) {
            $parentDevice = Device::find($deviceData['parent_id']);
            if ($parentDevice && $parentDevice->hierarchy_level === 'device') {
                return redirect()->back()->withErrors(['parent_id' => 'Perangkat induk harus berupa perangkat utama atau sub, bukan perangkat biasa.'])->withInput();
            }
        }

        $device->update($deviceData);
        
        // If IP address was changed, check the device status
        if (isset($deviceData['ip_address']) && $device->wasChanged('ip_address')) {
            try {
                $this->pingService->pingAndRecord($device);
            } catch (\Exception $e) {
                \Log::error('Error pinging updated device: ' . $e->getMessage(), [
                    'device_id' => $device->id,
                    'ip_address' => $device->ip_address
                ]);
            }
        }

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $this->authorize('delete devices');
        
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
