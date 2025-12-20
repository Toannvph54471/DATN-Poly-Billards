<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosDashboardController extends Controller
{
    /**
     * Hiển thị POS Dashboard - chỉ thống kê số bàn
     */
    public function posDashboard()
    {
        try {
            // Lấy thống kê bàn
            $tableStats = $this->getTableStats();
            
            // Lấy danh sách bàn với thông tin hóa đơn hiện tại
            $tables = DB::table('tables')
                ->select(
                    'tables.id',
                    'tables.table_number',
                    'tables.table_name',
                    'tables.capacity',
                    'tables.status',
                    DB::raw('(SELECT bills.id FROM bills WHERE bills.table_id = tables.id AND bills.status IN ("Open", "Paused") ORDER BY bills.created_at DESC LIMIT 1) as current_bill'),
                    DB::raw('(SELECT bills.start_time FROM bills WHERE bills.table_id = tables.id AND bills.status IN ("Open", "Paused") ORDER BY bills.created_at DESC LIMIT 1) as start_time')
                )
                ->whereNull('tables.deleted_at')
                ->orderBy('tables.table_number')
                ->get();
            
            // Thêm thông tin khách hàng cho các bàn đang dùng
            foreach ($tables as $table) {
                if ($table->current_bill) {
                    $customer = DB::table('bills')
                        ->join('users', 'bills.user_id', '=', 'users.id')
                        ->where('bills.id', $table->current_bill)
                        ->select('users.name as customer_name')
                        ->first();
                    
                    if ($customer) {
                        $table->customer_name = $customer->customer_name;
                    }
                }
            }
            
            // Lấy bàn trống
            $availableTables = $tables->where('status', 'available');
            
            // Lấy bàn đang dùng
            $occupiedTables = $tables->where('status', 'occupied');
            
            // Lấy hóa đơn đang mở với thông tin chi tiết
            $openBills = DB::table('bills')
                ->select(
                    'bills.id',
                    'bills.bill_number',
                    'bills.table_id',
                    'tables.table_name',
                    'bills.start_time',
                    'bills.total_amount',
                    'users.name as customer_name'
                )
                ->join('tables', 'bills.table_id', '=', 'tables.id')
                ->leftJoin('users', 'bills.user_id', '=', 'users.id')
                ->where('bills.status', 'Open')
                ->orderBy('bills.updated_at', 'desc')
                ->limit(5)
                ->get();
            
            return view('admin.pos-dashboard', [
                'tableStats' => $tableStats['tableStats'],
                'tables' => $tables,
                'availableTables' => $availableTables,
                'occupiedTables' => $occupiedTables,
                'openBills' => $openBills,
                'totalTables' => $tableStats['tableStats']['total'],
                'availableCount' => $tableStats['tableStats']['available'],
                'occupiedCount' => $tableStats['tableStats']['occupied'],
                'reservedCount' => $tableStats['tableStats']['reserved'],
                'maintenanceCount' => $tableStats['tableStats']['maintenance'],
            ]);
            
        } catch (\Exception $e) {
            \Log::error('POS Dashboard Error: ' . $e->getMessage());
            return view('admin.pos-dashboard', $this->getFallbackData());
        }
    }
    
    /**
     * Lấy thống kê bàn
     */
    private function getTableStats()
    {
        $stats = DB::table('tables')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied,
                SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) as reserved,
                SUM(CASE WHEN status = "maintenance" THEN 1 ELSE 0 END) as maintenance,
                SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available
            ')
            ->whereNull('deleted_at')
            ->first();

        $total = $stats->total ?? 0;
        $used = ($stats->occupied ?? 0) + ($stats->reserved ?? 0);

        return [
            'tableStats' => [
                'total' => $total,
                'occupied' => $stats->occupied ?? 0,
                'reserved' => $stats->reserved ?? 0,
                'available' => $stats->available ?? 0,
                'maintenance' => $stats->maintenance ?? 0,
                'occupancy_rate' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
            ]
        ];
    }
    
    /**
     * API để lấy thống kê nhanh (dùng cho AJAX refresh)
     */
    public function getQuickStats()
    {
        try {
            $tableStats = $this->getTableStats();
            
            return response()->json([
                'success' => true,
                'tableStats' => $tableStats['tableStats'],
                'open_bills' => DB::table('bills')->where('status', 'Open')->count(),
                'updated_at' => now()->format('H:i:s'),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting stats'
            ], 500);
        }
    }
    
    /**
     * Dữ liệu fallback
     */
    private function getFallbackData()
    {
        return [
            'tableStats' => [
                'total' => 0,
                'occupied' => 0,
                'reserved' => 0,
                'available' => 0,
                'maintenance' => 0,
                'occupancy_rate' => 0,
            ],
            'tables' => collect(),
            'availableTables' => collect(),
            'occupiedTables' => collect(),
            'openBills' => collect(),
            'totalTables' => 0,
            'availableCount' => 0,
            'occupiedCount' => 0,
            'reservedCount' => 0,
            'maintenanceCount' => 0,
        ];
    }
}