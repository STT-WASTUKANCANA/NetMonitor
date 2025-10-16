<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlertController extends Controller
{
    /**
     * Get all alerts with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alert::with('device');
        
        // Filter by status
        if ($request->has('status') && in_array($request->status, ['active', 'resolved'])) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date')) {
            $startDate = Carbon::parse($request->start_date);
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }
        
        // Filter by device
        if ($request->has('device_id')) {
            $query->where('device_id', $request->device_id);
        }
        
        $alerts = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
            
        return response()->json($alerts);
    }

    /**
     * Get unresolved alerts
     */
    public function getUnresolved(): JsonResponse
    {
        $alerts = Alert::with('device')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($alerts);
    }

    /**
     * Resolve an alert
     */
    public function resolve(Alert $alert): JsonResponse
    {
        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Alert resolved successfully',
            'alert' => $alert->load('device')
        ]);
    }
}
