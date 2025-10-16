<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Log;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get network overview report
     */
    public function getOverview(Request $request): JsonResponse
    {
        $period = $request->get('period', '7d'); // 24h, 7d, 30d, 90d, 1y
        $endDate = Carbon::now();
        
        switch ($period) {
            case '24h':
                $startDate = Carbon::now()->subDay();
                break;
            case '7d':
                $startDate = Carbon::now()->subWeek();
                break;
            case '30d':
                $startDate = Carbon::now()->subMonth();
                break;
            case '90d':
                $startDate = Carbon::now()->subMonths(3);
                break;
            case '1y':
                $startDate = Carbon::now()->subYear();
                break;
            default:
                $startDate = Carbon::now()->subWeek();
                break;
        }

        // Get device statistics
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'up')->count();
        $inactiveDevices = Device::where('status', 'down')->count();
        
        // Calculate uptime percentage
        $uptimePercentage = $totalDevices > 0 ? round(($activeDevices / $totalDevices) * 100, 2) : 0;
        
        // Get log statistics for the period
        $logsForPeriod = Log::whereBetween('checked_at', [$startDate, $endDate])->get();
        $totalChecks = $logsForPeriod->count();
        $successfulChecks = $logsForPeriod->where('status', 'up')->count();
        
        $avgResponseTime = $logsForPeriod->whereNotNull('response_time')->avg('response_time') ?? 0;
        $minResponseTime = $logsForPeriod->whereNotNull('response_time')->min('response_time') ?? 0;
        $maxResponseTime = $logsForPeriod->whereNotNull('response_time')->max('response_time') ?? 0;
        
        // Get alert statistics for the period
        $alertsForPeriod = Alert::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalAlerts = $alertsForPeriod->count();
        $resolvedAlerts = $alertsForPeriod->where('status', 'resolved')->count();
        $activeAlerts = $totalAlerts - $resolvedAlerts;
        
        // Top devices with most alerts
        $topAlertDevices = Alert::join('devices', 'alerts.device_id', '=', 'devices.id')
            ->selectRaw('devices.name, devices.ip_address, COUNT(alerts.id) as alert_count')
            ->whereBetween('alerts.created_at', [$startDate, $endDate])
            ->groupBy('devices.id', 'devices.name', 'devices.ip_address')
            ->orderByDesc('alert_count')
            ->limit(10)
            ->get();
        
        // Status distribution over time
        $statusTrend = Log::selectRaw(
            'DATE(checked_at) as date, 
            COUNT(CASE WHEN status = "up" THEN 1 END) as up_count,
            COUNT(CASE WHEN status = "down" THEN 1 END) as down_count,
            COUNT(*) as total_count'
        )
        ->whereBetween('checked_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return response()->json([
            'period' => $period,
            'start_date' => $startDate->toISOString(),
            'end_date' => $endDate->toISOString(),
            'summary' => [
                'total_devices' => $totalDevices,
                'active_devices' => $activeDevices,
                'inactive_devices' => $inactiveDevices,
                'uptime_percentage' => $uptimePercentage,
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'success_rate' => $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 2) : 0,
                'average_response_time' => round($avgResponseTime, 2),
                'min_response_time' => $minResponseTime,
                'max_response_time' => $maxResponseTime,
                'total_alerts' => $totalAlerts,
                'resolved_alerts' => $resolvedAlerts,
                'active_alerts' => $activeAlerts,
            ],
            'top_alert_devices' => $topAlertDevices,
            'status_trend' => $statusTrend,
        ]);
    }

    /**
     * Generate detailed report (will be used for PDF generation)
     */
    public function generate(Request $request) 
    {
        $request->validate([
            'report_type' => 'required|in:summary,detailed,historical',
            'period' => 'required|in:24h,7d,30d,90d,1y',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'device_id' => 'nullable|exists:devices,id',
            'format' => 'nullable|in:json,pdf' // Added format parameter
        ]);
        
        $reportType = $request->report_type;
        $period = $request->period;
        $deviceId = $request->device_id;
        $format = $request->get('format', 'json'); // Default to JSON
        
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date) 
            : match($period) {
                '24h' => Carbon::now()->subDay(),
                '7d' => Carbon::now()->subWeek(),
                '30d' => Carbon::now()->subMonth(),
                '90d' => Carbon::now()->subMonths(3),
                '1y' => Carbon::now()->subYear(),
                default => Carbon::now()->subWeek(),
            };
        
        // Base query for logs
        $logsQuery = Log::whereBetween('checked_at', [$startDate, $endDate]);
        if ($deviceId) {
            $logsQuery->where('device_id', $deviceId);
        }
        
        $logs = $logsQuery->get();
        $totalChecks = $logs->count();
        $successfulChecks = $logs->where('status', 'up')->count();
        $avgResponseTime = $logs->whereNotNull('response_time')->avg('response_time') ?? 0;
        
        // Build the report data based on type
        $reportData = [
            'report_type' => $reportType,
            'period' => $period,
            'start_date' => $startDate->toISOString(),
            'end_date' => $endDate->toISOString(),
            'device_id' => $deviceId,
            'summary' => [
                'total_checks' => $totalChecks,
                'successful_checks' => $successfulChecks,
                'failed_checks' => $totalChecks - $successfulChecks,
                'success_rate' => $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 2) : 0,
                'average_response_time' => round($avgResponseTime, 2),
            ],
        ];
        
        if ($reportType === 'detailed' || $reportType === 'historical') {
            // Add more detailed information
            $reportData['detailed'] = [
                'response_time_stats' => [
                    'average' => round($avgResponseTime, 2),
                    'min' => $logs->whereNotNull('response_time')->min('response_time') ?? 0,
                    'max' => $logs->whereNotNull('response_time')->max('response_time') ?? 0,
                    'median' => $this->calculateMedian($logs->whereNotNull('response_time')->pluck('response_time')->toArray()),
                ],
                'uptime_trend' => $this->getUptimeTrend($startDate, $endDate, $deviceId),
                'top_issue_devices' => $this->getTopIssueDevices($startDate, $endDate),
            ];
        }
        
        // If format is PDF, generate and return PDF
        if ($format === 'pdf') {
            return $this->generatePDF($reportData);
        }
        
        return response()->json([
            'success' => true,
            'report_data' => $reportData,
            'generated_at' => now()->toISOString(),
        ]);
    }
    
    /**
     * Generate PDF report
     */
    private function generatePDF(array $reportData)
    {
        // Create the PDF using Laravel's view functionality
        $pdf = app('dompdf.wrapper');
        
        // Get view with report data
        $pdfContent = view('reports.pdf', [
            'reportData' => $reportData,
            'title' => 'Network Monitoring Report'
        ])->render();
        
        $pdf->loadHTML($pdfContent);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Download the PDF
        return $pdf->download('network-monitoring-report-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Helper to calculate median
     */
    private function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0;
        }
        
        sort($values);
        $count = count($values);
        
        if ($count % 2 == 0) {
            return ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
        }
        
        return $values[floor($count / 2)];
    }
    
    /**
     * Get uptime trend grouped by day
     */
    private function getUptimeTrend(Carbon $startDate, Carbon $endDate, ?int $deviceId = null): array
    {
        $query = Log::selectRaw(
            'DATE(checked_at) as date, 
            COUNT(CASE WHEN status = "up" THEN 1 END) as up_count,
            COUNT(CASE WHEN status = "down" THEN 1 END) as down_count,
            COUNT(*) as total_count'
        )
        ->whereBetween('checked_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date');
        
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }
        
        return $query->get()->map(function ($item) {
            return [
                'date' => $item->date,
                'up_count' => (int)$item->up_count,
                'down_count' => (int)$item->down_count,
                'total_count' => (int)$item->total_count,
                'uptime_percentage' => $item->total_count > 0 ? round(($item->up_count / $item->total_count) * 100, 2) : 0,
            ];
        })->toArray();
    }
    
    /**
     * Get devices with the most issues
     */
    private function getTopIssueDevices(Carbon $startDate, Carbon $endDate): array
    {
        return Alert::join('devices', 'alerts.device_id', '=', 'devices.id')
            ->selectRaw('devices.name, devices.ip_address, devices.type, COUNT(alerts.id) as alert_count')
            ->whereBetween('alerts.created_at', [$startDate, $endDate])
            ->groupBy('devices.id', 'devices.name', 'devices.ip_address', 'devices.type')
            ->orderByDesc('alert_count')
            ->limit(10)
            ->get()
            ->toArray();
    }
}
