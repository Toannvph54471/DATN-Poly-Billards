<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MockPaymentController extends Controller
{
    /**
     * Show mock payment form
     */
    public function showPaymentForm(Request $request)
    {
        $paymentId = $request->input('payment');

        if (!$paymentId) {
            return redirect('/')->with('error', 'Không tìm thấy thanh toán');
        }

        $payment = Payment::with([
            'reservation.table',
            'payable'
        ])->findOrFail($paymentId);

        if (!$payment->isPending()) {
            return redirect()
                ->back()
                ->with('info', 'Thanh toán này đã được xử lý');
        }

        // Determine payment context
        $context = $this->getPaymentContext($payment);

        return view('mock.payment', compact('payment', 'context'));
    }

    /**
     * Process mock payment (simulate success/failure)
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'action' => 'required|in:success,failed',
        ]);

        $payment = Payment::with(['reservation', 'payable'])->findOrFail($request->payment_id);

        if (!$payment->isPending()) {
            return redirect()
                ->route('reservations.track')
                ->with('info', 'Thanh toán này đã được xử lý');
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'success') {
                $this->handleSuccessPayment($payment);
                $message = 'Thanh toán thành công!';
                $type = 'success';
            } else {
                $this->handleFailedPayment($payment);
                $message = 'Thanh toán thất bại!';
                $type = 'error';
            }

            DB::commit();

            // Redirect based on payment type
            $redirect = $this->getRedirectUrl($payment);

            return redirect($redirect)->with($type, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mock payment processing error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán');
        }
    }

    /**
     * Create reservation payment (from reservation form)
     */
    public function createReservationPayment(Request $request, $reservationId)
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay,momo,zalopay,mock',
        ]);

        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->isPaid()) {
            return back()->with('error', 'Đặt bàn này đã được thanh toán');
        }

        try {
            $payment = $reservation->createPayment($request->payment_method);

            $paymentUrl = route('mock.payment.form', ['payment' => $payment->id]);

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            Log::error('Create reservation payment error: ' . $e->getMessage());
            return back()->with('error', 'Không thể tạo thanh toán');
        }
    }

    /**
     * Create bill payment (from bill payment form)
     */
    public function createBillPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay,momo,zalopay,mock',
            'amount' => 'required|numeric|min:0',
        ]);

        $bill = Bill::findOrFail($billId);

        if (!$bill->canBePaid()) {
            return back()->with('error', 'Bill không thể thanh toán');
        }

        try {
            DB::beginTransaction();

            // Create payment
            $payment = $bill->payments()->create([
                'amount' => $request->amount,
                'currency' => 'VND',
                'payment_method' => $request->payment_method,
                'payment_type' => Payment::TYPE_PARTIAL,
                'status' => Payment::STATUS_PENDING,
                'transaction_id' => 'BILL-' . $bill->id . '-' . time(),
            ]);

            DB::commit();

            $paymentUrl = route('mock.payment.form', ['payment' => $payment->id]);

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create bill payment error: ' . $e->getMessage());
            return back()->with('error', 'Không thể tạo thanh toán');
        }
    }

    /**
     * Show bank transfer info (alternative payment method)
     */
    public function showBankTransfer($transactionId)
    {
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        $bankInfo = [
            'bank_name' => 'Ngân hàng TMCP Á Châu (ACB)',
            'account_number' => '123456789',
            'account_name' => 'CONG TY TNHH BILLIARDS',
            'amount' => $payment->amount,
            'content' => $payment->transaction_id,
        ];

        return view('mock.bank-transfer', compact('payment', 'bankInfo'));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Handle successful payment
     */
    private function handleSuccessPayment(Payment $payment): void
    {
        $payment->markAsCompleted();

        // ✅ Cập nhật reservation status
        if ($payment->reservation_id) {
            $reservation = $payment->reservation;

            $reservation->update([
                'payment_status' => Reservation::PAYMENT_PAID,
                'payment_gateway' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'payment_completed_at' => now(),
                'status' => Reservation::STATUS_CONFIRMED,
            ]);
        }

        Log::info('Mock payment completed', [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'reservation_id' => $payment->reservation_id,
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Payment $payment): void
    {
        $payment->markAsFailed('Customer cancelled or payment failed');

        // Update reservation if applicable
        if ($payment->reservation_id) {
            $payment->reservation->update([
                'payment_status' => Reservation::PAYMENT_FAILED
            ]);
        }

        Log::warning('Mock payment failed', [
            'payment_id' => $payment->id,
            'reservation_id' => $payment->reservation_id,
        ]);
    }

    /**
     * Get payment context (reservation or bill)
     */
    private function getPaymentContext(Payment $payment): array
    {
        $context = [
            'type' => 'unknown',
            'title' => 'Thanh toán',
            'description' => '',
            'items' => [],
        ];

        // Reservation payment
        if ($payment->reservation_id) {
            $reservation = $payment->reservation;
            $context['type'] = 'reservation';
            $context['title'] = 'Thanh toán đặt bàn';
            $context['description'] = 'Đặt bàn ' . $reservation->table->table_name;
            $context['items'] = [
                [
                    'name' => $reservation->table->table_name,
                    'description' => 'Thời gian: ' . $reservation->duration . ' phút',
                    'price' => $payment->amount,
                ]
            ];
            $context['reservation'] = $reservation;
        }

        // Bill payment
        if ($payment->payable_type === Bill::class) {
            $bill = $payment->payable;
            $context['type'] = 'bill';
            $context['title'] = 'Thanh toán hóa đơn';
            $context['description'] = 'Hóa đơn ' . $bill->bill_number;

            $items = [];

            // Table time
            if ($tableAmount = $bill->calculateTableAmount()) {
                $items[] = [
                    'name' => 'Tiền bàn ' . $bill->table->table_name,
                    'description' => $bill->billTimeUsages->first()?->duration_minutes . ' phút',
                    'price' => $tableAmount,
                ];
            }

            // Products
            foreach ($bill->billDetails as $detail) {
                if (!$detail->is_combo_component) {
                    $items[] = [
                        'name' => $detail->product?->name ?? $detail->combo?->name,
                        'description' => 'SL: ' . $detail->quantity,
                        'price' => $detail->total_price,
                    ];
                }
            }

            $context['items'] = $items;
            $context['bill'] = $bill;
        }

        return $context;
    }

    /**
     * Get redirect URL after payment
     */
    private function getRedirectUrl(Payment $payment): string
    {
        // Reservation payment - redirect to customer track page
        if ($payment->reservation_id) {
            return route('reservations.track'); // ✅ SỬA TỪ reservations.index
        }

        // Bill payment
        if ($payment->payable_type === Bill::class) {
            return route('admin.tables.detail', $payment->payable->table_id);
        }

        return route('home');
    }
}
