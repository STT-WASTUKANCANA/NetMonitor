<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('view dashboard');
        
        // Debug: Check if current user can view users
        $canViewUsers = auth()->user()?->can('view users');
        
        // Get current date and time information
        $currentDateTime = now();
        $currentDate = $currentDateTime->format('l, d F Y'); // Day, date Month Year format
        $currentDay = $currentDateTime->format('l'); // Full day name
        $currentDateShort = $currentDateTime->format('d/m/Y'); // Day/Month/Year format
        $currentTime = $currentDateTime->format('H:i:s'); // 24-hour time format
        
        // Get statistics for dashboard
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'up')->count();
        $downDevices = Device::where('status', 'down')->count();
        $activeAlerts = Alert::where('status', 'active')->count();
        
        // Get recent alerts
        $recentAlerts = Alert::with('device')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get main devices (Utama) with their sub-devices
        $mainDevices = Device::with(['children', 'logs'])
            ->where('type', 'Utama')
            ->get();
            
        // Get some statistics for chart (last 7 days)
        $deviceStatusData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $deviceStatusData[] = [
                'date' => $date->format('Y-m-d'),
                'up_count' => Device::where('status', 'up')->whereDate('last_checked_at', $date)->count(),
                'down_count' => Device::where('status', 'down')->whereDate('last_checked_at', $date)->count(),
            ];
        }
        
        return view('dashboard', compact(
            'totalDevices', 
            'activeDevices', 
            'downDevices', 
            'activeAlerts', 
            'recentAlerts', 
            'mainDevices',
            'deviceStatusData',
            'currentDate',
            'currentDay',
            'currentDateShort',
            'currentTime',
            'canViewUsers'
        ));
    }
    
    /**
     * Get real-time dashboard data via AJAX
     */
    public function getRealTimeData()
    {
        $this->authorize('view dashboard');
        
        // Get current date and time information
        $currentDateTime = now();
        $currentDate = $currentDateTime->format('l, d F Y'); // Day, date Month Year format
        $currentDay = $currentDateTime->format('l'); // Full day name
        $currentDateShort = $currentDateTime->format('d/m/Y'); // Day/Month/Year format
        $currentTime = $currentDateTime->format('H:i:s'); // 24-hour time format
        
        // Get updated statistics
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'up')->count();
        $downDevices = Device::where('status', 'down')->count();
        $activeAlerts = Alert::where('status', 'active')->count();
        
        // Get main devices with their current status
        $mainDevices = Device::with(['children'])
            ->where('type', 'Utama')
            ->get()
            ->map(function ($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $device->ip_address,
                    'status' => $device->status,
                    'last_checked' => $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never',
                    'children' => $device->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'status' => $child->status,
                            'ip_address' => $child->ip_address,
                        ];
                    }),
                ];
            });
        
        return response()->json([
            'totalDevices' => $totalDevices,
            'activeDevices' => $activeDevices,
            'downDevices' => $downDevices,
            'activeAlerts' => $activeAlerts,
            'mainDevices' => $mainDevices,
            'currentDateTime' => [
                'full' => $currentDate,
                'date' => $currentDateShort,
                'day' => $currentDay,
                'time' => $currentTime,
                'timestamp' => $currentDateTime->toISOString(),
            ],
        ]);
    }
}
