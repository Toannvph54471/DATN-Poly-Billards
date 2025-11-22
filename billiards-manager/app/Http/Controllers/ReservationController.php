<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * STEP 1: Customer view reservation form
     */
    public function create()
    {
        $tables = Table::with('category')
            ->where('status', Table::STATUS_AVAILABLE)
            ->get()
            ->groupBy('category.name');

        return view('client.reservation.create', compact('tables'));
    }

    /**
     * STEP 2: Check table availability
     * ✅ FIXED: Sửa logic query conflicting reservations
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:30|max:480',
            'guest_count' => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $start = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time']);
        $end = $start->copy()->addMinutes($validated['duration']);

        // Tính end_time động từ reservation_time + duration
        $conflictingTableIds = Reservation::whereRaw(
            "DATE_ADD(reservation_time, INTERVAL duration MINUTE) > ? AND reservation_time < ?",
            [$start, $end]
        )
            ->whereIn('status', [
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_CHECKED_IN
            ])
            ->pluck('table_id')
            ->toArray();

        $query = Table::with(['category'])
            ->where('status', Table::STATUS_AVAILABLE)
            ->whereNotIn('id', $conflictingTableIds);

        if ($validated['guest_count'] ?? null) {
            $query->where('capacity', '>=', $validated['guest_count']);
        }

        if ($validated['category_id'] ?? null) {
            $query->where('category_id', $validated['category_id']);
        }

        $tables = $query->get()->map(function ($table) use ($validated) {
            $price = $table->calculatePrice($validated['duration']);
            return [
                'id' => $table->id,
                'table_name' => $table->table_name,
                'table_number' => $table->table_number,
                'category' => $table->category->name ?? 'Standard',
                'capacity' => $table->capacity ?? 4,
                'type' => $table->type,
                'hourly_rate' => $table->getHourlyRate(),
                'total_price' => $price,
                'total_price_formatted' => number_format($price) . 'đ',
            ];
        });

        return response()->json([
            'success' => true,
            'tables' => $tables,
            'duration' => $validated['duration'],
        ]);
    }

    /**
     * STEP 3: Create reservation
     * ✅ SỬA: Tạo reservation trước, payment sau
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:30|max:480',
            'guest_count' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'note' => 'nullable|string|max:1000',
            'payment_type' => 'required|in:online,onsite',
        ]);

        DB::beginTransaction();
        try {
            // Find or create customer
            $customer = $this->findOrCreateCustomer($validated);

            // Parse time
            $start = Carbon::parse($validated['reservation_time']);
            $end = $start->copy()->addMinutes($validated['duration']);

            // Tính tiền bàn
            $table = Table::findOrFail($validated['table_id']);
            $totalAmount = $table->calculatePrice($validated['duration']);

            // ✅ SỬA: Deposit = 0 cho cả online và onsite
            // Khách sẽ thanh toán sau
            $depositAmount = 0;

            // Create reservation
            $reservation = Reservation::create([
                'customer_id' => $customer->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'table_id' => $validated['table_id'],
                'reservation_time' => $start,
                'end_time' => $end,
                'duration' => $validated['duration'],
                'guest_count' => $validated['guest_count'],
                'note' => $validated['note'] ?? null,
                'payment_type' => $validated['payment_type'],
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'status' => Reservation::STATUS_PENDING, // ✅ Luôn pending ban đầu
                'payment_status' => Reservation::PAYMENT_PENDING,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            // ✅ QUAN TRỌNG: Không redirect đến payment ngay
            // Trả về thông tin reservation để hiển thị modal
            return response()->json([
                'success' => true,
                'message' => 'Đặt bàn thành công!',
                'reservation_code' => $reservation->reservation_code,
                'reservation_id' => $reservation->id,
                'payment_type' => $validated['payment_type'],
                'total_amount' => $totalAmount,
                'reservation' => $reservation->load('table'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * STEP 4: Payment page
     * ✅ Cho phép thanh toán sau khi đã có reservation
     */
    public function showPayment($id)
    {
        $reservation = Reservation::with('table')->findOrFail($id);

        // ✅ Cho phép xem trang payment nếu chưa thanh toán
        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return redirect()
                ->route('reservations.show', $reservation->id)
                ->with('info', 'Đặt bàn này đã được thanh toán');
        }

        // ✅ Kiểm tra quyền truy cập
        if (Auth::check() && $reservation->customer_id !== Auth::id()) {
            return redirect()
                ->route('reservations.track')
                ->with('error', 'Bạn không có quyền truy cập đặt bàn này');
        }

        return view('client.reservation.payment', compact('reservation'));
    }

    /**
     * STEP 5: Process payment
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay,momo,zalopay,mock',
        ]);

        $reservation = Reservation::findOrFail($id);

        if ($reservation->payment_status === Reservation::PAYMENT_PAID) {
            return back()->with('error', 'Đặt bàn này đã được thanh toán');
        }

        DB::beginTransaction();
        try {
            // Tạo payment record
            $payment = $reservation->payments()->create([
                'amount' => $reservation->total_amount,
                'currency' => 'VND',
                'payment_method' => $request->payment_method,
                'payment_type' => Payment::TYPE_FULL,
                'status' => Payment::STATUS_PENDING,
                'transaction_id' => 'RSV-' . $reservation->id . '-' . time(),
            ]);

            DB::commit();

            // Redirect đến mock payment form
            $paymentUrl = route('mock.payment.form', ['payment' => $payment->id]);

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation error: ' . $e->getMessage());
            return back()->with('error', 'Không thể tạo thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * STEP 7: Customer check-in
     */
    public function checkin(Request $request, $id)
    {
        $reservation = Reservation::with('table')->findOrFail($id);

        if (!$reservation->canCheckIn()) {
            return response()->json([
                'success' => false,
                'message' => $this->getCheckInErrorMessage($reservation)
            ], 400);
        }

        DB::beginTransaction();
        try {
            $bill = $reservation->checkIn();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công!',
                'bill' => $bill->load(['table', 'billTimeUsages']),
                'redirect' => route('admin.tables.detail', $reservation->table_id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Check-in error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if (!in_array($reservation->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đặt bàn này'
            ], 400);
        }

        // Check cancellation policy (1 hour before)
        if ($reservation->reservation_time->diffInHours(now(), false) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy trước 1 giờ'
            ], 400);
        }

        $reason = $request->input('reason', 'Khách hàng hủy');

        if ($reservation->cancel($reason)) {
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đặt bàn' . ($reservation->isPaid() ? ' và hoàn tiền' : '')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể hủy đặt bàn'
        ], 500);
    }

    /**
     * Track reservation
     */
    public function track()
    {
        if (Auth::check()) {
            $userId = Auth::id();

            $reservations = Reservation::where('customer_id', $userId)
                ->with('table')
                ->latest('reservation_time')
                ->get();

            $upcomingReservations = Reservation::where('customer_id', $userId)
                ->whereIn('status', [
                    Reservation::STATUS_PENDING,
                    Reservation::STATUS_CONFIRMED
                ])
                ->where('reservation_time', '>', now())
                ->with('table')
                ->orderBy('reservation_time', 'asc')
                ->get();

            $totalSpent = Reservation::where('customer_id', $userId)
                ->where('status', Reservation::STATUS_COMPLETED)
                ->sum('total_amount');

            return view('client.reservation.track', compact(
                'reservations',
                'upcomingReservations',
                'totalSpent'
            ));
        }

        return view('client.reservation.track-guest');
    }

    /**
     * Search reservation (for guests)
     */
    public function search(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'nullable|string',
        ]);

        $query = Reservation::with('table', 'payments');

        // ✅ SỬA: Tìm theo phone trực tiếp trong reservations table
        $query->where('customer_phone', $request->phone);

        if ($request->filled('code')) {
            $query->where('reservation_code', $request->code);
        }

        $reservations = $query->get();

        if ($reservations->isEmpty()) {
            return back()->with('error', 'Không tìm thấy đặt bàn');
        }

        // ✅ Nếu tìm thấy nhiều, hiển thị danh sách
        if ($reservations->count() > 1) {
            return view('client.reservation.search-results', compact('reservations'));
        }

        // Nếu chỉ có 1, redirect đến chi tiết
        return redirect()->route('reservations.show', $reservations->first()->id);
    }

    /**
     * Show reservation detail
     */
    public function show($id)
    {
        $reservation = Reservation::with([
            'table',
            'customer',
            'bill.billDetails.product',
            'bill.billTimeUsages',
            'payments'
        ])->findOrFail($id);

        return view('client.reservation.show', compact('reservation'));
    }

    /**
     * List all reservations (admin)
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['table', 'customer'])
            ->latest('reservation_time');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('reservation_time', $request->date);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $reservations = $query->paginate(20);

        return view('admin.reservations.index', compact('reservations'));
    }

    // ==================== HELPER METHODS ====================

    private function findOrCreateCustomer(array $data): User
    {
        if (Auth::check() && Auth::user()->role->slug === 'customer') {
            $customer = Auth::user();
            $customer->update([
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone']
            ]);
            return $customer;
        }

        $customerRole = Role::where('slug', 'customer')->firstOrFail();

        return User::firstOrCreate(
            ['phone' => $data['customer_phone']],
            [
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? null,
                'role_id' => $customerRole->id,
                'password' => null,
            ]
        );
    }

    private function getCheckInErrorMessage(Reservation $reservation): string
    {
        // ✅ SỬA: Cho phép check-in cả khi chưa thanh toán (onsite payment)
        if ($reservation->payment_type === 'online' && !$reservation->isPaid()) {
            return 'Vui lòng thanh toán trước khi check-in';
        }

        if (!in_array($reservation->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED
        ])) {
            return 'Đặt bàn không trong trạng thái hợp lệ';
        }

        $now = now();
        $allowedStart = $reservation->reservation_time->copy()->subMinutes(30);
        $allowedEnd = $reservation->reservation_time->copy()->addHours(1);

        if ($now->lt($allowedStart)) {
            return 'Chưa đến giờ check-in (có thể check-in từ 30 phút trước)';
        }

        if ($now->gt($allowedEnd)) {
            return 'Đã quá giờ check-in';
        }

        return 'Không thể check-in';
    }
}
