<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['table', 'customer', 'createdBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('reservation_time', $request->date);
        }

        $reservations = $query->orderBy('reservation_time')->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $tables = Table::where('status', 'Available')->get();
        $customers = Customer::all();
        return view('reservations.create', compact('tables', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_id' => 'sometimes|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:30|max:240',
            'guest_count' => 'required|integer|min:1|max:20',
            'note' => 'sometimes|string'
        ]);

        $table = Table::find($request->table_id);

        if (!$table->isAvailable()) {
            return redirect()->back()->with('error', 'Bàn không khả dụng!');
        }

        // Kiểm tra trùng giờ
        $conflictingReservation = Reservation::where('table_id', $request->table_id)
            ->where('status', 'Confirmed')
            ->where(function($query) use ($request) {
                $reservationTime = Carbon::parse($request->reservation_time);
                $endTime = $reservationTime->copy()->addMinutes($request->duration);
                
                $query->whereBetween('reservation_time', [$reservationTime, $endTime])
                      ->orWhereBetween('reservation_time', 
                          [$reservationTime->copy()->subMinutes(30), $reservationTime->copy()->addMinutes(30)]);
            })
            ->exists();

        if ($conflictingReservation) {
            return redirect()->back()->with('error', 'Bàn đã được đặt trong khung giờ này!');
        }

        Reservation::create([
            'table_id' => $request->table_id,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'duration' => $request->duration,
            'guest_count' => $request->guest_count,
            'note' => $request->note,
            'created_by' => $request->user()->id,
            'status' => 'Pending'
        ]);

        $table->update(['status' => 'Reserved']);

        return redirect()->route('reservations.index')->with('success', 'Tạo đặt bàn thành công!');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['table', 'customer', 'createdBy']);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $tables = Table::where('status', 'Available')->get();
        $customers = Customer::all();
        return view('reservations.edit', compact('reservation', 'tables', 'customers'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_id' => 'sometimes|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'reservation_time' => 'required|date',
            'duration' => 'required|integer|min:30|max:240',
            'guest_count' => 'required|integer|min:1|max:20',
            'note' => 'sometimes|string'
        ]);

        $reservation->update($request->all());

        return redirect()->route('reservations.index')->with('success', 'Cập nhật đặt bàn thành công!');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Xóa đặt bàn thành công!');
    }

    public function confirm(Reservation $reservation)
    {
        if ($reservation->status !== 'Pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xác nhận đặt bàn đang chờ!');
        }

        $reservation->confirm();

        return redirect()->back()->with('success', 'Xác nhận đặt bàn thành công!');
    }

    public function checkin(Reservation $reservation)
    {
        if ($reservation->status !== 'Confirmed') {
            return redirect()->back()->with('error', 'Chỉ có thể check-in đặt bàn đã xác nhận!');
        }

        $bill = $reservation->checkin();

        return redirect()->route('bills.show', $bill)->with('success', 'Check-in thành công!');
    }

    public function cancel(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['Pending', 'Confirmed'])) {
            return redirect()->back()->with('error', 'Không thể hủy đặt bàn này!');
        }

        $reservation->cancel();

        return redirect()->back()->with('success', 'Hủy đặt bàn thành công!');
    }
}