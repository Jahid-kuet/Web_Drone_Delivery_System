<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryRequest;
use App\Models\Drone;
use App\Models\Hospital;
use App\Models\MedicalSupply;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $stats = $this->getStatistics();
        $recentActivities = $this->getRecentActivities();
        $charts = $this->getChartData();
        
        return view('admin.dashboard', compact('stats', 'recentActivities', 'charts'));
    }

    /**
     * Get key statistics for dashboard
     */
    private function getStatistics(): array
    {
        return [
            // Delivery Statistics
            'total_deliveries' => Delivery::count(),
            'active_deliveries' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
            'completed_deliveries' => Delivery::where('status', 'completed')->count(),
            'failed_deliveries' => Delivery::where('status', 'failed')->count(),
            'today_deliveries' => Delivery::whereDate('created_at', today())->count(),
            'emergency_deliveries' => Delivery::whereHas('deliveryRequest', function ($q) {
                $q->where('priority', 'emergency');
            })->whereIn('status', ['pending', 'in_transit'])->count(),
            
            // Delivery Request Statistics
            'pending_requests' => DeliveryRequest::where('status', 'pending')->count(),
            'approved_requests' => DeliveryRequest::where('status', 'approved')->count(),
            'rejected_requests' => DeliveryRequest::where('status', 'rejected')->count(),
            
            // Drone Statistics
            'total_drones' => Drone::count(),
            'available_drones' => Drone::where('status', 'available')->count(),
            'active_drones' => Drone::where('status', 'in_flight')->count(),
            'maintenance_drones' => Drone::where('status', 'maintenance')->count(),
            'charging_drones' => Drone::where('status', 'charging')->count(),
            'low_battery_drones' => Drone::where('battery_level', '<', 30)->count(),
            
            // Medical Supply Statistics
            'total_supplies' => MedicalSupply::count(),
            'low_stock_supplies' => MedicalSupply::where('quantity_in_stock', '<=', DB::raw('minimum_stock_level'))->count(),
            'out_of_stock_supplies' => MedicalSupply::where('quantity_in_stock', 0)->count(),
            'expiring_soon_supplies' => MedicalSupply::whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
            'expired_supplies' => MedicalSupply::where('expiry_date', '<', now())->count(),
            
            // Hospital Statistics
            'total_hospitals' => Hospital::count(),
            'active_hospitals' => Hospital::where('status', 'active')->count(),
            
            // User Statistics
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'drone_pilots' => User::whereHas('roles', function ($q) {
                $q->where('name', 'drone_operator');
            })->count(),
            
            // Performance Metrics
            'avg_delivery_time' => Delivery::where('status', 'completed')
                ->whereNotNull('actual_delivery_time')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, actual_delivery_time)')),
            'success_rate' => $this->calculateSuccessRate(),
            'on_time_delivery_rate' => $this->calculateOnTimeRate(),
        ];
    }

    /**
     * Calculate delivery success rate
     */
    private function calculateSuccessRate(): float
    {
        $total = Delivery::whereIn('status', ['completed', 'failed'])->count();
        if ($total === 0) return 100.0;
        
        $completed = Delivery::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calculate on-time delivery rate
     */
    private function calculateOnTimeRate(): float
    {
        $total = Delivery::where('status', 'completed')
            ->whereNotNull('estimated_delivery_time')
            ->whereNotNull('actual_delivery_time')
            ->count();
        
        if ($total === 0) return 100.0;
        
        $onTime = Delivery::where('status', 'completed')
            ->whereRaw('actual_delivery_time <= estimated_delivery_time')
            ->count();
        
        return round(($onTime / $total) * 100, 2);
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities(int $limit = 20): array
    {
        $activities = [];
        
        // Recent deliveries
        $recentDeliveries = Delivery::with(['deliveryRequest.hospital', 'drone'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($delivery) {
                return [
                    'type' => 'delivery',
                    'icon' => 'truck',
                    'color' => $this->getDeliveryColor($delivery->status),
                    'title' => "Delivery #{$delivery->tracking_number}",
                    'description' => "Status: {$delivery->status} - {$delivery->deliveryRequest->hospital->name}",
                    'time' => $delivery->created_at,
                ];
            });
        
        // Recent audit logs
        $recentLogs = AuditLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $userName = $log->user ? $log->user->name : 'System';
                return [
                    'type' => 'audit',
                    'icon' => 'file-text',
                    'color' => $this->getAuditColor($log->severity),
                    'title' => ucfirst($log->action),
                    'description' => "{$userName} - {$log->auditable_type}",
                    'time' => $log->created_at,
                ];
            });
        
        // Merge and sort by time
        $activities = collect($recentDeliveries)
            ->merge($recentLogs)
            ->sortByDesc('time')
            ->take($limit)
            ->values()
            ->all();
        
        return $activities;
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData(): array
    {
        return [
            'deliveries_chart' => $this->getDeliveriesTrendData(),
            'status_distribution' => $this->getStatusDistribution(),
            'priority_distribution' => $this->getPriorityDistribution(),
            'drone_utilization' => $this->getDroneUtilization(),
            'hourly_deliveries' => $this->getHourlyDeliveries(),
        ];
    }

    /**
     * Get deliveries trend data (last 30 days)
     */
    private function getDeliveriesTrendData(): array
    {
        $data = Delivery::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $data->pluck('total')->toArray(),
                    'color' => 'blue',
                ],
                [
                    'label' => 'Completed',
                    'data' => $data->pluck('completed')->toArray(),
                    'color' => 'green',
                ],
                [
                    'label' => 'Failed',
                    'data' => $data->pluck('failed')->toArray(),
                    'color' => 'red',
                ],
            ],
        ];
    }

    /**
     * Get delivery status distribution
     */
    private function getStatusDistribution(): array
    {
        $data = Delivery::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        return [
            'labels' => $data->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->toArray(),
            'data' => $data->pluck('count')->toArray(),
            'colors' => $data->pluck('status')->map(fn($s) => $this->getDeliveryColor($s))->toArray(),
        ];
    }

    /**
     * Get priority distribution
     */
    private function getPriorityDistribution(): array
    {
        $data = DeliveryRequest::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get();
        
        return [
            'labels' => $data->pluck('priority')->map(fn($p) => ucfirst($p))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get drone utilization data
     */
    private function getDroneUtilization(): array
    {
        $total = Drone::count();
        $data = Drone::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        return [
            'labels' => $data->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->toArray(),
            'data' => $data->pluck('count')->toArray(),
            'percentages' => $data->pluck('count')->map(fn($c) => $total > 0 ? round(($c / $total) * 100, 1) : 0)->toArray(),
        ];
    }

    /**
     * Get hourly deliveries (today)
     */
    private function getHourlyDeliveries(): array
    {
        $data = Delivery::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Fill missing hours with 0
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = 0;
        }
        
        foreach ($data as $item) {
            $hourlyData[$item->hour] = $item->count;
        }
        
        return [
            'labels' => array_map(fn($h) => sprintf('%02d:00', $h), array_keys($hourlyData)),
            'data' => array_values($hourlyData),
        ];
    }

    /**
     * Get color for delivery status
     */
    private function getDeliveryColor(string $status): string
    {
        return match ($status) {
            'completed', 'delivered' => 'green',
            'in_transit', 'picked_up', 'pending' => 'blue',
            'on_hold', 'awaiting_pickup' => 'yellow',
            'failed', 'cancelled', 'returned' => 'red',
            'emergency' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get color for audit severity
     */
    private function getAuditColor(string $severity): string
    {
        return match ($severity) {
            'critical' => 'red',
            'error' => 'red',
            'warning' => 'yellow',
            'info' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get real-time statistics (AJAX endpoint)
     */
    public function realtimeStats()
    {
        return response()->json([
            'active_deliveries' => Delivery::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count(),
            'available_drones' => Drone::where('status', 'available')->count(),
            'pending_requests' => DeliveryRequest::where('status', 'pending')->count(),
            'emergency_deliveries' => Delivery::whereHas('deliveryRequest', function ($q) {
                $q->where('priority', 'emergency');
            })->whereIn('status', ['pending', 'in_transit'])->count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Export dashboard data
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $stats = $this->getStatistics();
        
        // Implementation would depend on export library (PDF, Excel, etc.)
        // For now, return JSON
        return response()->json($stats);
    }
}
