<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NetworkMetricsController extends Controller
{
    /**
     * Get overall network metrics
     */
    public function getNetworkMetrics(Request $request): JsonResponse
    {
        $period = $request->get('period', '24h'); // 24h, 7d, 30d, 90d
        $endDate = Carbon::now();
        
        switch ($period) {
            case '24h':
                $startDate = Carbon::now()->subDay();
                $interval = '1 hour';
                break;
            case '7d':
                $startDate = Carbon::now()->subWeek();
                $interval = '6 hours';
                break;
            case '30d':
                $startDate = Carbon::now()->subMonth();
                $interval = '1 day';
                break;
            case '90d':
                $startDate = Carbon::now()->subMonths(3);
                $interval = '1 day';
                break;
            default:
                $startDate = Carbon::now()->subWeek();
                $interval = '6 hours';
                break;
        }

        // Get overall metrics
        $totalDevices = Device::count();
        $onlineDevices = Device::where('status', 'up')->count();
        $offlineDevices = Device::where('status', 'down')->count();
        
        // Calculate average response time
        $avgResponseTime = DeviceLog::whereBetween('checked_at', [$startDate, $endDate])
            ->avg('response_time') ?? 0;
        
        // Get device status timeline
        $statusTimeline = DeviceLog::selectRaw(
            'DATE_FORMAT(checked_at, "%Y-%m-%d %H:00:00") as time_bucket, 
            AVG(response_time) as avg_response_time,
            COUNT(CASE WHEN status = "up" THEN 1 END) as up_count,
            COUNT(CASE WHEN status = "down" THEN 1 END) as down_count,
            COUNT(*) as total_checks'
        )
        ->whereBetween('checked_at', [$startDate, $endDate])
        ->groupBy('time_bucket')
        ->orderBy('time_bucket')
        ->get()
        ->map(function ($item) {
            return [
                'timestamp' => $item->time_bucket,
                'avg_response_time' => $item->avg_response_time ? round($item->avg_response_time, 2) : 0,
                'up_count' => (int)$item->up_count,
                'down_count' => (int)$item->down_count,
                'total_checks' => (int)$item->total_checks,
            ];
        });

        // Get top devices by response time
        $topResponseTimes = DeviceLog::join('devices', 'device_logs.device_id', '=', 'devices.id')
            ->selectRaw('devices.name, AVG(device_logs.response_time) as avg_response_time')
            ->whereBetween('device_logs.checked_at', [$startDate, $endDate])
            ->whereNotNull('device_logs.response_time')
            ->groupBy('devices.id', 'devices.name')
            ->orderByDesc('avg_response_time')
            ->limit(10)
            ->get();

        return response()->json([
            'summary' => [
                'total_devices' => $totalDevices,
                'online_devices' => $onlineDevices,
                'offline_devices' => $offlineDevices,
                'online_percentage' => $totalDevices > 0 ? round(($onlineDevices / $totalDevices) * 100, 2) : 0,
                'average_response_time' => round($avgResponseTime, 2),
                'period' => $period,
                'start_date' => $startDate->toISOString(),
                'end_date' => $endDate->toISOString(),
            ],
            'timeline' => $statusTimeline,
            'top_response_times' => $topResponseTimes,
        ]);
    }

    /**
     * Get metrics for a specific device
     */
    public function getDeviceMetrics(Request $request, Device $device): JsonResponse
    {
        $period = $request->get('period', '24h'); // 24h, 7d, 30d, 90d
        $endDate = Carbon::now();
        
        switch ($period) {
            case '24h':
                $startDate = Carbon::now()->subDay();
                $interval = '15 minutes';
                break;
            case '7d':
                $startDate = Carbon::now()->subWeek();
                $interval = '2 hours';
                break;
            case '30d':
                $startDate = Carbon::now()->subMonth();
                $interval = '6 hours';
                break;
            case '90d':
                $startDate = Carbon::now()->subMonths(3);
                $interval = '1 day';
                break;
            default:
                $startDate = Carbon::now()->subWeek();
                $interval = '2 hours';
                break;
        }

        // Get device logs for the period
        $logs = DeviceLog::where('device_id', $device->id)
            ->whereBetween('checked_at', [$startDate, $endDate])
            ->orderBy('checked_at')
            ->get();

        // Calculate device-specific metrics
        $upTimePercentage = 0;
        if ($logs->count() > 0) {
            $upLogs = $logs->where('status', 'up')->count();
            $upTimePercentage = round(($upLogs / $logs->count()) * 100, 2);
        }

        $avgResponseTime = $logs->whereNotNull('response_time')->avg('response_time') ?? 0;
        $minResponseTime = $logs->whereNotNull('response_time')->min('response_time') ?? 0;
        $maxResponseTime = $logs->whereNotNull('response_time')->max('response_time') ?? 0;

        // Group data by time intervals for chart
        $chartData = collect();
        $intervalLogs = $logs->groupBy(function ($log) use ($interval) {
            switch ($interval) {
                case '15 minutes':
                    return $log->checked_at->format('Y-m-d H:i');
                case '2 hours':
                    $hour = floor($log->checked_at->hour / 2) * 2;
                    return $log->checked_at->format('Y-m-d ') . sprintf('%02d:00', $hour);
                case '6 hours':
                    $hour = floor($log->checked_at->hour / 6) * 6;
                    return $log->checked_at->format('Y-m-d ') . sprintf('%02d:00', $hour);
                case '1 day':
                default:
                    return $log->checked_at->format('Y-m-d');
            }
        });

        foreach ($intervalLogs as $time => $groupedLogs) {
            $upLogs = $groupedLogs->where('status', 'up');
            $downLogs = $groupedLogs->where('status', 'down');
            
            $chartData->push([
                'timestamp' => $time,
                'up_count' => $upLogs->count(),
                'down_count' => $downLogs->count(),
                'avg_response_time' => $upLogs->whereNotNull('response_time')->avg('response_time') ? 
                    round($upLogs->whereNotNull('response_time')->avg('response_time'), 2) : null,
                'min_response_time' => $upLogs->whereNotNull('response_time')->min('response_time') ?: null,
                'max_response_time' => $upLogs->whereNotNull('response_time')->max('response_time') ?: null,
            ]);
        }

        return response()->json([
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'ip_address' => $device->ip_address,
                'type' => $device->type,
                'status' => $device->status,
                'location' => $device->location,
                'last_checked_at' => $device->last_checked_at,
            ],
            'metrics' => [
                'up_time_percentage' => $upTimePercentage,
                'average_response_time' => round($avgResponseTime, 2),
                'min_response_time' => $minResponseTime,
                'max_response_time' => $maxResponseTime,
                'total_logs' => $logs->count(),
                'period' => $period,
                'start_date' => $startDate->toISOString(),
                'end_date' => $endDate->toISOString(),
            ],
            'chart_data' => $chartData->values(),
        ]);
    }

    /**
     * Get real-time response time data for the dashboard chart
     */
    public function getRealTimeResponseTimeData(Request $request): JsonResponse
    {
        // Get the last 60 seconds of data for the chart
        $startDate = Carbon::now()->subMinutes(1);
        $logs = DeviceLog::where('checked_at', '>=', $startDate)
            ->select('checked_at', 'response_time', 'status')
            ->orderBy('checked_at')
            ->get();

        $timeline = $logs->where('status', 'up')->map(function ($log) {
            return [
                'timestamp' => $log->checked_at->format('H:i:s'),
                'response_time' => $log->response_time,
            ];
        })->values();

        return response()->json([
            'timeline' => $timeline,
            'current_timestamp' => Carbon::now()->toISOString(),
            'total_points' => count($timeline),
        ]);
    }
}
