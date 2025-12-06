<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class PosDashboardController extends Controller
{
    public function posDashboard()
    {
        try {
            $user = FacadesAuth::user();

            // Thống kê nhanh cho POS - Sửa key names để match với view
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

            // Đặt bàn hôm nay
            $todayReservations = Reservation::with(['table', 'customer'])
                ->whereDate('reservation_time', Carbon::today())
                ->where('status', 'confirmed')
                ->orderBy('reservation_time')
                ->get();

            // Tính toán thống kê thêm
            $allTables = Table::count();
            $availableCount = $stats['available_tables'];
            $occupiedCount = $stats['occupied_tables'];
            $reservedCount = $stats['pending_reservations'];
            $maintenanceCount = $allTables - ($availableCount + $occupiedCount + $reservedCount);
            $maintenanceCount = max(0, $maintenanceCount);

            return view('admin.pos-dashboard', compact(
                'stats',
                'openBills',
                'availableTables',
                'occupiedTables',
                'todayReservations',
                'allTables',
                'availableCount',
                'occupiedCount',
                'reservedCount',
                'maintenanceCount'
            ));
        } catch (\Exception $e) {
            Log::error('POS Dashboard Error: ' . $e->getMessage());
            return $this->getFallbackData();
        }
    }

    /**
     * Lấy vị trí bàn từ localStorage hoặc database
     * Helper function cho view
     */
    public static function getTablePosition($tableId)
    {
        // Trong thực tế, bạn nên lưu vị trí vào database
        // Ở đây tôi tạo vị trí mặc định dựa trên ID

        // Tạo vị trí dựa trên ID để có tính nhất quán
        $baseX = 50;
        $baseY = 50;
        $spacingX = 220;
        $spacingY = 120;

        // Tạo layout grid 3x3
        $row = intval(($tableId - 1) / 3);
        $col = ($tableId - 1) % 3;

        return [
            'x' => $baseX + ($col * $spacingX),
            'y' => $baseY + ($row * $spacingY)
        ];
    }

    /**
     * API để lưu vị trí bàn
     */
    public function saveTablePositions(Request $request)
    {
        try {
            $positions = $request->input('positions');

            // Lưu vào database
            foreach ($positions as $tableId => $position) {
                Table::where('id', $tableId)->update([
                    'position_x' => $position['x'],
                    'position_y' => $position['y'],
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu vị trí bàn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu vị trí: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API để lấy vị trí bàn
     */
    public function getTablePositions()
    {
        try {
            $positions = Table::select('id', 'position_x', 'position_y')
                ->whereNotNull('position_x')
                ->whereNotNull('position_y')
                ->get()
                ->mapWithKeys(function ($table) {
                    return [
                        $table->id => [
                            'x' => $table->position_x,
                            'y' => $table->position_y
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy vị trí'
            ], 500);
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
            'formattedTables' => collect(),
            'openBills' => collect(),
            'availableTables' => collect(),
            'occupiedTables' => collect(),
            'todayReservations' => collect(),
        ]);
    }
}
