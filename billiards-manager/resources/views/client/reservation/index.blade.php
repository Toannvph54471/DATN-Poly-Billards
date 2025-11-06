@extends('layouts.customer')

@section('title', 'Theo dõi đặt bàn')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Theo dõi đặt bàn</h4>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Search Form for non-logged in users -->
                    @guest
                    <div class="search-section mb-5">
                        <h5 class="mb-3">Tra cứu đặt bàn</h5>
                        <form id="searchForm" class="row g-3">
                            <div class="col-md-6">
                                <label for="search_phone" class="form-label">Số điện thoại *</label>
                                <input type="tel" class="form-control" id="search_phone" 
                                       placeholder="Nhập số điện thoại đã đặt" required>
                            </div>
                            <div class="col-md-6">
                                <label for="search_code" class="form-label">Mã đặt bàn</label>
                                <input type="text" class="form-control" id="search_code" 
                                       placeholder="Nhập mã đặt bàn (nếu có)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tra cứu
                                </button>
                            </div>
                        </form>
                    </div>
                    @endguest

                    <!-- Reservations List -->
                    <div id="reservationsList">
                        <h5 class="mb-4">Đặt bàn của tôi</h5>
                        
                        @auth
                            <!-- For logged in users -->
                            @if($reservations->count() > 0)
                                @foreach($reservations as $reservation)
                                    @include('partials.reservation-card', ['reservation' => $reservation])
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5>Chưa có đặt bàn nào</h5>
                                    <p class="text-muted">Hãy đặt bàn đầu tiên của bạn!</p>
                                    <a href="{{ route('reservation.create') }}" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus me-1"></i>Đặt bàn ngay
                                    </a>
                                </div>
                            @endif
                        @else
                            <!-- For guests - will be populated by JavaScript -->
                            <div class="text-center py-4">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5>Vui lòng tra cứu đặt bàn</h5>
                                <p class="text-muted">Nhập số điện thoại để xem các đặt bàn của bạn</p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Check-in Online</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn check-in cho đặt bàn <strong id="checkinReservationCode"></strong>?</p>
                <p class="text-muted small">Thao tác này sẽ thông báo cho quán biết bạn đã đến.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmCheckin">Xác nhận Check-in</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy đặt bàn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn hủy đặt bàn <strong id="cancelReservationCode"></strong>?</p>
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">Lý do hủy (tùy chọn)</label>
                    <textarea class="form-control" id="cancelReason" rows="3" 
                              placeholder="Nhập lý do hủy đặt bàn..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Xác nhận Hủy</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentReservationId = null;

// Search form handling
document.getElementById('searchForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        phone: document.getElementById('search_phone').value,
        reservation_code: document.getElementById('search_code').value
    };

    try {
        const response = await fetch('{{ route("api.reservations.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        displaySearchResults(data.reservations);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tra cứu');
    }
});

function displaySearchResults(reservations) {
    const container = document.getElementById('reservationsList');
    
    if (reservations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                <h5>Không tìm thấy đặt bàn</h5>
                <p class="text-muted">Vui lòng kiểm tra lại số điện thoại hoặc mã đặt bàn</p>
            </div>
        `;
        return;
    }

    let html = '<h5 class="mb-4">Kết quả tra cứu</h5>';
    
    reservations.forEach(reservation => {
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title mb-1">${reservation.table_name}</h6>
                            <p class="text-muted small mb-0">Mã: ${reservation.reservation_code}</p>
                        </div>
                        <span class="badge ${getStatusBadgeClass(reservation.status)}">
                            ${getStatusText(reservation.status)}
                        </span>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <small class="text-muted"><i class="fas fa-calendar me-1"></i>Ngày</small>
                            <p class="mb-0">${formatDate(reservation.reservation_time)}</p>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>Thời gian</small>
                            <p class="mb-0">${formatTime(reservation.reservation_time)} - ${formatTime(reservation.end_time)}</p>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted"><i class="fas fa-users me-1"></i>Số người</small>
                            <p class="mb-0">${reservation.guest_count} người</p>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted"><i class="fas fa-hourglass me-1"></i>Thời lượng</small>
                            <p class="mb-0">${Math.floor(reservation.duration / 60)} giờ</p>
                        </div>
                    </div>
                    
                    ${reservation.note ? `
                        <div class="mb-3">
                            <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>Ghi chú</small>
                            <p class="mb-0 small">${reservation.note}</p>
                        </div>
                    ` : ''}
                    
                    <div class="d-flex gap-2">
                        ${reservation.status === 'confirmed' && canCheckin(reservation.reservation_time) ? `
                            <button class="btn btn-success btn-sm" onclick="openCheckinModal(${reservation.id}, '${reservation.reservation_code}')">
                                <i class="fas fa-check-circle me-1"></i>Check-in
                            </button>
                        ` : ''}
                        
                        ${reservation.status === 'pending' || reservation.status === 'confirmed' ? `
                            <button class="btn btn-outline-danger btn-sm" onclick="openCancelModal(${reservation.id}, '${reservation.reservation_code}')">
                                <i class="fas fa-times me-1"></i>Hủy
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Modal functions
function openCheckinModal(reservationId, reservationCode) {
    currentReservationId = reservationId;
    document.getElementById('checkinReservationCode').textContent = reservationCode;
    new bootstrap.Modal(document.getElementById('checkinModal')).show();
}

function openCancelModal(reservationId, reservationCode) {
    currentReservationId = reservationId;
    document.getElementById('cancelReservationCode').textContent = reservationCode;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

// Confirm actions
document.getElementById('confirmCheckin')?.addEventListener('click', async function() {
    try {
        const response = await fetch(`/api/reservations/${currentReservationId}/checkin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    bootstrap.Modal.getInstance(document.getElementById('checkinModal')).hide();
});

document.getElementById('confirmCancel')?.addEventListener('click', async function() {
    const reason = document.getElementById('cancelReason').value;
    
    try {
        const response = await fetch(`/api/reservations/${currentReservationId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    bootstrap.Modal.getInstance(document.getElementById('cancelModal')).hide();
});

// Helper functions
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'bg-warning',
        'confirmed': 'bg-primary',
        'checked_in': 'bg-success',
        'completed': 'bg-secondary',
        'cancelled': 'bg-danger',
        'no_show': 'bg-dark'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusText(status) {
    const texts = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'checked_in': 'Đã check-in',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'no_show': 'Không đến'
    };
    return texts[status] || status;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('vi-VN');
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleTimeString('vi-VN', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

function canCheckin(reservationTime) {
    const now = new Date();
    const reservation = new Date(reservationTime);
    const diffMinutes = (reservation - now) / (1000 * 60);
    
    // Allow check-in 15 minutes before and 30 minutes after reservation time
    return diffMinutes <= 15 && diffMinutes >= -30;
}
</script>
@endsection