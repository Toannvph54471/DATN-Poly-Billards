<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillTimeUsage;
use App\Models\Product;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    // Hiện thị
    public function index(Request $request)
    {
        $query = Table::query();
        $types = Table::select('type')->distinct()->pluck('type');
        if ($request->filled('search')) {
            $query->where('table_name', 'like', "%{$request->search}%")
                ->orWhere('table_number', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->paginate(10);

        return view('admin.tables.index', [
            'tables' => $tables,
            'types' => $types,
            'totalTables' => Table::count(),
            'inUseCount' => Table::where('status', 'in_use')->count(),
            'maintenanceCount' => Table::where('status', 'maintenance')->count(),
            'availableCount' => Table::where('status', 'available')->count(),
        ]);
    }
    // hien thi form sua 
    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }
    // xu ly update thong tin ban
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:50',
            'type' => 'required|string',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $table = Table::findOrFail($id);
        $table->update($request->only([
            'table_name',
            'table_number',
            'type',
            'hourly_rate',
            'status',
        ]));

        return redirect()->route('admin.tables.index')
            ->with('success', 'Cập nhật bàn thành công!');
    }


    public function create()
    {

        return view('admin.tables.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number|max:10',
            'table_name' => 'required|max:255|unique:tables,table_name',
            'type' => 'required|in:standard,vip,competition',
            'hourly_rate' => 'required|numeric|min:0',
        ], [
            'table_number.unique' => 'Mã bàn đã tồn tại.',
            'table_name.unique' => 'Tên bàn đã tồn tại trong hệ thống.',
        ]);

        Table::create([
            'table_number' => $request->table_number,
            'table_name' => $request->table_name,
            'type' => $request->type,
            'status' => Table::STATUS_AVAILABLE,
            'hourly_rate' => $request->hourly_rate,
        ]);

        return redirect()->route('admin.tables.create')->with('success', 'Thêm bàn mới thành công!');
    }

    // Xóa mềm
    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bàn'], 404);
        }

        $table->delete();

        return response()->json(['success' => true]);
    }


    public function trashed()
    {
        $tables = Table::onlyTrashed()->get();
        return view('admin.tables.trashed', compact('tables'));
    }

    public function restore($id)
    {
        $table = Table::onlyTrashed()->where('id', $id)->firstOrFail();
        $table->restore();
        return redirect()->route('admin.tables.trashed')->with('success', 'Khôi phục bàn thành công!');
    }

    public function detail($id)
    {
        $table = Table::findOrFail($id);
        $products = Product::all();

        // Lấy phiên sử dụng hiện tại (nếu có)
        $currentUsage = BillTimeUsage::whereHas('bill', function ($query) use ($id) {
            $query->where('table_id', $id);
        })
            ->whereNull('end_time')
            ->first();

        // Lấy lịch sử sử dụng
        $usageHistory = BillTimeUsage::whereHas('bill', function ($query) use ($id) {
            $query->where('table_id', $id);
        })
            ->whereNotNull('end_time')
            ->orderBy('start_time', 'desc')
            ->get();

        // Tính tổng thống kê
        $totalMinutes = $usageHistory->sum('duration');
        $totalRevenue = $usageHistory->sum('total_cost');

        return view('admin.tables.detail', compact(
            'table',
            'currentUsage',
            'usageHistory',
            'totalMinutes',
            'totalRevenue',
            'products'
        ));
    }


    public function startSession(Request $request, $id)
    {
        try {
            $table = Table::findOrFail($id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn không khả dụng để bắt đầu');
            }

            $bill = Bill::create([
                'bill_number' => 'BILL-' . now()->format('Ymd-His') . '-' . $table->id . '-' . rand(100, 999),
                'table_id' => $table->id,
                'staff_id' => 1,
                'start_time' => now(),
                'status' => 'in_progress',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0,
                'payment_status' => 'unpaid'
            ]);

            // Sử dụng DB facade
            DB::table('bill_time_usage')->insert([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'end_time' => now(),
                'duration_minutes' => 0,
                'hourly_rate' => $table->hourly_rate,
                'total_price' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $table->update([
                'status' => 'in_use',
                'current_bill_id' => $bill->id
            ]);

            return redirect()->back()->with('success', 'Bắt đầu sử dụng bàn thành công. Bill: ' . $bill->bill_number);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    public function stopSession(Request $request, $id)
    {
        try {
            // Tìm bàn
            $table = Table::findOrFail($id);

            if ($table->status !== 'in_use' || !$table->id) {
                return redirect()->back()->with('error', 'Bàn hiện không đang được sử dụng.');
            }

            // Tìm bill đang hoạt động (chưa thanh toán)
            $bill = Bill::where('table_id', $table->id)
                ->where('status', 'in_progress')
                ->latest()
                ->firstOrFail();

            // Lấy record thời gian đang sử dụng
            $timeUsage = DB::table('bill_time_usage')
                ->where('bill_id', $bill->id)
                ->orderByDesc('id')
                ->first();

            if (!$timeUsage) {
                return redirect()->back()->with('error', 'Không tìm thấy thời gian sử dụng để dừng.');
            }

            // Tính thời gian sử dụng (phút)
            $startTime = Carbon::parse($timeUsage->start_time);
            $endTime = now();
            $durationMinutes = $endTime->diffInMinutes($startTime);

            // Tính tiền theo giờ
            $hours = $durationMinutes / 60;
            $totalPrice = $hours * $table->hourly_rate;

            // Cập nhật lại bill_time_usage
            DB::table('bill_time_usage')
                ->where('id', $timeUsage->id)
                ->update([
                    'end_time' => $endTime,
                    'duration_minutes' => $durationMinutes,
                    'total_price' => $totalPrice,
                    'updated_at' => now(),
                ]);

            // Cập nhật tổng tiền bill
            $bill->update([
                'status' => 'completed',
                'total_amount' => $totalPrice,
                'final_amount' => $totalPrice, // chưa tính giảm giá
                'payment_status' => 'unpaid', // hoặc paid nếu bạn thanh toán luôn
            ]);

            // Cập nhật trạng thái bàn
            $table->update([
                'status' => 'available',
                'current_bill_id' => null,
            ]);

            return redirect()->back()->with('success', 'Đã dừng sử dụng bàn thành công. Tổng tiền: ' . number_format($totalPrice, 0, ',', '.') . 'đ');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
