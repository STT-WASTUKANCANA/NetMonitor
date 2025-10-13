<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
        
        // Get all active devices that can be parents (any device can potentially be a parent)
        $parentDevices = Device::active()
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:router,switch,access_point,server,other',
            'hierarchy_level' => 'required|in:utama,sub,device',
            'parent_id' => 'nullable|exists:devices,id',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Device::create($request->all());

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
        
        // Get all active devices that can be parents (excluding the current device to prevent circular reference)
        $parentDevices = Device::active()
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:router,switch,access_point,server,other',
            'hierarchy_level' => 'required|in:utama,sub,device',
            'parent_id' => 'nullable|exists:devices,id',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $device->update($request->all());

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
