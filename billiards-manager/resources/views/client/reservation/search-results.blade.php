@extends('layouts.customer')

@section('title', 'Kết quả tìm kiếm - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-elegant-navy mb-2">Kết quả tìm kiếm</h1>
            <p class="text-gray-600">Tìm thấy {{ $reservations->count() }} đặt bàn</p>
        </div>

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('reservations.track') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Tìm kiếm lại
            </a>
        </div>

        <!-- Reservations List -->
        <div class="space-y-4">
            @foreach($reservations as $reservation)
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <!-- Left: Info -->
                    <div class="flex-1 mb-4 md:mb-0">
                        <div class="flex items-center mb-2">
                            <h3 class="text-xl font-bold text-gray-800 mr-3">{{ $reservation->reservation_code }}</h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($reservation->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($reservation->status === 'checked_in') bg-green-100 text-green-800
                                @elseif($reservation->status === 'completed') bg-gray-100 text-gray-800
                                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-gray-600 mt-3">
                            <div>
                                <i class="fas fa-table text-blue-600 mr-2"></i>
                                {{ $reservation->table->table_name }}
                            </div>
                            <div>
                                <i class="fas fa-calendar text-blue-600 mr-2"></i>
                                {{ $reservation->reservation_time->format('d/m/Y') }}
                            </div>
                            <div>
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                {{ $reservation->reservation_time->format('H:i') }}
                            </div>
                            <div>
                                <i class="fas fa-users text-blue-600 mr-2"></i>
                                {{ $reservation->guest_count }} người
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="mt-3">
                            @if($reservation->payment_status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Chưa thanh toán
                            </span>
                            @elseif($reservation->payment_status === 'paid')
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                <i class="fas fa-check-circle mr-1"></i>
                                Đã thanh toán
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex flex-col gap-2 md:ml-4">
                        <a href="{{ route('reservations.show', $reservation->id) }}" 
                           class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center font-medium rounded-lg transition">
                            Xem chi tiết
                        </a>
                        
                        @if($reservation->payment_status === 'pending' && $reservation->payment_type === 'online')
                        <a href="{{ route('reservations.payment', $reservation->id) }}" 
                           class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-center font-medium rounded-lg transition">
                            Thanh toán ngay
                        </a>
                        @endif

                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                        <button onclick="cancelReservation({{ $reservation->id }}, '{{ $reservation->reservation_code }}')"
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white text-center font-medium rounded-lg transition">
                            Hủy đặt bàn
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Amount -->
                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                    <span class="text-gray-600">Tổng tiền:</span>
                    <span class="text-xl font-bold text-blue-600">{{ number_format($reservation->total_amount) }}đ</span>
                </div>
            </div>
            @endforeach
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
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="Nhập lý do hủy đặt bàn..."></textarea>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeCancelModal()" 
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 rounded-lg transition">
                Đóng
            </button>
            <button onclick="confirmCancel()" 
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-lg transition">
                Xác nhận Hủy
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentReservationId = null;

function cancelReservation(id, code) {
    currentReservationId = id;
    document.getElementById('cancelReservationCode').textContent = code;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    currentReservationId = null;
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

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endsection