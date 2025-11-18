@extends('layouts.customer')

@section('title', 'Thanh toán - Mock Gateway')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Mock Payment Gateway</h1>
                <p class="text-gray-500 mt-2">Mô phỏng thanh toán (Test Mode)</p>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">Mã giao dịch:</span>
                    <span class="font-mono text-sm">{{ $payment->transaction_id }}</span>
                </div>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">Phương thức:</span>
                    <span class="font-semibold">{{ strtoupper($payment->payment_method) }}</span>
                </div>
                
                @if($payment->reservation_id)
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">Đặt bàn:</span>
                    <span class="font-semibold">{{ $payment->reservation->reservation_code }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center pt-3 border-t">
                    <span class="text-lg font-semibold">Số tiền:</span>
                    <span class="text-2xl font-bold text-blue-600">{{ number_format($payment->amount) }}đ</span>
                </div>
            </div>

            <!-- Test Buttons -->
            <form action="{{ route('mock.payment.process') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                
                <div class="space-y-3">
                    <button type="submit" name="action" value="success"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-lg transition transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>
                        Thanh toán thành công
                    </button>
                    
                    <button type="submit" name="action" value="failed"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 rounded-lg transition transform hover:scale-105">
                        <i class="fas fa-times-circle mr-2"></i>
                        Thanh toán thất bại
                    </button>
                </div>
            </form>

            <!-- Info -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Đây là trang thanh toán mô phỏng. Chọn kết quả để test luồng thanh toán.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection