<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Services\MockPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private MockPaymentService $mockPaymentService
    ) {}

    /**
     * Tạo thanh toán mới (deposit hoặc remaining)
     */
    public function create(Request $request, Reservation $reservation)
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay,momo,stripe,cash',
            'payment_type' => 'required|in:deposit,remaining',
        ]);

        // Kiểm tra quyền thanh toán
        if ($request->payment_type === 'deposit' && $reservation->isDepositPaid()) {
            return back()->with('error', 'Đã thanh toán tiền cọc rồi!');
        }

        if ($request->payment_type === 'remaining' && !$reservation->canPayRemaining()) {
            return back()->with('error', 'Chưa thể thanh toán tiền còn lại!');
        }

        // Tính số tiền cần thanh toán
        $amount = $request->payment_type === 'deposit'
            ? $reservation->deposit_amount
            : $reservation->getRemainingToPay();

        // Tạo payment record
        $payment = Payment::create([
            'reservation_id' => $reservation->id,
            'transaction_id' => 'TXN' . time() . strtoupper(Str::random(6)),
            'payment_method' => $request->payment_method,
            'payment_type' => $request->payment_type,
            'amount' => $amount,
            'currency' => 'VND',
            'status' => Payment::STATUS_PENDING,
        ]);

        // Thanh toán tiền mặt - cập nhật trực tiếp
        if ($request->payment_method === 'cash') {
            $payment->markAsCompleted();

            if ($payment->isDeposit()) {
                $reservation->update(['payment_status' => 'deposit_paid']);
            } else {
                $reservation->markAsFullyPaid();
            }

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', 'Đã xác nhận thanh toán bằng tiền mặt!');
        }

        // Redirect đến trang thanh toán giả lập
        $paymentUrl = $this->mockPaymentService->createPaymentUrl(
            $payment,
            $request->payment_method
        );

        return redirect($paymentUrl);
    }

    /**
     * Trang thanh toán giả lập
     */
    public function mockPaymentPage(Request $request)
    {
        $payment = Payment::with('reservation.table')->findOrFail($request->payment_id);
        $method = $request->method;

        if (!$payment->isPending()) {
            return redirect()
                ->route('reservations.show', $payment->reservation)
                ->with('error', 'Thanh toán này đã được xử lý!');
        }

        return view('payments.mock-page', compact('payment', 'method'));
    }

    /**
     * Xử lý thanh toán giả lập
     */
    public function mockPaymentProcess(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:success,failed',
        ]);

        $success = $request->action === 'success';
        $result = $this->mockPaymentService->processPayment($payment, $success);

        if ($result['success']) {
            // Cập nhật trạng thái reservation
            if ($payment->isDeposit()) {
                $payment->reservation->update([
                    'payment_status' => 'deposit_paid',
                    'status' => Reservation::STATUS_CONFIRMED
                ]);
            } else if ($payment->isRemaining()) {
                $payment->reservation->markAsFullyPaid();
                $payment->reservation->complete();
            }

            return redirect()
                ->route('reservations.show', $payment->reservation)
                ->with('success', $result['message']);
        }

        return redirect()
            ->route('reservations.show', $payment->reservation)
            ->with('error', $result['message']);
    }

    /**
     * Form thanh toán tiền còn lại
     */
    public function remainingPaymentForm(Reservation $reservation)
    {
        if (!$reservation->canPayRemaining()) {
            return back()->with('error', 'Không thể thanh toán lúc này!');
        }

        $remainingAmount = $reservation->getRemainingToPay();

        return view('payments.remaining-form', compact('reservation', 'remainingAmount'));
    }

    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show(Payment $payment)
    {
        $payment->load('reservation.table', 'reservation.customer');
        return view('payments.show', compact('payment'));
    }

    /**
     * Danh sách thanh toán
     */
    public function index(Request $request)
    {
        $query = Payment::with('reservation.table', 'reservation.customer')
            ->latest();

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo loại thanh toán
        if ($request->filled('type')) {
            $query->where('payment_type', $request->type);
        }

        // Filter theo phương thức
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter theo ngày
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $payments = $query->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * Hoàn tiền
     */
    public function refund(Payment $payment)
    {
        if (!$payment->isCompleted()) {
            return back()->with('error', 'Chỉ có thể hoàn tiền cho thanh toán đã hoàn thành!');
        }

        if ($payment->isRefunded()) {
            return back()->with('error', 'Thanh toán này đã được hoàn tiền!');
        }

        $payment->markAsRefunded();
        $payment->reservation->update(['payment_status' => 'refunded']);

        return back()->with('success', 'Đã hoàn tiền thành công!');
    }

    /**
     * Hủy thanh toán
     */
    public function cancel(Payment $payment)
    {
        if (!$payment->isPending()) {
            return back()->with('error', 'Chỉ có thể hủy thanh toán đang chờ xử lý!');
        }

        $payment->markAsFailed();

        return back()->with('success', 'Đã hủy thanh toán!');
    }
}
