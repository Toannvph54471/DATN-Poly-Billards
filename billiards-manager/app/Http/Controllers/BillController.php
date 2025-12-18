<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Table;
use App\Models\User;
use App\Models\Combo;
use App\Models\Product;
use App\Models\ComboTimeUsage;
use App\Models\BillTimeUsage;
use App\Models\BillDetail;
use App\Models\TableRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function index()
    {
        // Lấy filters từ session (nếu có)
        $filters = session('bill_filters', []);

        // Lấy dữ liệu cho dropdown
        $tables = Table::orderBy('table_name')->get();
        $staff = User::where('role_id', 3)->orderBy('name')->get();

        // Thực hiện query nếu có filters
        $bills = $this->buildQuery($filters)->paginate(20);

        // Thống kê nhanh
        $stats = $this->getStats();

        return view('admin.bills.index', compact(
            'bills',
            'filters',
            'tables',
            'staff',
            'stats'
        ));
    }

    /**
     * Xử lý tìm kiếm với POST
     */
    public function filter(Request $request)
    {
        // Lưu filters vào session
        session(['bill_filters' => $request->except(['_token'])]);

        // Redirect về trang chính
        return redirect()->route('admin.bills.index');
    }

    /**
     * Reset bộ lọc
     */
    public function resetFilter(Request $request)
    {
        // Xóa filters khỏi session
        $request->session()->forget('bill_filters');

        // Redirect về trang chính
        return redirect()->route('admin.bills.index');
    }

    public function checkNewBills(Request $request)
    {
        $lastCheck = $request->input('last_check', now()->subMinutes(10)->toISOString());

        // Lấy hóa đơn mới (trong vòng 5 phút) đã thanh toán
        $newBills = Bill::with(['table', 'staff', 'user'])
            ->where('created_at', '>', $lastCheck)
            ->where('payment_status', 'Paid')
            ->where('created_at', '>', now()->subMinutes(5)) // Chỉ lấy trong 5 phút
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'user_name' => $bill->user->name ?? null,
                    'user_phone' => $bill->user->phone ?? null,
                    'table_name' => $bill->table->table_name ?? null,
                    'table_number' => $bill->table->table_number ?? null,
                    'staff_name' => $bill->staff->name ?? null,
                    'staff_code' => $bill->staff->code ?? null,
                    'total_amount' => $bill->total_amount,
                    'discount_amount' => $bill->discount_amount,
                    'final_amount' => $bill->final_amount,
                    'status' => $bill->status,
                    'payment_status' => $bill->payment_status,
                    'created_at' => $bill->created_at->toISOString(),
                ];
            });

        return response()->json([
            'new_bills_count' => $newBills->count(),
            'new_bills' => $newBills,
            'current_time' => now()->toISOString(),
        ]);
    }


    /**
     * Xây dựng query dựa trên filters
     */
    private function buildQuery($filters)
    {
        $query = Bill::with([
            'table',
            'staff',
            'billTimeUsages',
            'billDetails.product',
            'user'
        ]);

        // Tìm kiếm theo từ khóa
        if (!empty($filters['query'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('bill_number', 'LIKE', "%{$filters['query']}%")
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('phone', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('email', 'LIKE', "%{$filters['query']}%");
                    })
                    ->orWhereHas('staff', function ($staffQuery) use ($filters) {
                        $staffQuery->where('name', 'LIKE', "%{$filters['query']}%");
                    })
                    ->orWhereHas('table', function ($tableQuery) use ($filters) {
                        $tableQuery->where('table_name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('table_number', 'LIKE', "%{$filters['query']}%");
                    })
                    ->orWhere('note', 'LIKE', "%{$filters['query']}%");
            });
        }

        // Lọc theo trạng thái hóa đơn
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Lọc theo trạng thái thanh toán
        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Lọc theo ngày bắt đầu
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }

        // Lọc theo ngày kết thúc
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        // Lọc theo khoảng tiền tối thiểu
        if (!empty($filters['min_amount'])) {
            $query->where('total_amount', '>=', $filters['min_amount']);
        }

        // Lọc theo khoảng tiền tối đa
        if (!empty($filters['max_amount'])) {
            $query->where('total_amount', '<=', $filters['max_amount']);
        }

        // Lọc theo bàn
        if (!empty($filters['table_id'])) {
            $query->where('table_id', $filters['table_id']);
        }

        // Lọc theo nhân viên
        if (!empty($filters['staff_id'])) {
            $query->where('staff_id', $filters['staff_id']);
        }

        // Sắp xếp
        $sortBy = $filters['sort_by'] ?? 'created_at_desc';
        switch ($sortBy) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'updated_at_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'updated_at_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'total_amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'total_amount_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'bill_number_desc':
                $query->orderBy('bill_number', 'desc');
                break;
            case 'bill_number_asc':
                $query->orderBy('bill_number', 'asc');
                break;
            default:
                $query->latest();
        }

        return $query;
    }

    /**
     * Lấy thống kê
     */
    private function getStats()
    {
        return [
            'total' => Bill::count(),
            'open' => Bill::where('status', 'Open')->count(),
            'paid' => Bill::where('payment_status', 'Paid')->count(),
            'today' => Bill::whereDate('created_at', today())->count(),
            'total_amount_today' => Bill::whereDate('created_at', today())->sum('total_amount'),
        ];
    }

    // Hàm hiển thị chi tiết hóa đơn
    public function show($id)
    {
        $bill = Bill::with([
            'table.tableRate',
            'user',
            'staff', // Nhân viên tạo hóa đơn
            'billTimeUsages',
            'billDetails.product.category',
            'billDetails.combo.comboItems.product',
            'billDetails.addedByUser', // Load thông tin nhân viên đã thêm
            'payments',
            'promotion'
        ])
            ->findOrFail($id);

        return view('admin.bills.show', compact('bill'));
    }

    public function createBill(Request $request)
    {
        $request->validate([
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo user
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => null,
                        'password' => null,
                        'role_id' => 4,
                        'status' => 'Active'
                    ]
                );

                // CẬP NHẬT SỐ LẦN GHÉ QUA - THÊM ĐOẠN NÀY
                $user->increment('total_visits');

                // Hoặc nếu bạn muốn cập nhật customer_type dựa trên số lần ghé qua
                $this->updateCustomerType($user);
            }

            // Tạo bill number
            $billNumber = 'BILL' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Lấy hourly rate
            $hourlyRate = $this->getTableHourlyRate($table);

            // Tạo bill
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $table->id,
                'user_id' => $user?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'Open',
                'payment_status' => 'Pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Khởi tạo tính giờ
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $table->id)
                ->with('success', 'Tạo hóa đơn tính giờ thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Create bill error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    // THÊM HÀM NÀY ĐỂ CẬP NHẬT LOẠI KHÁCH HÀNG
    private function updateCustomerType(User $user)
    {
        $visitCount = $user->total_visits;

        if ($visitCount >= 10) {
            $user->customer_type = 'VIP';
        } elseif ($visitCount >= 5) {
            $user->customer_type = 'Regular';
        } elseif ($visitCount >= 1) {
            $user->customer_type = 'Returning';
        } else {
            $user->customer_type = 'New';
        }

        $user->save();
    }

    /**
     * Tạo bill bàn lẻ (không tính giờ)
     */
    public function createQuickBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo user
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => $request->user_phone . '@customer.com',
                        'password' => bcrypt(Str::random(8)),
                        'role_id' => 4,
                        'status' => 'Active'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'QUICK' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill với status là 'quick'
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $table->id,
                'user_id' => $user?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'quick',
                'payment_status' => 'Pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $table->id)
                ->with('success', 'Tạo hóa đơn bàn lẻ thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Thêm sản phẩm vào bill
     */
    public function addProductToBill(Request $request, $billId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $product = Product::findOrFail($request->product_id);

            // Kiểm tra tồn kho
            if ($product->stock_quantity < $request->quantity) {
                return redirect()->back()->with('error', "Sản phẩm {$product->name} không đủ tồn kho. Còn: {$product->stock_quantity}");
            }

            // Thêm sản phẩm vào bill
            BillDetail::create([
                'bill_id' => $bill->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'original_price' => $product->price,
                'total_price' => $product->price * $request->quantity,
                'added_by' => Auth::id(), // Thêm dòng này
                'added_at' => now(), // Đảm bảo có dòng này
                'is_combo_component' => false
            ]);

            // Cập nhật tồn kho
            $product->decrement('stock_quantity', $request->quantity);

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm sản phẩm thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Xóa sản phẩm khỏi bill (chỉ áp dụng cho sản phẩm thông thường, không phải thành phần combo)
     */
    public function removeProductFromBill(Request $request, $billId, $billDetailId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $billDetail = BillDetail::where('id', $billDetailId)
                ->where('bill_id', $billId)
                ->firstOrFail();

            $currentUser = Auth::user();
            $currentUserId = $currentUser->id;
            $currentUserRole = $currentUser->role_id;

            // 1. Kiểm tra xem bill có đang ở trạng thái có thể xóa sản phẩm không
            if (!in_array($bill->status, ['Open', 'quick'])) {
                return redirect()->back()->with('error', 'Chỉ có thể xóa sản phẩm khỏi bill đang mở');
            }

            // 2. Kiểm tra quyền xóa - Chỉ người thêm sản phẩm mới được xóa
            $addedBy = $billDetail->added_by;

            // Admin (role_id = 1) có quyền xóa tất cả
            $isAdmin = ($currentUserRole == 1);

            // Manager (role_id = 2) có quyền xóa tất cả
            $isManager = ($currentUserRole == 2);

            // Kiểm tra xem current user có phải là người thêm sản phẩm không
            $isAddedByCurrentUser = ($addedBy == $currentUserId);

            // Trường hợp đặc biệt: nếu added_by là null, cho phép nhân viên tạo bill được xóa
            $isBillCreator = ($addedBy === null && $bill->staff_id == $currentUserId);

            // Kiểm tra quyền
            if (!$isAdmin && !$isManager && !$isAddedByCurrentUser && !$isBillCreator) {
                // Lấy thông tin người thêm sản phẩm để hiển thị thông báo
                $addedByUser = null;
                if ($addedBy) {
                    $addedByUser = User::find($addedBy);
                }

                $addedByName = $addedByUser ? $addedByUser->name : 'Nhân viên khác';

                return redirect()->back()->with(
                    'error',
                    "Bạn không có quyền xóa sản phẩm này. Sản phẩm được thêm bởi: {$addedByName}"
                );
            }

            // 3. KHÔNG cho phép xóa nếu là thành phần của combo
            if ($billDetail->is_combo_component) {
                return redirect()->back()->with('error', 'Không thể xóa sản phẩm là thành phần của combo');
            }

            // 4. KHÔNG cho phép xóa nếu là combo
            if ($billDetail->combo_id) {
                return redirect()->back()->with('error', 'Không thể xóa combo bằng chức năng này. Vui lòng sử dụng chức năng xóa combo.');
            }

            // 5. Chỉ xử lý với sản phẩm thông thường
            if ($billDetail->product_id) {
                // Hoàn trả tồn kho
                $product = Product::find($billDetail->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $billDetail->quantity);
                    Log::info("Restored stock for product: {$product->name}, quantity: {$billDetail->quantity}, restored by user: {$currentUserId}");
                }
            }

            // 6. Xóa bản ghi bill detail
            $billDetail->delete();

            // 7. Cập nhật lại tổng tiền bill
            $this->calculateBillTotal($bill);

            DB::commit();

            // Ghi log hành động xóa
            Log::info("Product removed from bill by user {$currentUserId} (Role: {$currentUserRole}): BillDetail ID {$billDetailId}, Added by: {$addedBy}");

            return redirect()->back()->with('success', 'Xóa sản phẩm khỏi bill thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Remove product from bill error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Thêm combo vào bill
     */
    public function addComboToBill(Request $request, $billId)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Kiểm tra nếu là bàn lẻ thì không cho thêm combo
            if ($bill->status === 'quick') {
                return redirect()->back()->with('error', 'Bàn lẻ không thể thêm combo');
            }

            $combo = Combo::with('comboItems.product')->findOrFail($request->combo_id);

            // Kiểm tra tồn kho cho tất cả sản phẩm trong combo
            foreach ($combo->comboItems as $item) {
                if ($item->product && $item->product->stock_quantity < ($item->quantity * $request->quantity)) {
                    return redirect()->back()->with(
                        'error',
                        "{$item->product->name} không đủ tồn kho. Cần: " . ($item->quantity * $request->quantity)
                    );
                }
            }

            // Thêm combo vào bill details
            $comboDetail = BillDetail::create([
                'bill_id' => $bill->id,
                'combo_id' => $combo->id,
                'quantity' => $request->quantity,
                'unit_price' => $combo->price,
                'original_price' => $combo->actual_value,
                'total_price' => $combo->price * $request->quantity,
                'is_combo_component' => false,
                'added_by' => Auth::id(), // Thêm dòng này
                'added_at' => now() // Thêm dòng này
            ]);

            // Xử lý các sản phẩm trong combo
            foreach ($combo->comboItems as $item) {
                if ($item->product_id) {
                    BillDetail::create([
                        'bill_id' => $bill->id,
                        'product_id' => $item->product_id,
                        'parent_bill_detail_id' => $comboDetail->id,
                        'quantity' => $item->quantity * $request->quantity,
                        'unit_price' => 0,
                        'original_price' => $item->product->price,
                        'total_price' => 0,
                        'is_combo_component' => true,
                        'added_by' => Auth::id(), // Thêm dòng này
                        'added_at' => now() // Thêm dòng này
                    ]);

                    // Cập nhật tồn kho
                    $item->product->decrement('stock_quantity', $item->quantity * $request->quantity);
                }
            }

            // Xử lý combo có giờ chơi
            if ($combo->is_time_combo && $combo->play_duration_minutes) {
                $this->activateComboTime($bill, $combo, $comboDetail);
            }

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm combo thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm combo: ' . $e->getMessage());
        }
    }


    /**
     * DỪNG COMBO TIME - KHI HẾT THỜI GIAN HOẶC NHÂN VIÊN DỪNG THỦ CÔNG
     */
    public function stopComboTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Tìm combo time đang chạy hoặc đang tạm dừng
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->first();

            if (!$activeComboTime) {
                return redirect()->back()->with('error', 'Không tìm thấy combo thời gian đang chạy');
            }

            // Tính thời gian đã sử dụng nếu đang chạy
            if (is_null($activeComboTime->end_time)) {
                $start = Carbon::parse($activeComboTime->start_time);
                $elapsedMinutes = $start->diffInMinutes(now());
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);
            } else {
                // Đang tạm dừng, sử dụng remaining_minutes hiện tại
                $remainingMinutes = $activeComboTime->remaining_minutes;
            }

            // Dừng combo time
            $activeComboTime->update([
                'end_time' => now(),
                'remaining_minutes' => $remainingMinutes,
                'is_expired' => true
            ]);

            // Cập nhật trạng thái bill nếu cần
            $bill->refresh();

            DB::commit();

            return redirect()->back()->with('success', 'Đã dừng combo thời gian. Bạn có thể bật giờ thường nếu cần.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error stopping combo time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi dừng combo: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển từ combo time sang tính giờ thường
     */
    public function switchToRegularTime(Request $request, $billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::with('table')->findOrFail($billId);

            // Kiểm tra trạng thái bill
            if ($bill->status !== 'Open') {
                return redirect()->back()->with('error', 'Chỉ có thể chuyển sang giờ thường cho bill đang mở');
            }

            // Kiểm tra xem có combo time đã hết hạn không
            $expiredComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', true)
                ->first();

            if (!$expiredComboTime) {
                return redirect()->back()->with('error', 'Combo thời gian chưa kết thúc hoặc không tìm thấy');
            }

            // Kiểm tra xem đã có giờ thường đang chạy chưa
            $existingRegularTime = BillTimeUsage::where('bill_id', $billId)
                ->whereNull('end_time')
                ->first();

            if ($existingRegularTime) {
                return redirect()->back()->with('error', 'Đã có giờ thường đang chạy');
            }

            // Bắt đầu tính giờ thường
            $hourlyRate = $this->getTableHourlyRate($bill->table);

            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            // Cập nhật lại tổng tiền bill
            $this->calculateBillTotal($bill);
            $bill->refresh();

            DB::commit();

            return redirect()->route('admin.tables.detail', $bill->table_id)
                ->with('success', 'Đã chuyển sang tính giờ thường thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error switching to regular time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi chuyển sang giờ thường: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra và cập nhật trạng thái combo time real-time
     */
    public function checkComboTimeStatus($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);

            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->where('remaining_minutes', '>', 0)
                ->whereNull('end_time')
                ->first();

            if ($activeComboTime) {
                $start = Carbon::parse($activeComboTime->start_time);
                $elapsedMinutes = $start->diffInMinutes(now());
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                // Format thời gian ở server-side
                $hours = floor($remainingMinutes / 60);
                $minutes = $remainingMinutes % 60;

                $formattedTime = '';
                if ($hours > 0) {
                    $formattedTime .= $hours . 'h';
                }
                if ($minutes > 0) {
                    $formattedTime .= ($hours > 0 ? ' ' : '') . $minutes . 'p';
                }
                if ($remainingMinutes === 0) {
                    $formattedTime = '0p';
                }

                $isNearEnd = $remainingMinutes <= 10 && $remainingMinutes > 0;

                return response()->json([
                    'has_active_combo' => true,
                    'remaining_minutes' => $remainingMinutes,
                    'formatted_time' => $formattedTime, // Thêm formatted time
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'is_near_end' => $isNearEnd,
                    'is_expired' => false,
                    'needs_switch' => false,
                    'elapsed_minutes' => $elapsedMinutes,
                    'mode' => 'combo'
                ]);
            }

            return response()->json([
                'has_active_combo' => false,
                'remaining_minutes' => 0,
                'formatted_time' => '0p',
                'hours' => 0,
                'minutes' => 0,
                'is_near_end' => false,
                'is_expired' => false,
                'needs_switch' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking combo time: ' . $e->getMessage());
            return response()->json([
                'has_active_combo' => false,
                'remaining_minutes' => 0,
                'formatted_time' => 'Lỗi',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Bắt đầu tính giờ từ bàn lẻ
     */
    public function startPlaying($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Chỉ cho phép nếu bill đang là quick
            if ($bill->status !== 'quick') {
                return redirect()->back()->with('error', 'Chỉ có thể bắt đầu tính giờ từ bàn lẻ');
            }

            // Chuyển từ quick sang Open
            $bill->update([
                'status' => 'Open'
            ]);

            // Bắt đầu tính giờ thường
            $hourlyRate = $this->getTableHourlyRate($bill->table);
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Đã bắt đầu tính giờ chơi');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật tổng tiền bill
     */
    public function updateBillTotal($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);
            $this->calculateBillTotal($bill);
            $bill->refresh();

            return redirect()->back()->with('success', 'Đã cập nhật tổng tiền');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật tổng tiền: ' . $e->getMessage());
        }
    }

    private function stopAllTimeUsage(Bill $bill)
    {
        // Dừng regular time chưa kết thúc
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->get();

        foreach ($activeRegularTime as $timeUsage) {
            $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
            $effectiveMinutes = $elapsedMinutes - ($timeUsage->paused_duration ?? 0);

            $timeUsage->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes)
            ]);
        }

        // Dừng combo time chưa kết thúc
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->whereNull('end_time')
            ->get();

        foreach ($activeComboTime as $comboTime) {
            $elapsedMinutes = Carbon::parse($comboTime->start_time)->diffInMinutes(now());

            $comboTime->update([
                'end_time' => now(),
                'total_minutes' => $comboTime->total_minutes,
                'remaining_minutes' => max(0, $comboTime->total_minutes - $elapsedMinutes),
                'is_expired' => true
            ]);
        }
    }

    /**
     * Kích hoạt combo time
     */
    private function activateComboTime(Bill $bill, Combo $combo, BillDetail $comboDetail)
    {
        // Tính toán và lưu giá trị giờ thường đã sử dụng trước khi chuyển sang combo
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            // Tính thời gian đã sử dụng giờ thường
            $elapsedMinutes = $this->calculateElapsedMinutes($activeRegularTime);
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);
            $regularTimeCost = ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes);

            // Kết thúc session giờ thường và lưu giá trị
            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => $regularTimeCost
            ]);
        }

        // Tạo bản ghi theo dõi thời gian combo
        ComboTimeUsage::create([
            'combo_id' => $combo->id,
            'bill_id' => $bill->id,
            'table_id' => $bill->table_id,
            'start_time' => now(),
            'total_minutes' => $combo->play_duration_minutes,
            'remaining_minutes' => $combo->play_duration_minutes,
            'is_expired' => false
        ]);
    }

    /**
     * Tính tổng tiền bill
     */
    public function calculateBillTotal(Bill $bill)
    {
        // Tính tiền sản phẩm (không bao gồm thành phần combo)
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Tính tiền giờ - bao gồm cả đã kết thúc và đang chạy
        $timeTotal = $this->calculateTimeCharge($bill);

        $totalAmount = $productTotal + $timeTotal;
        $finalAmount = $totalAmount - $bill->discount_amount;

        $bill->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount
        ]);

        return $totalAmount;
    }

    /**
     * Tính tiền giờ chơi
     */
    public function calculateTimeCharge(Bill $bill)
    {
        $totalTimeCost = 0;

        // 1. Tính tiền giờ thường đã kết thúc (bao gồm cả khi chuyển sang combo)
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $totalTimeCost += $timeUsage->total_price ?? 0;
        }

        // 2. Tính tiền giờ thường đang chạy hoặc tạm dừng (nếu không có combo active)
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        // Chỉ tính giờ thường nếu không có combo active
        if (!$activeComboTime) {
            $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->get();

            foreach ($activeRegularTime as $timeUsage) {
                $timeCost = $this->calculateRoundedTimeCost($timeUsage);
                $totalTimeCost += $timeCost;
            }
        }

        return $totalTimeCost;
    }

    /**
     * Tính tiền giờ với làm tròn phút và tiền
     */
    private function calculateRoundedTimeCost(BillTimeUsage $timeUsage)
    {
        $tableRate = $timeUsage->bill->table->tableRate;

        // Lấy cấu hình làm tròn từ table_rate
        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
        $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
        $roundingAmount = $tableRate->rounding_amount ?? 1000;

        // Tính số phút đã sử dụng
        $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
        $effectiveMinutes = max($minChargeMinutes, $elapsedMinutes - ($timeUsage->paused_duration ?? 0));

        // Làm tròn số phút lên
        $roundedMinutes = ceil($effectiveMinutes / $roundingMinutes) * $roundingMinutes;

        // Tính tiền gốc
        $hourlyRate = $timeUsage->hourly_rate;
        $rawPrice = ($hourlyRate / 60) * $roundedMinutes;

        // Làm tròn tiền lên theo rounding_amount
        $finalPrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;

        // Cập nhật nếu cần (cho session đang chạy)
        if (is_null($timeUsage->end_time)) {
            $timeUsage->duration_minutes = $roundedMinutes;
            $timeUsage->total_price = $finalPrice;
            $timeUsage->save();
        }

        return $finalPrice;
    }

    /**
     * Lấy giá giờ của bàn
     */
    private function getTableHourlyRate(Table $table)
    {
        if ($table->table_rate_id) {
            $tableRate = TableRate::find($table->table_rate_id);
            if ($tableRate) {
                return $tableRate->hourly_rate;
            }
        }

        // Default rate nếu không tìm thấy
        return 50000.00;
    }

    /**
     * Tính số phút đã trôi qua
     */
    private function calculateElapsedMinutes(BillTimeUsage $timeUsage): int
    {
        try {
            if (is_null($timeUsage->end_time)) {
                // Session chưa kết thúc
                $start = Carbon::parse($timeUsage->start_time);

                if ($timeUsage->paused_at) {
                    // Đang tạm dừng - trả về thời gian đã chạy (paused_duration)
                    return (int) ($timeUsage->paused_duration ?? 0);
                } else {
                    // Đang chạy - tính từ start_time đến now
                    $elapsedMinutes = $start->diffInMinutes(now());
                    return $elapsedMinutes;
                }
            } else {
                // Session đã kết thúc - trả về duration_minutes
                return (int) ($timeUsage->duration_minutes ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('Error in calculateElapsedMinutes: ' . $e->getMessage(), [
                'time_usage_id' => $timeUsage->id,
                'paused_at' => $timeUsage->paused_at,
                'paused_duration' => $timeUsage->paused_duration,
                'end_time' => $timeUsage->end_time
            ]);
            return 0;
        }
    }
    private function calculateRegularTimeInfo($regularTime, $hourlyRate)
    {
        $isPaused = !is_null($regularTime->paused_at);

        if ($isPaused) {
            // Đang tạm dừng - sử dụng paused_duration đã lưu
            $isRunning = false;
            $effectiveMinutes = $regularTime->paused_duration ?? 0;
        } else {
            // Đang chạy - tính từ start_time đến now
            $start = Carbon::parse($regularTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());

            // KHÔNG trừ paused_duration nữa vì start_time đã được cập nhật khi resume
            $effectiveMinutes = $elapsedMinutes;
            $isRunning = true;
        }

        // Tính chi phí hiện tại
        $currentCost = max(0, $effectiveMinutes) * ($hourlyRate / 60);

        return [
            'is_running' => $isRunning,
            'mode' => 'regular',
            'elapsed_minutes' => (int) round($effectiveMinutes),
            'current_cost' => $currentCost,
            'hourly_rate' => $hourlyRate,
            'total_minutes' => 0,
            'remaining_minutes' => 0,
            'is_near_end' => false,
            'is_paused' => $isPaused,
            'paused_duration' => $regularTime->paused_duration ?? 0,
            'bill_status' => 'regular',
            'needs_switch' => false,
            'is_auto_stopped' => false
        ];
    }
    public function showTransferForm($billId)
    {
        try {
            $bill = Bill::with(['table', 'user', 'comboTimeUsages'])
                ->where('status', 'Open')
                ->where('payment_status', 'Pending')
                ->findOrFail($billId);

            // Kiểm tra nếu có combo time đang chạy
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->whereNull('end_time')
                ->first();

            // Lấy các bàn có thể chuyển đến
            // Nếu có combo đang chạy, chỉ hiển thị bàn cùng loại
            if ($activeComboTime) {
                $availableTables = Table::where('status', 'available')
                    ->where('id', '!=', $bill->table_id)
                    ->where('table_rate_id', $bill->table->table_rate_id) // CHỈ HIỂN THỊ BÀN CÙNG LOẠI
                    ->get();

                if ($availableTables->isEmpty()) {
                    return redirect()
                        ->route('admin.tables.index')
                        ->with('warning', 'Hiện tại không có bàn trống cùng loại để chuyển. Vui lòng chờ hoặc dừng combo trước.');
                }
            } else {
                $availableTables = Table::where('status', 'available')
                    ->where('id', '!=', $bill->table_id)
                    ->get();
            }

            return view('admin.bills.transfer', compact('bill', 'availableTables'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Không tìm thấy bill hoặc bill không hợp lệ');
        }
    }

    /**
     * Xử lý chuyển bàn
     */
    public function transferTable(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'target_table_id' => 'required|exists:tables,id'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Kiểm tra bill và bàn
                $bill = Bill::with(['table', 'billDetails', 'billTimeUsages', 'comboTimeUsages'])
                    ->where('status', 'Open')
                    ->where('payment_status', 'Pending')
                    ->findOrFail($request->bill_id);

                $sourceTable = $bill->table;
                $targetTable = Table::findOrFail($request->target_table_id);

                // 2. Kiểm tra combo time đang chạy
                $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
                    ->where('is_expired', false)
                    ->whereNull('end_time')
                    ->first();

                // 3. NẾU CÓ COMBO ĐANG CHẠY, CHỈ CHO PHÉP CHUYỂN SANG BÀN CÙNG LOẠI
                if ($activeComboTime && $sourceTable->table_rate_id !== $targetTable->table_rate_id) {
                    throw new \Exception('Không thể chuyển sang bàn khác loại khi đang sử dụng combo thời gian. Vui lòng chọn bàn cùng loại hoặc dừng combo trước.');
                }

                // 4. Kiểm tra bàn đích có trống không
                if ($targetTable->status !== 'available') {
                    throw new \Exception('Bàn đích đang được sử dụng hoặc bảo trì');
                }

                // 5. Kiểm tra không chuyển cùng bàn
                if ($sourceTable->id === $targetTable->id) {
                    throw new \Exception('Không thể chuyển cùng một bàn');
                }

                // 6. XỬ LÝ THỜI GIAN VÀ GIÁ CẢ TRƯỚC KHI CHUYỂN
                // Lấy giá giờ của bàn cũ và bàn mới
                $sourceHourlyRate = $this->getTableHourlyRate($sourceTable);
                $targetHourlyRate = $this->getTableHourlyRate($targetTable);

                // Xử lý giờ thường đang chạy
                $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                    ->whereNull('end_time')
                    ->first();

                if ($activeRegularTime) {
                    // Tính thời gian đã sử dụng ở bàn cũ
                    $startTime = Carbon::parse($activeRegularTime->start_time);
                    $elapsedMinutes = $startTime->diffInMinutes(now());
                    $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

                    // Tính tiền giờ đã sử dụng ở bàn cũ
                    $timeCost = ($sourceHourlyRate / 60) * max(0, $effectiveMinutes);

                    // Kết thúc session giờ thường ở bàn cũ
                    $activeRegularTime->update([
                        'end_time' => now(),
                        'duration_minutes' => $elapsedMinutes,
                        'total_price' => $timeCost
                    ]);

                    // Tạo session giờ thường mới ở bàn mới
                    BillTimeUsage::create([
                        'bill_id' => $bill->id,
                        'start_time' => now(),
                        'hourly_rate' => $targetHourlyRate
                    ]);
                }

                // Xử lý combo time đang chạy
                if ($activeComboTime) {
                    // Tính thời gian đã sử dụng của combo
                    $startTime = Carbon::parse($activeComboTime->start_time);
                    $elapsedMinutes = $startTime->diffInMinutes(now());
                    $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                    // Cập nhật thời gian còn lại và tạm dừng combo
                    $activeComboTime->update([
                        'end_time' => now(),
                        'remaining_minutes' => $remainingMinutes
                    ]);

                    // Chuyển combo sang bàn mới và tiếp tục
                    $activeComboTime->update([
                        'table_id' => $targetTable->id,
                        'start_time' => now(),
                        'end_time' => null
                    ]);
                }

                // 7. Cập nhật bill sang bàn mới
                $bill->update([
                    'table_id' => $targetTable->id,
                    'note' => $bill->note . " [Chuyển từ bàn {$sourceTable->table_number} lúc " . now()->format('H:i d/m/Y') . "]"
                ]);

                // 8. Cập nhật trạng thái bàn
                $sourceTable->update(['status' => 'available']);
                $targetTable->update(['status' => 'occupied']);

                // 9. Cập nhật tổng tiền bill
                $this->calculateBillTotal($bill);

                // 10. Log hoạt động
                Log::info('Chuyển bàn thành công', [
                    'bill_id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'source_table' => $sourceTable->table_number,
                    'target_table' => $targetTable->table_number,
                    'source_table_rate_id' => $sourceTable->table_rate_id,
                    'target_table_rate_id' => $targetTable->table_rate_id,
                    'has_active_combo' => (bool)$activeComboTime,
                    'source_hourly_rate' => $sourceHourlyRate,
                    'target_hourly_rate' => $targetHourlyRate,
                    'staff_id' => Auth::id()
                ]);

                return redirect()
                    ->route('admin.tables.index')
                    ->with('success', "Đã chuyển bàn {$sourceTable->table_number} → {$targetTable->table_number} thành công");
            });
        } catch (\Exception $e) {
            Log::error('Lỗi chuyển bàn', [
                'bill_id' => $request->bill_id,
                'target_table' => $request->target_table_id,
                'error' => $e->getMessage(),
                'staff_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Lỗi khi chuyển bàn: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý chuyển bàn với chi tiết thời gian
     */
    private function handleTableTransferTime(Bill $bill, Table $sourceTable, Table $targetTable)
    {
        $sourceHourlyRate = $this->getTableHourlyRate($sourceTable);
        $targetHourlyRate = $this->getTableHourlyRate($targetTable);

        $timeDetails = [
            'source_hourly_rate' => $sourceHourlyRate,
            'target_hourly_rate' => $targetHourlyRate,
            'transfer_time' => now(),
            'elapsed_minutes' => 0,
            'time_cost' => 0
        ];

        // Xử lý giờ thường đang chạy
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            // Tính thời gian đã sử dụng ở bàn cũ
            $startTime = Carbon::parse($activeRegularTime->start_time);
            $elapsedMinutes = $startTime->diffInMinutes(now());
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

            // Tính tiền giờ đã sử dụng ở bàn cũ
            $timeCost = ($sourceHourlyRate / 60) * max(0, $effectiveMinutes);

            // Kết thúc session giờ thường ở bàn cũ
            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => $timeCost
            ]);

            // Tạo session giờ thường mới ở bàn mới
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $targetHourlyRate,
                'note' => "Chuyển từ bàn {$sourceTable->table_number}"
            ]);

            $timeDetails['elapsed_minutes'] = $effectiveMinutes;
            $timeDetails['time_cost'] = $timeCost;
            $timeDetails['has_regular_time'] = true;
        }

        // Xử lý combo time đang chạy
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->whereNull('end_time')
            ->first();

        if ($activeComboTime) {
            // Tính thời gian đã sử dụng của combo
            $startTime = Carbon::parse($activeComboTime->start_time);
            $elapsedMinutes = $startTime->diffInMinutes(now());
            $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

            // Cập nhật thời gian còn lại và tạm dừng combo
            $activeComboTime->update([
                'end_time' => now(),
                'remaining_minutes' => $remainingMinutes
            ]);

            // Chuyển combo sang bàn mới và tiếp tục
            $activeComboTime->update([
                'table_id' => $targetTable->id,
                'start_time' => now(),
                'end_time' => null
            ]);

            $timeDetails['has_combo_time'] = true;
            $timeDetails['combo_remaining_minutes'] = $remainingMinutes;
        }

        return $timeDetails;
    }

    /**
     * In hóa đơn
     */
    public function printBill($id)
    {
        try {
            $bill = Bill::with([
                'table',
                'user',
                'billDetails.product',
                'billDetails.combo',
                'billTimeUsages',
                'comboTimeUsages.combo'
            ])->findOrFail($id);

            // Kiểm tra xem có phải là preview thanh toán không
            $isPreview = request()->has('preview');
            $paymentMethod = request()->get('payment_method');
            $finalAmount = request()->get('final_amount');

            if ($isPreview) {
                // Lấy thông tin từ session
                $paymentData = session('pending_payment_' . $id);

                if (!$paymentData) {
                    return redirect()
                        ->route('admin.payments.show', $id)
                        ->with('error', 'Thông tin thanh toán không tồn tại. Vui lòng thử lại.');
                }

                $timeCost = $paymentData['time_price'] ?? 0;
                $productTotal = $paymentData['product_total'] ?? 0;
                $totalAmount = $paymentData['total_amount'] ?? 0;
                $discountAmount = $paymentData['discount_amount'] ?? 0;
                $finalAmount = $paymentData['final_amount'] ?? 0;
                $paymentMethod = $paymentData['payment_method'] ?? 'cash';

                // Tính toán chi tiết thời gian cho display
                $timeDetails = $this->calculateTimeChargeDetailed($bill);
            } else {
                // Tính toán thông thường (xem bill đã thanh toán)
                $timeDetails = $this->calculateTimeChargeDetailed($bill);
                $timeCost = $timeDetails['totalCost'];
                $productTotal = BillDetail::where('bill_id', $bill->id)
                    ->where('is_combo_component', false)
                    ->sum('total_price');
                $totalAmount = $timeCost + $productTotal;
                $discountAmount = $bill->discount_amount ?? 0;
                $finalAmount = $totalAmount - $discountAmount;
                $paymentMethod = $bill->payment_method;
            }

            // Lấy thông tin khuyến mãi từ note
            $promotionInfo = $this->extractPromotionInfoFromNote($bill->note);

            // Tạo QR code
            $qrData = [
                'bill_number' => $bill->bill_number,
                'amount' => $finalAmount,
                'currency' => 'VND',
                'account' => '0368015218',
                'bank' => 'MBBank',
                'content' => "TT Bill {$bill->bill_number}"
            ];

            $qrUrl = "https://img.vietqr.io/image/MB-0368015218-qr_only.png?"
                . http_build_query([
                    'amount' => $finalAmount,
                    'addInfo' => "TT Bill {$bill->bill_number}"
                ]);

            // Dữ liệu cho bill
            $billData = [
                'bill' => $bill,
                'timeCost' => $timeCost,
                'timeDetails' => $timeDetails,
                'productTotal' => $productTotal,
                'totalAmount' => $totalAmount,
                'finalAmount' => $finalAmount,
                'discountAmount' => $discountAmount,
                'promotionInfo' => $promotionInfo,
                'printTime' => now()->format('H:i d/m/Y'),
                'staff' => Auth::user()->name,
                'qrUrl' => $qrUrl,
                'qrData' => $qrData,
                'isPreview' => $isPreview, // Thêm flag để biết là preview
                'paymentMethod' => $paymentMethod,
                'paymentData' => $isPreview ? $paymentData : null
            ];

            return view('admin.bills.print', $billData);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi in hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * In hóa đơn nhiều
     */
    public function printBillMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return back()->with('error', 'Không có hóa đơn nào được chọn.');
        }

        try {
            // Kiểm tra xem có phải là preview thanh toán không
            $isPreview = request()->has('preview');
            // Lấy tất cả bills theo mảng ID
            $bills = Bill::with([
                'table',
                'user',
                'billDetails.product',
                'billDetails.combo',
                'billTimeUsages',
                'comboTimeUsages.combo'
            ])
                ->whereIn('id', $ids)
                ->get();

            if ($bills->isEmpty()) {
                return back()->with('error', 'Không tìm thấy hóa đơn.');
            }

            $billsData = [];

            foreach ($bills as $bill) {
                if ($isPreview) {
                    // Lấy thông tin từ session cho từng bill
                    $paymentData = session('pending_payment_' . $bill->id);

                    if (!$paymentData) {
                        return redirect()
                            ->route('admin.payments.show', $bill->id)
                            ->with('error', 'Thông tin thanh toán không tồn tại cho bill ' . $bill->bill_number . '. Vui lòng thử lại.');
                    }

                    $timeCost = $paymentData['time_price'] ?? 0;
                    $productTotal = $paymentData['product_total'] ?? 0;
                    $totalAmount = $paymentData['total_amount'] ?? 0;
                    $discountAmount = $paymentData['discount_amount'] ?? 0;
                    $finalAmount = $paymentData['final_amount'] ?? 0;
                    $paymentMethod = $paymentData['payment_method'] ?? 'cash';

                    // Tính toán chi tiết thời gian cho display
                    $timeDetails = $this->calculateTimeChargeDetailed($bill);
                } else {
                    // Tính toán thông thường (xem bill đã thanh toán)
                    $timeDetails = $this->calculateTimeChargeDetailed($bill);
                    $timeCost = $timeDetails['totalCost'] ?? 0;

                    // Tổng SP/Combo (không tính thành phần combo)
                    $productTotal = $bill->billDetails->where('is_combo_component', false)->sum('total_price');

                    $totalAmount = $timeCost + $productTotal;

                    $discountAmount = $bill->discount_amount ?? 0;
                    $finalAmount = $totalAmount - $discountAmount;
                    $paymentMethod = $bill->payment_method;
                }

                // Lấy thông tin khuyến mãi từ note
                $promotionInfo = $this->extractPromotionInfoFromNote($bill->note);

                // Tạo QR code
                $qrUrl = "https://img.vietqr.io/image/MB-0368015218-qr_only.png?"
                    . http_build_query([
                        'amount' => $finalAmount,
                        'addInfo' => "TT Bill {$bill->bill_number}"
                    ]);

                // Gán các thuộc tính tạm thời vào model để view dễ truy cập
                $bill->timeCost = $timeCost;
                $bill->timeDetails = $timeDetails;
                $bill->productTotal = $productTotal;
                $bill->totalAmount = $totalAmount;
                $bill->finalAmount = $finalAmount;
                $bill->discountAmount = $discountAmount;
                $bill->promotionInfo = $promotionInfo;
                $bill->printTime = now()->format('H:i d/m/Y');
                $bill->staff = Auth::user()->name;
                $bill->qrUrl = $qrUrl;
                $bill->isPreview = $isPreview;
                $bill->paymentMethod = $paymentMethod;
                $bill->paymentData = $isPreview ? $paymentData : null;

                // Push model đã mở rộng vào mảng trả về
                $billsData[] = $bill;
            }

            // Truyền cả 'staff' top-level (dùng cho các chỗ view gọi $staff trực tiếp)
            return view('admin.bills.print-multiple', [
                'billsData' => $billsData,
                'autoRedirect' => $request->auto_print == 'true',
                'redirectUrl' => route('admin.bills.index'),
                'staff' => Auth::user()->name,
                'totalAmount' => array_sum(array_map(function ($bill) {
                    return $bill->finalAmount;
                }, $billsData))
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi in nhiều hóa đơn: ' . $e->getMessage());
        }
    }

    // Thêm phương thức trích xuất thông tin khuyến mãi từ note
    private function extractPromotionInfoFromNote($note)
    {
        if (!$note) {
            return null;
        }

        // Kiểm tra xem note có chứa thông tin khuyến mãi không
        if (strpos($note, 'Mã KM:') !== false) {
            $parts = explode(' - ', $note);
            if (count($parts) >= 2) {
                $codePart = $parts[0]; // "Mã KM: WELCOME10"
                $namePart = $parts[1]; // "Giảm 10% cho KH mới"

                // Lấy mã khuyến mãi
                $code = str_replace('Mã KM: ', '', $codePart);

                return [
                    'code' => $code,
                    'name' => $namePart
                ];
            }
        }

        return null;
    }

    /**
     * Tính toán chi tiết thời gian với làm tròn
     */
    public function calculateTimeChargeDetailed(Bill $bill)
    {
        $timeDetails = [
            'totalCost' => 0,
            'totalMinutes' => 0,
            'roundedMinutes' => 0,
            'sessions' => [],
            'hourlyRate' => 0,
            'roundingInfo' => []
        ];

        // Lấy tất cả session giờ thường
        $allRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->orderBy('created_at')
            ->get();

        $tableRate = $bill->table->tableRate;
        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
        $roundingAmount = $tableRate->rounding_amount ?? 1000;

        foreach ($allRegularTime as $timeUsage) {
            $sessionDetails = $this->calculateSessionDetails($timeUsage);

            $timeDetails['sessions'][] = [
                'type' => is_null($timeUsage->end_time) ? 'regular_active' : 'regular_ended',
                'actual_minutes' => $sessionDetails['actual_minutes'],
                'rounded_minutes' => $sessionDetails['rounded_minutes'],
                'hours' => round($sessionDetails['rounded_minutes'] / 60, 2),
                'hourly_rate' => $timeUsage->hourly_rate,
                'raw_price' => $sessionDetails['raw_price'],
                'rounded_price' => $sessionDetails['rounded_price'],
                'cost' => $sessionDetails['rounded_price'],
                'description' => "Giờ thường: " . $this->formatDuration($sessionDetails['actual_minutes']),
                'rounded_description' => "Tính phí: " . $this->formatDuration($sessionDetails['rounded_minutes']),
                'calculation' => $this->formatRoundedTimeCalculation(
                    $timeUsage->hourly_rate,
                    $sessionDetails['actual_minutes'],
                    $sessionDetails['rounded_minutes'],
                    $sessionDetails['raw_price'],
                    $sessionDetails['rounded_price']
                ),
                'table_note' => $timeUsage->note
            ];

            $timeDetails['totalCost'] += $sessionDetails['rounded_price'];
            $timeDetails['totalMinutes'] += $sessionDetails['actual_minutes'];
            $timeDetails['roundedMinutes'] += $sessionDetails['rounded_minutes'];

            if ($timeUsage->hourly_rate > 0) {
                $timeDetails['hourlyRate'] = $timeUsage->hourly_rate;
            }
        }

        $timeDetails['roundingInfo'] = [
            'rounding_minutes' => $roundingMinutes,
            'rounding_amount' => number_format($roundingAmount, 0, ',', '.'),
            'total_rounding_diff' => $timeDetails['totalCost'] -
                (($timeDetails['hourlyRate'] / 60) * $timeDetails['totalMinutes'])
        ];

        return $timeDetails;
    }

    /**
     * Tính chi tiết từng session
     */
    private function calculateSessionDetails(BillTimeUsage $timeUsage)
    {
        $tableRate = $timeUsage->bill->table->tableRate;
        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
        $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
        $roundingAmount = $tableRate->rounding_amount ?? 1000;

        // Tính số phút thực tế
        if (is_null($timeUsage->end_time)) {
            $actualMinutes = $this->calculateElapsedMinutes($timeUsage);
        } else {
            $actualMinutes = $timeUsage->duration_minutes ?? 0;
        }

        $effectiveMinutes = max($minChargeMinutes, $actualMinutes - ($timeUsage->paused_duration ?? 0));

        // Làm tròn phút lên
        $roundedMinutes = ceil($effectiveMinutes / $roundingMinutes) * $roundingMinutes;

        // Tính tiền
        $hourlyRate = $timeUsage->hourly_rate;
        $rawPrice = ($hourlyRate / 60) * $roundedMinutes;
        $roundedPrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;

        return [
            'actual_minutes' => $actualMinutes,
            'effective_minutes' => $effectiveMinutes,
            'rounded_minutes' => $roundedMinutes,
            'raw_price' => $rawPrice,
            'rounded_price' => $roundedPrice,
            'rounding_diff' => $roundedPrice - $rawPrice
        ];
    }

    /**
     * Format công thức tính tiền với làm tròn
     */
    private function formatRoundedTimeCalculation($hourlyRate, $actualMinutes, $roundedMinutes, $rawPrice, $roundedPrice)
    {
        $hourlyRateFormatted = number_format($hourlyRate, 0, ',', '.');
        $actualHours = round($actualMinutes / 60, 2);
        $roundedHours = round($roundedMinutes / 60, 2);
        $rawPriceFormatted = number_format($rawPrice, 0, ',', '.');
        $roundedPriceFormatted = number_format($roundedPrice, 0, ',', '.');

        $calculation = "{$hourlyRateFormatted}₫/h × {$actualHours}h (thực) ";
        $calculation .= "= {$rawPriceFormatted}₫ (làm tròn {$roundedHours}h) ";
        $calculation .= "→ {$roundedPriceFormatted}₫";

        if ($roundedPrice > $rawPrice) {
            $diff = $roundedPrice - $rawPrice;
            $diffFormatted = number_format($diff, 0, ',', '.');
            $calculation .= " (+{$diffFormatted}₫ làm tròn)";
        }

        return $calculation;
    }


    /**
     * Format thời gian từ phút sang "XhYp"
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h{$mins}p";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins}p";
        }
    }

    /**
     * Format công thức tính tiền giờ
     */
    private function formatTimeCalculation($hourlyRate, $minutes, $cost)
    {
        $hours = $minutes / 60;
        $hourlyRateFormatted = number_format($hourlyRate, 0, ',', '.');
        $costFormatted = number_format($cost, 0, ',', '.');

        return "{$hourlyRateFormatted}₫/h × {$hours}h = {$costFormatted}₫";
    }

    public function getCustomerStats($userId)
    {
        $user = User::find($userId);

        $stats = [
            'total_visits' => $user->total_visits,
            'total_spent' => $user->total_spent,
            'average_spent' => $user->total_visits > 0 ? $user->total_spent / $user->total_visits : 0,
            'customer_type' => $user->customer_type,
            'last_visit' => $user->last_visit_date,
        ];

        return $stats;
    }

    public function checkNewPayments(Request $request)
    {
        $lastCheck = $request->input('last_check', now()->subMinutes(5)->toISOString());

        $newPayments = Bill::with(['table', 'staff', 'user'])
            ->where('payment_status', 'Paid')
            ->where('updated_at', '>', $lastCheck)
            ->where('updated_at', '>', now()->subMinutes(10))
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'user_name' => $bill->user->name ?? null,
                    'user_phone' => $bill->user->phone ?? null,
                    'table_name' => $bill->table->table_name ?? null,
                    'table_number' => $bill->table->table_number ?? null,
                    'staff_name' => $bill->staff->name ?? null,
                    'staff_code' => $bill->staff->code ?? null,
                    'total_amount' => $bill->total_amount,
                    'discount_amount' => $bill->discount_amount,
                    'final_amount' => $bill->final_amount,
                    'status' => $bill->status,
                    'payment_status' => $bill->payment_status,
                    'created_at' => $bill->created_at->toISOString(),
                    'updated_at' => $bill->updated_at->toISOString(),
                ];
            });

        return response()->json([
            'new_payments' => $newPayments,
            'current_time' => now()->toISOString(),
        ]);
    }

    /**
     * Lấy thông tin thanh toán của bill
     */
    public function getPaymentInfo($id)
    {
        try {
            $bill = Bill::with(['table', 'staff', 'user'])
                ->findOrFail($id);

            return response()->json([
                'id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'user_name' => $bill->user->name ?? null,
                'user_phone' => $bill->user->phone ?? null,
                'table_name' => $bill->table->table_name ?? null,
                'table_number' => $bill->table->table_number ?? null,
                'staff_name' => $bill->staff->name ?? null,
                'staff_code' => $bill->staff->code ?? null,
                'total_amount' => $bill->total_amount,
                'discount_amount' => $bill->discount_amount,
                'final_amount' => $bill->final_amount,
                'status' => $bill->status,
                'payment_status' => $bill->payment_status,
                'created_at' => $bill->created_at->toISOString(),
                'updated_at' => $bill->updated_at->toISOString(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Không tìm thấy hóa đơn'], 404);
        }
    }

    /**
     * Xử lý khi thanh toán thành công (redirect từ print)
     */
    public function paymentSuccessRedirect($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);

            // Lưu session để hiển thị thông báo
            session()->flash('payment_success', [
                'bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'final_amount' => $bill->final_amount,
                'staff_name' => Auth::user()->name,
                'timestamp' => now()->toISOString(),
            ]);

            return redirect()->route('admin.bills.index')
                ->with('success', 'Thanh toán thành công!');
        } catch (Exception $e) {
            return redirect()->route('admin.bills.index')
                ->with('error', 'Không tìm thấy hóa đơn');
        }
    }
}
