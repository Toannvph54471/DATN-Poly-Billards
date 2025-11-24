@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Poly Billiards')

@section('styles')
<style>
    .stat-card {
        min-height: 70px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .quick-action-btn {
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
    }

    .table-card {
        min-height: 80px;
        transition: all 0.3s ease;
    }

    .table-card:hover {
        transform: translateY(-2px);
    }

    .bill-card {
        min-height: 100px;
        transition: all 0.3s ease;
    }

    .bill-card:hover {
        transform: translateY(-2px);
    }

    /* Mobile responsive improvements */
    @media (max-width: 640px) {
        .stat-card {
            min-height: 60px;
            padding: 12px;
        }

        .stat-card h3 {
            font-size: 1.25rem;
        }

        .stat-card i {
            font-size: 1.25rem;
        }

        .quick-action-btn {
            min-height: 50px;
            padding: 8px;
        }

        .quick-action-btn i {
            font-size: 1rem;
        }

        .table-card {
            min-height: 70px;
            padding: 12px;
        }

        .bill-card {
            min-height: 90px;
            padding: 12px;
        }

        .main-grid {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .stat-card p {
            font-size: 0.75rem;
        }

        .stat-card h3 {
            font-size: 1.125rem;
        }

        .quick-action-btn p {
            font-size: 0.7rem;
        }
    }

    /* Touch improvements */
    button, 
    a, 
    .clickable {
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    /* Better scrolling on mobile */
    .scroll-touch {
        -webkit-overflow-scrolling: touch;
    }

    /* Custom animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
        <!-- Open Bills -->
        <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs md:text-sm">Bills đang mở</p>
                    <h3 class="text-lg md:text-2xl font-bold mt-1" id="openBillsCount">
                        {{ $stats['open_bills'] ?? 0 }}
                    </h3>
                </div>
                <i class="fas fa-receipt text-lg md:text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- Occupied Tables -->
        <div class="stat-card bg-gradient-to-r from-orange-500 to-amber-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-xs md:text-sm">Bàn đang dùng</p>
                    <h3 class="text-lg md:text-2xl font-bold mt-1" id="occupiedTables">
                        {{ $stats['occupied_tables'] ?? 0 }}
                    </h3>
                </div>
                <i class="fas fa-table text-lg md:text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- Available Tables -->
        <div class="stat-card bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-xs md:text-sm">Bàn trống</p>
                    <h3 class="text-lg md:text-2xl font-bold mt-1">{{ $stats['available_tables'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-chair text-lg md:text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- Reservations -->
        <div class="stat-card bg-gradient-to-r from-pink-500 to-rose-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-fade-in" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-xs md:text-sm">Đặt bàn hôm nay</p>
                    <h3 class="text-lg md:text-2xl font-bold mt-1">{{ $stats['pending_reservations'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-calendar-check text-lg md:text-2xl opacity-80"></i>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 main-grid">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-4 md:space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                <div class="p-3 md:p-4 border-b border-gray-200">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Thao tác nhanh
                    </h3>
                </div>
                <div class="p-3 md:p-4">
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-2 md:gap-3">
                        <!-- Tạo Bill Nhanh -->
                        <a href="{{ route('admin.bills.index') }}"
                           class="quick-action-btn p-2 md:p-3 bg-blue-50 hover:bg-blue-100 transition-colors group border-2 border-blue-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-plus-circle text-blue-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Hóa đơn</p>
                            </div>
                        </a>

                        <!-- Đặt Bàn -->
                        <a href="{{ route('admin.reservations.create') }}"
                           class="quick-action-btn p-2 md:p-3 bg-green-50 hover:bg-green-100 transition-colors group border-2 border-green-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-calendar-plus text-green-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Đặt bàn</p>
                            </div>
                        </a>

                        <!-- Quản lý Bàn -->
                        <a href="{{ route('admin.tables.simple-dashboard') }}"
                           class="quick-action-btn p-2 md:p-3 bg-purple-50 hover:bg-purple-100 transition-colors group border-2 border-purple-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-table-cells text-purple-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Quản lý Bàn</p>
                            </div>
                        </a>

                        <!-- Danh sách Đặt Bàn -->
                        <a href="{{ route('admin.reservations.index') }}"
                           class="quick-action-btn p-2 md:p-3 bg-orange-50 hover:bg-orange-100 transition-colors group border-2 border-orange-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-list text-orange-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">DS Đặt Bàn</p>
                            </div>
                        </a>

                        <!-- Thanh Toán -->
                        <button onclick="openPaymentPage()"
                                class="quick-action-btn p-2 md:p-3 bg-red-50 hover:bg-red-100 transition-colors group border-2 border-red-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-credit-card text-red-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Thanh Toán</p>
                            </div>
                        </button>

                        <!-- Chuyển Bàn -->
                        <button onclick="openTransferTable()"
                                class="quick-action-btn p-2 md:p-3 bg-indigo-50 hover:bg-indigo-100 transition-colors group border-2 border-indigo-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-exchange-alt text-indigo-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Chuyển Bàn</p>
                            </div>
                        </button>

                        <!-- Check-in Bàn -->
                        <button onclick="openCheckinTable()"
                                class="quick-action-btn p-2 md:p-3 bg-teal-50 hover:bg-teal-100 transition-colors group border-2 border-teal-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-sign-in-alt text-teal-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Check-in</p>
                            </div>
                        </button>

                        <!-- Check-out Bàn -->
                        <button onclick="openCheckoutTable()"
                                class="quick-action-btn p-2 md:p-3 bg-amber-50 hover:bg-amber-100 transition-colors group border-2 border-amber-200 rounded-lg text-center">
                            <div>
                                <i class="fas fa-sign-out-alt text-amber-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">Check-out</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bàn trống -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                <div class="p-3 md:p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">
                        <i class="fas fa-chair text-green-500 mr-2"></i>
                        Bàn trống
                    </h3>
                    <span class="bg-green-100 text-green-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium">
                        {{ $availableTables->count() ?? 0 }} bàn
                    </span>
                </div>
                <div class="p-3 md:p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                        @forelse($availableTables as $table)
                            <button onclick="selectTable({{ $table->id }})"
                                    class="table-card p-3 md:p-4 bg-green-50 border-2 border-green-200 hover:border-green-400 hover:bg-green-100 transition-colors rounded-lg text-center group">
                                <i class="fas fa-table text-green-600 text-base md:text-xl group-hover:scale-110 transition-transform mb-1 md:mb-2"></i>
                                <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">{{ $table->table_name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                <div class="mt-1 md:mt-2">
                                    <span class="bg-green-500 text-white px-2 py-1 text-xs rounded-full">Trống</span>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-2 sm:col-span-3 lg:col-span-4 text-center py-6 md:py-8 text-gray-500">
                                <i class="fas fa-table text-2xl md:text-4xl mb-2 md:mb-3 opacity-50"></i>
                                <p class="text-sm md:text-base">Không có bàn trống</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4 md:space-y-6">
            <!-- Trạng thái Bill -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                <div class="p-3 md:p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">
                        <i class="fas fa-receipt text-blue-500 mr-2"></i>
                        Trạng thái Bill
                    </h3>
                    <span class="bg-blue-100 text-blue-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium">
                        {{ $openBills->count() ?? 0 }} bill
                    </span>
                </div>
                <div class="p-3 md:p-4">
                    <div class="space-y-3 max-h-80 md:max-h-96 overflow-y-auto scroll-touch">
                        @forelse($openBills as $bill)
                            <div class="bill-card border-2 border-blue-200 p-3 md:p-4 bg-blue-50 hover:bg-blue-100 transition-colors rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 flex items-center text-sm md:text-base">
                                            <i class="fas fa-receipt text-blue-600 mr-2"></i>
                                            <span class="truncate">Bill #{{ $bill->bill_number }}</span>
                                        </h4>
                                        <p class="text-xs md:text-sm text-gray-600 truncate">
                                            Bàn: <strong>{{ $bill->table->table_name ?? 'N/A' }}</strong>
                                        </p>
                                        <p class="text-xs md:text-sm text-gray-600 truncate">
                                            Khách: <strong>{{ $bill->user->name ?? 'Khách vãng lai' }}</strong>
                                        </p>
                                    </div>
                                    <span class="bg-blue-500 text-white px-2 py-1 text-xs md:text-sm rounded-full ml-2 flex-shrink-0">
                                        {{ $bill->status }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                    <div>
                                        <p class="truncate">Bắt đầu: {{ \Carbon\Carbon::parse($bill->start_time)->format('H:i') }}</p>
                                        <p class="truncate">Thời gian: {{ \Carbon\Carbon::parse($bill->start_time)->diffForHumans(['parts' => 1, 'short' => true]) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p>Tổng tiền:</p>
                                        <p class="font-semibold text-green-600 text-sm">
                                            {{ number_format($bill->total_amount ?? 0, 0, ',', '.') }}₫
                                        </p>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.tables.detail', $bill->table_id) }}"
                                       class="flex-1 px-2 md:px-3 py-1 bg-blue-500 text-white hover:bg-blue-600 text-xs transition-colors rounded text-center">
                                        <i class="fas fa-eye mr-1"></i>Xem
                                    </a>
                                    <a href="{{ route('admin.payments.payment-page', $bill) }}"
                                       class="flex-1 px-2 md:px-3 py-1 bg-green-500 text-white hover:bg-green-600 text-xs transition-colors rounded text-center">
                                        <i class="fas fa-credit-card mr-1"></i>Thanh toán
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 md:py-8 text-gray-500">
                                <i class="fas fa-receipt text-2xl md:text-4xl mb-2 md:mb-3 opacity-50"></i>
                                <p class="text-sm md:text-base">Không có bill nào đang mở</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Today's Reservations -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                <div class="p-3 md:p-4 border-b border-gray-200">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-day text-purple-500 mr-2"></i>
                        Đặt bàn hôm nay
                    </h3>
                </div>
                <div class="p-3 md:p-4">
                    <div class="space-y-2 md:space-y-3">
                        @forelse($todayReservations as $reservation)
                            <div class="flex items-center justify-between p-2 md:p-3 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors rounded-lg cursor-pointer text-sm"
                                 onclick="viewReservation({{ $reservation->id }})">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm truncate">{{ $reservation->customer_name }}</p>
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                        - Bàn {{ $reservation->table->table_name }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <span class="bg-purple-500 text-white px-2 py-1 text-xs rounded-full block mb-1">
                                        {{ $reservation->status }}
                                    </span>
                                    @if ($reservation->status === 'confirmed')
                                        <button onclick="checkinReservation({{ $reservation->id }}, event)"
                                                class="bg-green-500 text-white px-2 py-1 hover:bg-green-600 transition-colors text-xs rounded w-full">
                                            Check-in
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500 text-sm">
                                <i class="fas fa-calendar-times opacity-50"></i>
                                <p class="mt-1">Không có đặt bàn nào</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Các hàm xử lý action
    function openPaymentPage() {
        Swal.fire({
            title: 'Thanh toán',
            text: 'Chức năng Thanh toán - Cần chọn bill cụ thể từ danh sách bàn đang sử dụng',
            icon: 'info',
            confirmButtonText: 'Đã hiểu'
        });
    }

    function openTransferTable() {
        Swal.fire({
            title: 'Chuyển bàn',
            text: 'Chức năng Chuyển Bàn - Cần chọn bill cụ thể từ danh sách bàn đang sử dụng',
            icon: 'info',
            confirmButtonText: 'Đã hiểu'
        });
    }

    function openCheckinTable() {
        window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
    }

    function openCheckoutTable() {
        window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
    }

    function selectTable(tableId) {
        // Redirect đến trang tạo bill với bàn đã chọn
        window.location.href = '{{ route('admin.bills.create') }}?table_id=' + tableId;
    }

    function viewReservation(reservationId) {
        window.location.href = `/admin/reservations/${reservationId}`;
    }

    function checkinReservation(reservationId, event) {
        event.stopPropagation();
        
        Swal.fire({
            title: 'Xác nhận check-in?',
            text: 'Bạn có chắc muốn check-in cho đặt bàn này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Check-in',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/reservations/${reservationId}/checkin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Check-in thành công!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: data.message || 'Có lỗi xảy ra',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi check-in',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }

    // Auto-refresh stats every 30 seconds
    setInterval(() => {
        fetch('{{ route('admin.pos.dashboard') }}')
            .then(response => response.json())
            .then(data => {
                if (document.getElementById('openBillsCount')) {
                    document.getElementById('openBillsCount').textContent = data.open_bills;
                }
                if (document.getElementById('occupiedTables')) {
                    document.getElementById('occupiedTables').textContent = data.occupied_tables;
                }
            })
            .catch(error => {
                console.error('Error updating stats:', error);
            });
    }, 30000);

    // Initialize animations and touch improvements
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects
        const cards = document.querySelectorAll('.stat-card, .quick-action-btn, .table-card, .bill-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Touch improvements for mobile
        document.addEventListener('touchstart', function() {}, { passive: true });

        // Better button feedback on mobile
        document.addEventListener('touchstart', function(e) {
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                e.target.style.transform = 'scale(0.98)';
            }
        });

        document.addEventListener('touchend', function(e) {
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                e.target.style.transform = 'scale(1)';
            }
        });
    });
</script>
@endsection