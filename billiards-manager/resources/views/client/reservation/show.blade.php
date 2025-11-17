@extends('layouts.customer')

@section('title', 'Chi tiết đặt bàn - ' . $reservation->reservation_code)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-elegant-navy">Đặt bàn #{{ $reservation->reservation_code }}</h1>
                    <p class="text-gray-600 mt-1">
                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 text-sm font-semibold rounded-full
                        @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($reservation->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($reservation->status === 'checked_in') bg-green-100 text-green-800
                        @elseif($reservation->status === 'completed') bg-gray-100 text-gray-800
                        @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                    </span>
                </div>
            </div>

            <!-- Payment Status -->
            @if($reservation->payment_status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-yellow-800 mb-2">Chưa thanh toán</h3>
                        <p class="text-sm text-yellow-700 mb-3">
                            @if($reservation->payment_type === 'online')
                                Vui lòng thanh toán để xác nhận đặt bàn.
                            @else
                                Bạn sẽ thanh toán tại quán khi đến.
                            @endif
                        </p>
                        @if($reservation->payment_type === 'online')
                        <a href="{{ route('reservations.payment', $reservation->id) }}" 
                           class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                            Thanh toán ngay
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($reservation->payment_status === 'paid')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-green-800">Đã thanh toán</h3>
                        <p class="text-sm text-green-700">
                            Thanh toán lúc {{ $reservation->payment_completed_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Thông tin bàn</h3>
                    <p class="text-lg">{{ $reservation->table->table_name }}</p>
                    <p class="text-sm text-gray-600">{{ $reservation->guest_count }} khách</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Thời gian</h3>
                    <p>Từ: {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</p>
                    <p>Đến: {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                    <p class="text-sm text-gray-600">Thời lượng: {{ $reservation->duration }} phút</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="border-t pt-6 mb-8">
                <h3 class="font-semibold text-gray-700 mb-4">Thông tin khách hàng</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Họ tên</p>
                        <p class="font-medium">{{ $reservation->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Số điện thoại</p>
                        <p class="font-medium">{{ $reservation->customer_phone }}</p>
                    </div>
                    @if($reservation->customer_email)
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $reservation->customer_email }}</p>
                    </div>
                    @endif
                    @if($reservation->note)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Ghi chú</p>
                        <p class="font-medium">{{ $reservation->note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cost -->
            <div class="border-t pt-6">
                <h3 class="font-semibold text-gray-700 mb-4">Chi phí</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Tổng tiền bàn</span>
                        <span class="font-semibold">{{ number_format($reservation->total_amount) }}đ</span>
                    </div>
                    @if($reservation->deposit_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span>Đặt cọc</span>
                        <span class="text-green-600">-{{ number_format($reservation->deposit_amount) }}đ</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold text-elegant-navy pt-2 border-t">
                        <span>
                            @if($reservation->payment_status === 'paid')
                                Đã thanh toán
                            @else
                                Cần thanh toán
                            @endif
                        </span>
                        <span>{{ number_format($reservation->total_amount - $reservation->deposit_amount) }}đ</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex gap-3">
                <a href="{{ route('reservations.track') }}" 
                   class="flex-1 text-center bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg transition">
                    Quay lại
                </a>
                
                @if($reservation->payment_status === 'pending' && $reservation->payment_type === 'online')
                    <a href="{{ route('reservations.payment', $reservation->id) }}"
                       class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition">
                        Thanh toán ngay
                    </a>
                @endif

                @if($reservation->status === 'confirmed' && $reservation->canCheckIn())
                    <button onclick="openCheckinModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition">
                        Check-in ngay
                    </button>
                @endif
                
                @if(in_array($reservation->status, ['pending', 'confirmed']))
                    <button onclick="openCancelModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg transition">
                        Hủy đặt bàn
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" id="checkinModal">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Check-in Online</h3>
        </div>
        
        <p class="text-gray-600 mb-2">Bạn có chắc muốn check-in cho đặt bàn?</p>
        <p class="font-semibold text-elegant-gold mb-4" id="checkinReservationCode"></p>
        <p class="text-sm text-gray-500 mb-6">Thao tác này sẽ thông báo cho quán biết bạn đã đến.</p>
        
        <div class="flex gap-3">
            <button onclick="closeCheckinModal()" 
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                Hủy
            </button>
            <button onclick="confirmCheckin()" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                Xác nhận Check-in
            </button>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" id="cancelModal">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Hủy đặt bàn</h3>
        </div>
        
        <p class="text-gray-600 mb-2">Bạn có chắc muốn hủy đặt bàn?</p>
        <p class="font-semibold text-elegant-gold mb-4" id="cancelReservationCode"></p>
        
        <div class="mb-4">
            <label for="cancelReason" class="block text-sm font-medium text-gray-700 mb-2">Lý do hủy (tùy chọn)</label>
            <textarea id="cancelReason" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                      placeholder="Nhập lý do hủy đặt bàn..."></textarea>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeCancelModal()" 
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                Đóng
            </button>
            <button onclick="confirmCancel()" 
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                Xác nhận Hủy
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentReservationId = {{ $reservation->id }};

// Modal functions
function openCheckinModal(reservationId, reservationCode) {
    currentReservationId = reservationId;
    document.getElementById('checkinReservationCode').textContent = reservationCode;
    document.getElementById('checkinModal').classList.remove('hidden');
}

function closeCheckinModal() {
    document.getElementById('checkinModal').classList.add('hidden');
}

function openCancelModal(reservationId, reservationCode) {
    currentReservationId = reservationId;
    document.getElementById('cancelReservationCode').textContent = reservationCode;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Confirm actions
async function confirmCheckin() {
    try {
        const response = await fetch(`/api/reservations/${currentReservationId}/checkin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Check-in thành công!');
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi check-in');
    }
    
    closeCheckinModal();
}

async function confirmCancel() {
    const reason = document.getElementById('cancelReason').value;
    
    try {
        const response = await fetch(`/api/reservations/${currentReservationId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Hủy đặt bàn thành công!');
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi hủy đặt bàn');
    }
    
    closeCancelModal();
}

// Close modals when clicking outside
document.getElementById('checkinModal').addEventListener('click', function(e) {
    if (e.target === this) closeCheckinModal();
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endsection