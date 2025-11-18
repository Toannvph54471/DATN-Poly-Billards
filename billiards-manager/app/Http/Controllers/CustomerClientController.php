<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBillController extends Controller
{
    /**
     * Hiển thị lịch sử hóa đơn của khách hàng
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Bill::where('customer_id', $user->id)
            ->with(['table', 'staff', 'reservation', 'billDetails.product', 'billTimeUsages', 'payments'])
            ->latest();

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo trạng thái thanh toán
        if ($request->filled('is_paid')) {
            $query->where('is_paid', $request->is_paid);
        }

        // Filter theo tháng
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter theo năm
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Search theo bill_number
        if ($request->filled('search')) {
            $query->where('bill_number', 'like', "%{$request->search}%");
        }

        $bills = $query->paginate(15);

        // Thống kê
        $stats = [
            'total_bills' => Bill::where('customer_id', $user->id)->count(),
            'total_spent' => Bill::where('customer_id', $user->id)
                ->where('is_paid', true)
                ->sum('final_amount'),
            'unpaid_bills' => Bill::where('customer_id', $user->id)
                ->where('is_paid', false)
                ->whereIn('status', ['open', 'paused', 'closed'])
                ->count(),
            'this_month_spent' => Bill::where('customer_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('is_paid', true)
                ->sum('final_amount'),
        ];

        return view('client.bills.index', compact('bills', 'stats'));
    }

    /**
     * Chi tiết hóa đơn
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $bill = Bill::where('customer_id', $user->id)
            ->with([
                'table',
                'staff',
                'reservation',
                'billDetails.product',
                'billDetails.combo',
                'billTimeUsages',
                'payments'
            ])
            ->findOrFail($id);

        return view('client.bills.show', compact('bill'));
    }

    /**
     * Yêu cầu sửa hóa đơn (chỉ với bill đang mở)
     */
    public function requestEdit($id)
    {
        $user = Auth::user();
        
        $bill = Bill::where('customer_id', $user->id)
            ->whereIn('status', ['open', 'paused'])
            ->findOrFail($id);

        return view('client.bills.edit', compact('bill'));
    }

    /**
     * API: Lấy danh sách bill của khách hàng
     */
    public function apiList(Request $request)
    {
        $user = Auth::user();
        
        $bills = Bill::where('customer_id', $user->id)
            ->with(['table', 'billDetails', 'payments'])
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'table_name' => $bill->table->table_name ?? 'N/A',
                    'start_time' => $bill->start_time->format('d/m/Y H:i'),
                    'end_time' => $bill->end_time?->format('d/m/Y H:i'),
                    'total_amount' => $bill->total_amount,
                    'final_amount' => $bill->final_amount,
                    'is_paid' => $bill->is_paid,
                    'status' => $bill->status,
                    'status_label' => $bill->status_label,
                    'payment_status_label' => $bill->payment_status_label,
                    'can_edit' => $bill->isOpen(),
                ];
            });

        return response()->json(['success' => true, 'bills' => $bills]);
    }

    /**
     * Xuất hóa đơn PDF (tùy chọn)
     */
    public function exportPdf($id)
    {
        $user = Auth::user();
        
        $bill = Bill::where('customer_id', $user->id)
            ->with(['table', 'billDetails.product', 'billTimeUsages'])
            ->findOrFail($id);

        // TODO: Implement PDF export
        // return PDF::loadView('bills.pdf', compact('bill'))->download("bill-{$bill->bill_number}.pdf");
        
        return back()->with('info', 'Chức năng xuất PDF đang phát triển');
    }
}