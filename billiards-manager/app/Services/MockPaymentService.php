<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class MockPaymentService
{
    /**
     * Tạo URL thanh toán giả lập
     */
    public function createPaymentUrl(Payment $payment, string $method): string
    {
        return route('mock.payment.page', [
            'payment_id' => $payment->id,
            'method' => $method
        ]);
    }

    /**
     * Xử lý thanh toán giả lập
     */
    public function processPayment(Payment $payment, bool $success): array
    {
        if (!$payment->isPending()) {
            return [
                'success' => false,
                'message' => 'Thanh toán này đã được xử lý rồi!'
            ];
        }

        try {
            if ($success) {
                // Thanh toán thành công
                $payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                    'paid_at' => now(),
                    'payment_data' => [
                        'mock_transaction_id' => 'MOCK_' . time(),
                        'payment_gateway' => $payment->payment_method,
                        'completed_at' => now()->toISOString()
                    ]
                ]);

                Log::info('Mock payment completed', [
                    'payment_id' => $payment->id,
                    'reservation_id' => $payment->reservation_id,
                    'amount' => $payment->amount,
                    'type' => $payment->payment_type
                ]);

                $message = $payment->isDeposit()
                    ? 'Thanh toán cọc thành công! Vui lòng đến đúng giờ và thanh toán số tiền còn lại.'
                    : 'Thanh toán hoàn tất! Cảm ơn bạn đã sử dụng dịch vụ.';

                return [
                    'success' => true,
                    'message' => $message
                ];
            } else {
                // Thanh toán thất bại
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'payment_data' => [
                        'failed_at' => now()->toISOString(),
                        'reason' => 'Customer cancelled or payment failed'
                    ]
                ]);

                Log::warning('Mock payment failed', [
                    'payment_id' => $payment->id,
                    'reservation_id' => $payment->reservation_id
                ]);

                return [
                    'success' => false,
                    'message' => 'Thanh toán thất bại! Vui lòng thử lại.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Mock payment processing error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán!'
            ];
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        return [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at,
            'payment_method' => $payment->payment_method,
            'payment_type' => $payment->payment_type,
        ];
    }

    /**
     * Hoàn tiền
     */
    public function refundPayment(Payment $payment): array
    {
        if (!$payment->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Chỉ có thể hoàn tiền cho thanh toán đã hoàn thành!'
            ];
        }

        if ($payment->isRefunded()) {
            return [
                'success' => false,
                'message' => 'Thanh toán này đã được hoàn tiền rồi!'
            ];
        }

        try {
            $payment->update([
                'status' => Payment::STATUS_REFUNDED,
                'payment_data' => array_merge($payment->payment_data ?? [], [
                    'refunded_at' => now()->toISOString(),
                    'refund_transaction_id' => 'REFUND_' . time()
                ])
            ]);

            Log::info('Mock payment refunded', [
                'payment_id' => $payment->id,
                'reservation_id' => $payment->reservation_id,
                'amount' => $payment->amount
            ]);

            return [
                'success' => true,
                'message' => 'Hoàn tiền thành công!'
            ];
        } catch (\Exception $e) {
            Log::error('Mock refund error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hoàn tiền!'
            ];
        }
    }
}
