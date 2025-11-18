@extends('layouts.customer')

@section('title', 'Chi tiết hóa đơn #' . $bill->bill_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Hóa đơn #{{ $bill->bill_number }}</h1>
                <div class="flex items-center mt-3 space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($bill->status === 'open') bg-green-100 text-green-700
                        @elseif($bill->status === 'paused') bg-yellow-100 text-yellow-700
                        @elseif($bill->status === 'closed') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ $bill->status_label }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($bill->is_paid) bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ $bill->payment_status_label }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Bàn</p>
                <p class="text-2xl font-bold text-blue-600">{{ $bill->table->table_name }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Bill Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Time Usage -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-3"></i>
                    Thời gian sử dụng
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Bắt đầu</p>
                        <p class="font-semibold">{{ $bill->start_time->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kết thúc</p>
                        <p class="font-semibold">{{ $bill->end_time ? $bill->end_time->format('d/m/Y H:i') : 'Đang sử dụng' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Thời gian</p>
                        <p class="font-semibold">{{ $bill->total_time }} phút</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Giá bàn</p>
                        <p class="font-semibold text-blue-600">{{ number_format($bill->table_price) }}đ</p>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-shopping-cart text-blue-500 mr-3"></i>
                    Sản phẩm đã order
                </h2>
                
                @if($bill->billDetails->count() > 0)
                <div class="space-y-3">
                    @foreach($bill->billDetails as $detail)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $detail->product->name }}</p>
                            <p class="text-sm text-gray-600">{{ number_format($detail->unit_price) }}đ x {{ $detail->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-blue-600">{{ number_format($detail->total_price) }}đ</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">Chưa có sản phẩm nào</p>
                @endif
            </div>

            <!-- Payment History -->
            @if($bill->payments->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-3"></i>
                    Lịch sử thanh toán
                </h2>
                <div class="space-y-3">
                    @foreach($bill->payments as $payment)
                    <div class="flex items-center justify-between p-4 border-l-4 
                        @if($payment->status === 'completed') border-green-500 bg-green-50
                        @elseif($payment->status === 'pending') border-yellow-500 bg-yellow-50
                        @else border-red-500 bg-red-50 @endif rounded">
                        <div>
                            <p class="font-semibold">{{ number_format($payment->amount) }}đ</p>
                            <p class="text-sm text-gray-600">
                                {{ $payment->payment_method }} - 
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </p>
                            @if($payment->transaction_id)
                            <p class="text-xs text-gray-500">Mã GD: {{ $payment->transaction_id }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($payment->status === 'completed') bg-green-100 text-green-700
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Payment Summary -->
        <div class="space-y-6">
            <!-- Total Summary -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">Tổng thanh toán</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Tiền bàn:</span>
                        <span class="font-semibold">{{ number_format($bill->table_price) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sản phẩm:</span>
                        <span class="font-semibold">{{ number_format($bill->product_total) }}đ</span>
                    </div>
                    @if($bill->discount_amount > 0)
                    <div class="flex justify-between text-yellow-200">
                        <span>Giảm giá:</span>
                        <span class="font-semibold">-{{ number_format($bill->discount_amount) }}đ</span>
                    </div>
                    @endif
                    <div class="border-t border-white/30 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xl">Tổng cộng:</span>
                            <span class="text-3xl font-bold">{{ number_format($bill->final_amount) }}đ</span>
                        </div>
                    </div>
                    @if($bill->total_paid > 0)
                    <div class="flex justify-between text-green-200">
                        <span>Đã thanh toán:</span>
                        <span class="font-semibold">{{ number_format($bill->total_paid) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Còn lại:</span>
                        <span class="text-2xl font-bold">{{ number_format($bill->remaining_amount) }}đ</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Actions -->
            @if($bill->canBePaid() && $bill->remaining_amount > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Thanh toán</h3>
                
                <!-- Cash Payment -->
                <form action="{{ route('admin.bills.payment', $bill->id) }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="amount" value="{{ $bill->remaining_amount }}">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Thanh toán tiền mặt
                    </button>
                </form>

                <!-- Online Payment -->
                <div class="space-y-2">
                    <button onclick="openPaymentModal('vnpay')" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition">
                        <i class="fas fa-credit-card mr-2"></i>VNPay
                    </button>
                    <button onclick="openPaymentModal('momo')"
                            class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded-lg transition">
                        <i class="fas fa-mobile-alt mr-2"></i>Momo
                    </button>
                    <button onclick="openPaymentModal('zalopay')"
                            class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition">
                        <i class="fas fa-wallet mr-2"></i>ZaloPay
                    </button>
                </div>
            </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin</h3>
                <div class="space-y-3 text-sm">
                    @if($bill->customer)
                    <div>
                        <p class="text-gray-600">Khách hàng</p>
                        <p class="font-semibold">{{ $bill->customer->name }}</p>
                        <p class="text-gray-600">{{ $bill->customer->phone }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-600">Nhân viên phục vụ</p>
                        <p class="font-semibold">{{ $bill->staff->name }}</p>
                    </div>
                    @if($bill->note)
                    <div>
                        <p class="text-gray-600">Ghi chú</p>
                        <p class="text-gray-700">{{ $bill->note }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Thanh toán online</h3>
        <form id="onlinePaymentForm">
            <input type="hidden" id="payment_gateway" name="payment_gateway">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Số tiền</label>
                <input type="number" id="payment_amount" name="amount" 
                       value="{{ $bill->remaining_amount }}" 
                       max="{{ $bill->remaining_amount }}"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl" required>
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="closePaymentModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-xl transition">
                    Hủy
                </button>
                <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-xl transition">
                    Thanh toán
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(gateway) {
    document.getElementById('payment_gateway').value = gateway;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Cash/Card payment handler
document.querySelectorAll('.cash-payment-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        try {
            const response = await fetch('{{ route("admin.bills.payment", $bill->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ ' + result.message);
                location.reload();
            } else {
                alert('❌ ' + (result.message || 'Có lỗi xảy ra!'));
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    });
});

// Online payment handler
document.getElementById('onlinePaymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const gateway = document.getElementById('payment_gateway').value;
    const amount = document.getElementById('payment_amount').value;
    
    try {
        // SỬ DỤNG MOCK PAYMENT
        const response = await fetch('{{ route("mock.payment.bill.create", $bill->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_gateway: gateway,
                amount: amount
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
    }
});
</script>
@endsection