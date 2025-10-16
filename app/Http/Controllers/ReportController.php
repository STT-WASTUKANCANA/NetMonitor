<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the reports page
     */
    public function index()
    {
        $this->authorize('generate reports');
        
        $devices = Device::orderBy('name')->get();
        
        return view('reports.index', compact('devices'));
    }

    /**
     * Get chart data for response time trends
     */
    public function getResponseTimeData(Request $request)
    {
        $this->authorize('generate reports');
        
        $deviceIds = $request->input('device_ids', []);
        $range = $request->input('range', '7'); // default to 7 days
        
        $startDate = now()->subDays($range)->startOfDay();
        
        if (empty($deviceIds)) {
            // If no specific devices selected, get data for all devices
            $logs = DeviceLog::where('checked_at', '>=', $startDate)
                ->select(DB::raw('DATE(checked_at) as date, AVG(response_time) as avg_response_time'))
                ->groupBy(DB::raw('DATE(checked_at)'))
                ->orderBy('date')
                ->get();
        } else {
            // Get data for specific devices
            $logs = DeviceLog::whereIn('device_id', $deviceIds)
                ->where('checked_at', '>=', $startDate)
                ->select(DB::raw('DATE(checked_at) as date, AVG(response_time) as avg_response_time'))
                ->groupBy(DB::raw('DATE(checked_at)'))
                ->orderBy('date')
                ->get();
        }

        $dates = [];
        $responseTimes = [];
        
        foreach ($logs as $log) {
            $dates[] = $log->date;
            $responseTimes[] = $log->avg_response_time ? round($log->avg_response_time, 2) : 0;
        }

        return response()->json([
            'dates' => $dates,
            'response_times' => $responseTimes,
        ]);
    }

    /**
     * Get chart data for device status trends
     */
    public function getStatusData(Request $request)
    {
        $this->authorize('generate reports');
        
        $deviceIds = $request->input('device_ids', []);
        $range = $request->input('range', '7'); // default to 7 days
        
        $startDate = now()->subDays($range)->startOfDay();
        
        if (empty($deviceIds)) {
            // Get status data for all devices
            $logs = DeviceLog::where('checked_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(checked_at) as date'),
                    DB::raw("SUM(CASE WHEN status = 'up' THEN 1 ELSE 0 END) as up_count"),
                    DB::raw("SUM(CASE WHEN status = 'down' THEN 1 ELSE 0 END) as down_count"),
                    DB::raw("COUNT(*) as total_count")
                )
                ->groupBy(DB::raw('DATE(checked_at)'))
                ->orderBy('date')
                ->get();
        } else {
            // Get status data for specific devices
            $logs = DeviceLog::whereIn('device_id', $deviceIds)
                ->where('checked_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(checked_at) as date'),
                    DB::raw("SUM(CASE WHEN status = 'up' THEN 1 ELSE 0 END) as up_count"),
                    DB::raw("SUM(CASE WHEN status = 'down' THEN 1 ELSE 0 END) as down_count"),
                    DB::raw("COUNT(*) as total_count")
                )
                ->groupBy(DB::raw('DATE(checked_at)'))
                ->orderBy('date')
                ->get();
        }

        $dates = [];
        $upCounts = [];
        $downCounts = [];
        
        foreach ($logs as $log) {
            $dates[] = $log->date;
            $upCounts[] = $log->up_count;
            $downCounts[] = $log->down_count;
        }

        return response()->json([
            'dates' => $dates,
            'up_counts' => $upCounts,
            'down_counts' => $downCounts,
        ]);
    }
    
    /**
     * Generate PDF report
     */
    public function generatePdf(Request $request)
    {
        $this->authorize('generate reports');
        
        $deviceIds = $request->input('device_ids', []);
        $range = $request->input('range', '7'); // default to 7 days
        
        $startDate = now()->subDays($range)->startOfDay();
        $endDate = now();
        
        // Get device information
        if (empty($deviceIds)) {
            $devices = Device::all();
            $logs = DeviceLog::whereBetween('checked_at', [$startDate, $endDate])
                ->with('device')
                ->get();
            $alerts = Alert::whereBetween('created_at', [$startDate, $endDate])
                ->with('device')
                ->get();
        } else {
            $devices = Device::whereIn('id', $deviceIds)->get();
            $logs = DeviceLog::whereIn('device_id', $deviceIds)
                ->whereBetween('checked_at', [$startDate, $endDate])
                ->with('device')
                ->get();
            $alerts = Alert::whereIn('device_id', $deviceIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('device')
                ->get();
        }
        
        // Calculate statistics
        $totalLogs = $logs->count();
        $upLogs = $logs->where('status', 'up')->count();
        $downLogs = $logs->where('status', 'down')->count();
        $avgResponseTime = $logs->avg('response_time') ?? 0;
        
        $reportData = [
            'devices' => $devices,
            'logs' => $logs,
            'alerts' => $alerts,
            'totalLogs' => $totalLogs,
            'upLogs' => $upLogs,
            'downLogs' => $downLogs,
            'avgResponseTime' => round($avgResponseTime, 2),
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'range' => $range
            ],
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ];
        
        $pdf = Pdf::loadView('reports.pdf', $reportData);
        
        return $pdf->download('monitoring-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
