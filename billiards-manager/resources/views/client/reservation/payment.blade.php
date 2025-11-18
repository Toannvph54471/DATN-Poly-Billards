@extends('layouts.customer')

@section('title', 'Thanh toán đặt bàn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-credit-card text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Thanh toán đặt bàn</h1>
            <p class="text-gray-600">Vui lòng chọn phương thức thanh toán</p>
        </div>

        <!-- Reservation Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Thông tin đặt bàn</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Mã đặt bàn:</span>
                    <span class="font-semibold text-blue-600">{{ $reservation->reservation_code }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Bàn:</span>
                    <span class="font-semibold">{{ $reservation->table->table_name }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Thời gian:</span>
                    <span class="font-semibold">{{ $reservation->reservation_time->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Thời lượng:</span>
                    <span class="font-semibold">{{ $reservation->duration }} phút</span>
                </div>
                
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Số người:</span>
                    <span class="font-semibold">{{ $reservation->guest_count }} người</span>
                </div>
                
                <div class="flex justify-between items-center pt-3">
                    <span class="text-lg font-semibold text-gray-800">Tổng tiền:</span>
                    <span class="text-2xl font-bold text-blue-600">{{ number_format($reservation->total_amount) }}đ</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Chọn phương thức thanh toán</h2>
            
            <form action="{{ route('reservation.process-payment', $reservation->id) }}" method="POST" id="payment-form">
                @csrf
                
                <div class="space-y-3">
                    <!-- Mock Payment (Test) -->
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="payment_method" value="mock" checked class="w-5 h-5 text-blue-600">
                        <div class="ml-4 flex-1">
                            <div class="font-semibold text-gray-800">Thanh toán test (Mock)</div>
                            <div class="text-sm text-gray-500">Dùng để test, không thanh toán thật</div>
                        </div>
                        <div class="text-blue-600">
                            <i class="fas fa-vial text-2xl"></i>
                        </div>
                    </label>

                    <!-- VNPay -->
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="payment_method" value="vnpay" class="w-5 h-5 text-blue-600">
                        <div class="ml-4 flex-1">
                            <div class="font-semibold text-gray-800">VNPay</div>
                            <div class="text-sm text-gray-500">Thanh toán qua thẻ ATM, Visa, MasterCard</div>
                        </div>
                        <div class="text-blue-600">
                            <i class="fas fa-credit-card text-2xl"></i>
                        </div>
                    </label>

                    <!-- Momo -->
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-pink-500 transition">
                        <input type="radio" name="payment_method" value="momo" class="w-5 h-5 text-pink-600">
                        <div class="ml-4 flex-1">
                            <div class="font-semibold text-gray-800">MoMo</div>
                            <div class="text-sm text-gray-500">Ví điện tử MoMo</div>
                        </div>
                        <div class="text-pink-600">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                    </label>

                    <!-- ZaloPay -->
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="payment_method" value="zalopay" class="w-5 h-5 text-blue-600">
                        <div class="ml-4 flex-1">
                            <div class="font-semibold text-gray-800">ZaloPay</div>
                            <div class="text-sm text-gray-500">Ví điện tử ZaloPay</div>
                        </div>
                        <div class="text-blue-400">
                            <i class="fas fa-mobile-alt text-2xl"></i>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-lg transition transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-lock mr-2"></i>
                    Thanh toán {{ number_format($reservation->total_amount) }}đ
                </button>
            </form>
        </div>

        <!-- Security Info -->
        <div class="bg-blue-50 rounded-lg p-4 text-center">
            <i class="fas fa-shield-alt text-blue-600 text-2xl mb-2"></i>
            <p class="text-sm text-gray-600">Thông tin thanh toán của bạn được bảo mật 100%</p>
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('reservations.track') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
});
</script>
@endsection