<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Auth::user()->reservations()->latest()->get();
        // Giả sử bạn có quan hệ: User hasMany Reservation
        return view('client.reservation.index', compact('reservations'));
    }

    public function create()
    {
        $tables = Table::available()->get();
        return view('client.reservation.create', compact('tables'));
    }

public function store(Request $request)
{
    // Bắt buộc đăng nhập (route group middleware('auth') là ideal)
    if (!auth()->check()) {
        return response()->json(['message' => 'Bạn cần đăng nhập để đặt bàn'], 401);
    }

    $validated = $request->validate([
        'table_id' => 'required|integer|exists:tables,id',
        'reservation_time' => 'required|string', // "YYYY-MM-DD HH:mm"
        'duration' => 'required|integer|min:30',
        'guest_count' => 'required|integer|min:1',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:30',
        'customer_email' => 'nullable|email|max:255',
        'note' => 'nullable|string|max:1000',
    ]);

    // convert reservation_time to start/end
    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['reservation_time']);
    $end = (clone $start)->addMinutes($validated['duration']);

    DB::beginTransaction();
    try {
        $reservationCode = 'RSV' . now()->format('Ymd') . '-' . rand(100, 999);

        $res = Reservation::create([
            'customer_id' => auth()->id(), // or other logic if you have separate customer table
            'table_id' => $validated['table_id'],
            'reservation_time' => $start->format('Y-m-d H:i:s'),
            'end_time' => $end->format('Y-m-d H:i:s'),
            'duration' => $validated['duration'],
            'guest_count' => $validated['guest_count'],
            'note' => $validated['note'] ?? null,
            'status' => 'pending',
            'reservation_code' => $reservationCode,
            'created_by' => auth()->id(),
            // 'updated_by' => auth()->id(), // only if column exists
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'reservation_code' => $reservationCode,
            'data' => $res
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Reservation store error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'input' => $request->all()
        ]);
        return response()->json(['message' => 'Lỗi server khi lưu đặt bàn'], 500);
    }
}


    public function track()
    {
        return view('client.reservation.track'); // Theo dõi đặt bàn
    }

public function checkAvailability(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
        'time' => 'required',
        'duration' => 'required|integer|min:30',
    ]);

    // Ví dụ: tìm bàn trống theo logic của bạn
    // Đây là placeholder: đổi theo logic thật
    $date = $validated['date'];
    $time = $validated['time'];
    $duration = $validated['duration'];

    // build reservation_time and end_time for checking conflicts
    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
    $end = (clone $start)->addMinutes($duration);

    // Thực hiện truy vấn lấy bàn trống (tùy schema của bạn)
    $availableTables = Table::where('status', 'available')->get();

    return response()->json([
        'success' => true,
        'tables' => $availableTables
    ]);
}

    public function search(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'code' => 'nullable|string',
        ]);

        $query = Reservation::query();

        if ($request->filled('phone')) {
            $query->where('customer_phone', $request->phone);
        }

        if ($request->filled('code')) {
            $query->where('code', $request->code);
        }

        $reservation = $query->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đặt bàn.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    public function checkin(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'pending' && $reservation->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Đặt bàn không thể check-in.'
            ], 400);
        }

        $reservation->update([
            'status' => 'checked_in',
            'checkin_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'data' => $reservation
        ]);
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đặt bàn này.'
            ], 400);
        }

        // Kiểm tra thời gian: chỉ hủy trước 1h
        $reservationTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $reservation->reservation_date . ' ' . $reservation->reservation_time
        );

        if ($reservationTime->diffInHours(now(), false) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy trước 1 giờ.'
            ], 400);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Hủy đặt bàn thành công.'
        ]);
    }
}