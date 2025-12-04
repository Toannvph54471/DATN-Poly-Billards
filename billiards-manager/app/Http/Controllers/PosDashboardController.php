<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class PosDashboardController extends Controller
{

    public function posDashboard()
    {
        try {
            $user = FacadesAuth::user();
            
            // Thống kê nhanh cho POS
            $stats = [
                'open_bills' => Bill::where('status', 'Open')->count(),
                'today_revenue' => Bill::whereDate('created_at', Carbon::today())
                    ->where('status', 'Closed')
                    ->sum('final_amount'),
                'occupied_tables' => Table::where('status', 'occupied')->count(),
                'available_tables' => Table::where('status', 'available')->count(),
                'pending_reservations' => Reservation::where('status', 'confirmed')
                    ->whereDate('reservation_time', Carbon::today())
                    ->count(),
            ];

            // Bills đang mở
            $openBills = Bill::with(['table', 'user', 'staff'])
                ->where('status', 'Open')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Bàn trống
            $availableTables = Table::where('status', 'available')->get();

            // Bàn đang sử dụng với thông tin bill chi tiết
            $occupiedTables = Table::with([
                'tableRate',
                'currentBill.user',
                'currentBill.billDetails.product',
                'currentBill.billDetails.combo',
                'currentBill.billTimeUsages' => function ($query) {
                    $query->whereNull('end_time')
                        ->orWhere('end_time', '>', now()->subHours(24));
                },
                'currentBill.comboTimeUsages' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_expired', false)
                            ->orWhere('end_time', '>', now()->subHours(24));
                    });
                }
            ])->where('status', 'occupied')->get();

                

            return view('admin.pos-dashboard', compact(
                'stats',
                'openBills',
                'availableTables',
                'occupiedTables',
                'todayReservations'
            ));

            
        } catch (\Exception $e) {
            // Fallback data nếu có lỗi
            return $this->getFallbackData();
        }
    }

    public function getQuickStats()
    {
        $stats = [
            'open_bills' => Bill::where('status', 'Open')->count(),
            'today_sales' => Bill::whereDate('created_at', Carbon::today())
                ->where('status', 'Closed')
                ->sum('final_amount'),
            'occupied_tables' => Table::where('status', 'occupied')->count(),
            'pending_reservations' => Reservation::where('status', 'confirmed')
                ->whereDate('reservation_time', Carbon::today())
                ->count(),
        ];

        return response()->json($stats);
    }

    private function getFallbackData()
    {
        return view('admin.pos-dashboard', [
            'stats' => [
                'open_bills' => 0,
                'today_revenue' => 0,
                'occupied_tables' => 0,
                'available_tables' => 0,
                'pending_reservations' => 0,
            ],
            'openBills' => collect(),
            'availableTables' => collect(),
            'occupiedTables' => collect(),
            'todayReservations' => collect(),
        ]);
    }
}
