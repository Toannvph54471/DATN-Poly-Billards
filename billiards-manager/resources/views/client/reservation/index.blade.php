@extends('layouts.app')

@section('title', 'Lịch sử đặt bàn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-display font-bold text-elegant-navy mb-4">Lịch sử đặt bàn</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Quản lý và theo dõi tất cả các đặt bàn của bạn tại Poly Billiards</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tổng đặt bàn</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $reservations->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Đã hoàn thành</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $reservations->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sắp tới</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingReservations->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-star text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tổng chi tiêu</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSpent) }}đ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-8">
            <div class="flex flex-wrap gap-2 mb-6">
                <button class="filter-tab px-4 py-2 bg-elegant-gold text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="all">
                    Tất cả
                </button>
                <button class="filter-tab px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="upcoming">
                    Sắp tới
                </button>
                <button class="filter-tab px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="pending">
                    Chờ xác nhận
                </button>
                <button class="filter-tab px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="confirmed">
                    Đã xác nhận
                </button>
                <button class="filter-tab px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="completed">
                    Đã hoàn thành
                </button>
                <button class="filter-tab px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-status="cancelled">
                    Đã hủy
                </button>
            </div>

            <!-- Search Box -->
            <div class="relative max-w-md">
                <input type="text" 
                       id="reservationSearch" 
                       placeholder="Tìm kiếm theo mã đặt bàn, tên bàn..."
                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Reservations List -->
        <div class="space-y-6" id="reservationsList">
            @if($reservations->count() > 0)
                @foreach($reservations as $reservation)
                    @include('partials.reservation-card', ['reservation' => $reservation])
                @endforeach
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-200">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-4">Chưa có đặt bàn nào</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Hãy thực hiện đặt bàn đầu tiên của bạn để trải nghiệm dịch vụ tuyệt vời tại Poly Billiards</p>
                    <a href="{{ route('reservation.create') }}" 
                       class="inline-block bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-4 rounded-lg transition duration-200 transform hover:scale-105">
                        <i class="fas fa-calendar-plus mr-3"></i>
                        Đặt bàn ngay
                    </a>
                </div>
            @endif
        </div>

        <!-- Upcoming Reservations Section -->
        @if($upcomingReservations->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-semibold text-elegant-navy mb-6">Đặt bàn sắp tới</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingReservations as $reservation)
                <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-elegant-gold">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $reservation->table->table_name }}</h3>
                            <p class="text-sm text-gray-500">Mã: {{ $reservation->reservation_code }}</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            Sắp tới
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar-day w-4 mr-2"></i>
                            {{ $reservation->reservation_time->format('d/m/Y') }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-clock w-4 mr-2"></i>
                            {{ $reservation->reservation_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-users w-4 mr-2"></i>
                            {{ $reservation->guest_count }} người
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if($reservation->status === 'confirmed' && $reservation->canCheckIn())
                            <button onclick="openCheckinModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Check-in
                            </button>
                        @endif

                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <button onclick="openCancelModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>
                                Hủy
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
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
let currentReservationId = null;

// Filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const status = this.getAttribute('data-status');
        
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(t => {
            t.classList.remove('bg-elegant-gold', 'text-elegant-navy');
            t.classList.add('bg-gray-200', 'text-gray-700');
        });
        this.classList.remove('bg-gray-200', 'text-gray-700');
        this.classList.add('bg-elegant-gold', 'text-elegant-navy');
        
        // Filter reservations
        filterReservations(status);
    });
});

// Search functionality
document.getElementById('reservationSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterReservations('search', searchTerm);
});

function filterReservations(status, searchTerm = '') {
    const reservations = document.querySelectorAll('.reservation-card');
    
    reservations.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const cardText = card.textContent.toLowerCase();
        
        let shouldShow = true;
        
        if (status === 'search' && searchTerm) {
            shouldShow = cardText.includes(searchTerm);
        } else if (status !== 'all' && status !== 'search') {
            shouldShow = cardStatus === status;
        }
        
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

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