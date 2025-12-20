{{-- resources/views/admin/pos-dashboard.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Thống kê bàn')

@section('styles')
<style>
    .animate-float-in {
        animation: floatIn 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    @keyframes floatIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .scroll-touch {
        -webkit-overflow-scrolling: touch;
    }
    .table-status-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 10px;
        padding: 2px 8px;
    }
    .table-card {
        position: relative;
        transition: all 0.3s ease;
    }
    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Notification Area -->
    <div id="notification-area" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

    <!-- Quick Stats - CHỈ HIỂN THỊ THỐNG KÊ BÀN -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Tổng số bàn -->
        <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg shadow-lg animate-float-in" data-delay="0">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Tổng số bàn</p>
                    <h3 class="text-2xl font-bold mt-1" id="totalTables">{{ $tableStats['total'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-table text-3xl opacity-80"></i>
            </div>
        </div>

        <!-- Bàn đang dùng -->
        <div class="stat-card bg-gradient-to-r from-red-500 to-orange-600 text-white p-4 rounded-lg shadow-lg animate-float-in" data-delay="100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Bàn đang dùng</p>
                    <h3 class="text-2xl font-bold mt-1" id="occupiedTables">{{ $tableStats['occupied'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-users text-3xl opacity-80"></i>
            </div>
        </div>

        <!-- Bàn trống -->
        <div class="stat-card bg-gradient-to-r from-green-500 to-emerald-600 text-white p-4 rounded-lg shadow-lg animate-float-in" data-delay="200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Bàn trống</p>
                    <h3 class="text-2xl font-bold mt-1" id="availableTables">{{ $tableStats['available'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-chair text-3xl opacity-80"></i>
            </div>
        </div>

        <!-- Bàn đã đặt -->
        <div class="stat-card bg-gradient-to-r from-yellow-500 to-amber-600 text-white p-4 rounded-lg shadow-lg animate-float-in" data-delay="300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">Bàn đã đặt</p>
                    <h3 class="text-2xl font-bold mt-1" id="reservedTables">{{ $tableStats['reserved'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-calendar-check text-3xl opacity-80"></i>
            </div>
        </div>

        <!-- Tỷ lệ sử dụng -->
        <div class="stat-card bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-4 rounded-lg shadow-lg animate-float-in" data-delay="400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Tỷ lệ sử dụng</p>
                    <h3 class="text-2xl font-bold mt-1" id="occupancyRate">{{ $tableStats['occupancy_rate'] ?? 0 }}%</h3>
                </div>
                <i class="fas fa-chart-line text-3xl opacity-80"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - 2/3 width -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Danh sách bàn trống -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="600">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chair text-green-500 mr-2"></i>
                        Bàn trống
                        <span class="ml-2 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $availableCount ?? 0 }} bàn
                        </span>
                    </h3>
                    <a href="{{ route('admin.tables.simple-dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-external-link-alt mr-1"></i> Xem tất cả
                    </a>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="available-tables-container">
                        @forelse($availableTables as $table)
                            <a href="{{ route('admin.tables.detail', $table->id) }}"
                               class="table-card p-4 bg-green-50 border-2 border-green-200 hover:border-green-400 hover:bg-green-100 rounded-lg text-center transition relative">
                                <span class="table-status-badge bg-green-500 text-white rounded-full">Trống</span>
                                <i class="fas fa-table text-green-600 text-3xl mb-2"></i>
                                <p class="font-bold text-gray-800 text-sm">{{ $table->table_name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                <div class="mt-3">
                                    <span class="inline-block w-full py-1 bg-green-500 text-white text-xs rounded">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full text-center py-10 text-gray-500">
                                <i class="fas fa-chair text-5xl opacity-50 mb-4"></i>
                                <p>Không có bàn trống nào</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Danh sách bàn đang dùng -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="800">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-users text-red-500 mr-2"></i>
                        Bàn đang dùng
                        <span class="ml-2 bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $occupiedCount ?? 0 }} bàn
                        </span>
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="occupied-tables-container">
                        @forelse($occupiedTables as $table)
                            <div class="table-card p-4 bg-red-50 border-2 border-red-200 rounded-lg transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-table text-red-600 mr-2"></i>
                                            {{ $table->table_name }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                    </div>
                                    <span class="bg-red-500 text-white px-3 py-1 text-xs rounded-full">
                                        Đang dùng
                                    </span>
                                </div>
                                
                                @if($table->current_bill)
                                <div class="space-y-2 text-sm mb-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Mã HĐ:</span>
                                        <a href="{{ route('admin.bills.show', $table->current_bill) }}" 
                                           class="font-medium text-blue-600 hover:text-blue-800">
                                            #{{ $table->current_bill }}
                                        </a>
                                    </div>
                                    @if($table->start_time)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Bắt đầu:</span>
                                        <span class="font-medium">{{ $table->start_time }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <a href="{{ route('admin.tables.detail', $table->id) }}"
                                       class="py-2 bg-blue-600 hover:bg-blue-700 text-white text-center text-xs rounded transition">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết bàn
                                    </a>
                                    @if($table->current_bill)
                                    <a href="{{ route('admin.bills.show', $table->current_bill) }}"
                                       class="py-2 bg-green-600 hover:bg-green-700 text-white text-center text-xs rounded transition">
                                        <i class="fas fa-receipt mr-1"></i> Xem HĐ
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 text-gray-500">
                                <i class="fas fa-table text-5xl opacity-50 mb-4"></i>
                                <p>Không có bàn đang dùng</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - 1/3 width -->
        <div class="space-y-6">

            <!-- Danh sách tất cả bàn -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="1000">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center justify-between">
                        <span>
                            <i class="fas fa-list text-blue-500 mr-2"></i>
                            Tất cả bàn
                        </span>
                        <span class="text-sm font-medium text-gray-600">{{ $totalTables ?? 0 }} bàn</span>
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3 max-h-96 overflow-y-auto scroll-touch" id="all-tables-list">
                        @forelse($tables as $table)
                            <div class="p-3 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                                            @if($table->status == 'available') bg-green-100 text-green-600
                                            @elseif($table->status == 'occupied') bg-red-100 text-red-600
                                            @elseif($table->status == 'reserved') bg-yellow-100 text-yellow-600
                                            @elseif($table->status == 'maintenance') bg-gray-100 text-gray-600
                                            @endif">
                                            <i class="fas fa-table"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-sm">{{ $table->table_name }}</p>
                                            <p class="text-xs text-gray-600">{{ $table->capacity }} người</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($table->status == 'available')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 text-xs rounded-full">Trống</span>
                                        @elseif($table->status == 'occupied')
                                            <span class="bg-red-100 text-red-800 px-2 py-1 text-xs rounded-full">Đang dùng</span>
                                        @elseif($table->status == 'reserved')
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 text-xs rounded-full">Đã đặt</span>
                                        @elseif($table->status == 'maintenance')
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 text-xs rounded-full">Bảo trì</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 text-right">
                                    <a href="{{ route('admin.tables.detail', $table->id) }}"
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium inline-flex items-center">
                                        Chi tiết <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-table text-4xl opacity-50 mb-3"></i>
                                <p>Không có dữ liệu bàn</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Hóa đơn đang mở -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="1200">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center justify-between">
                        <span>
                            <i class="fas fa-receipt text-purple-500 mr-2"></i>
                            Hóa đơn đang mở
                        </span>
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $openBills->count() ?? 0 }} hóa đơn
                        </span>
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3" id="open-bills-container">
                        @forelse($openBills as $bill)
                            <a href="{{ route('admin.bills.show', $bill->id) }}"
                               class="block p-3 bg-purple-50 border border-purple-200 hover:bg-purple-100 rounded-lg transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-receipt text-purple-600 mr-2"></i>
                                            <p class="font-medium text-gray-800 truncate">#{{ $bill->bill_number }}</p>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-table mr-1"></i>
                                            Bàn: {{ $bill->table_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($bill->start_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    <span class="bg-purple-500 text-white px-2 py-1 text-xs rounded-full ml-2 flex-shrink-0">
                                        Mở
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-receipt text-4xl opacity-50 mb-3"></i>
                                <p class="text-sm">Không có hóa đơn đang mở</p>
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
    function showNotification(message, type = 'success', duration = 4000) {
        const area = document.getElementById('notification-area');
        const id = 'notif-' + Date.now();
        const notif = document.createElement('div');
        notif.id = id;
        notif.className = `p-4 rounded-lg shadow-lg border-l-4 animate-bounce-in text-sm ${
            type === 'success' ? 'bg-green-50 border-green-500 text-green-800' :
            type === 'error' ? 'bg-red-50 border-red-500 text-red-800' :
            type === 'warning' ? 'bg-yellow-50 border-yellow-500 text-yellow-800' :
            'bg-blue-50 border-blue-500 text-blue-800'
        }`;

        notif.innerHTML = `
            <div class="flex justify-between items-center">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        area.appendChild(notif);

        setTimeout(() => {
            if (document.getElementById(id)) {
                notif.style.opacity = '0';
                notif.style.transform = 'translateX(100%)';
                setTimeout(() => notif.remove(), 300);
            }
        }, duration);
    }

    function initializeAnimations() {
        document.querySelectorAll('[data-delay]').forEach(el => {
            const delay = parseInt(el.getAttribute('data-delay'));
            el.style.animationDelay = delay + 'ms';
            el.classList.add('animate-float-in');
        });
    }

    // Cập nhật thống kê realtime
    function updateTableStats() {
        fetch('{{ route("admin.pos.quick-stats") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Cập nhật các số thống kê
                    animateNumber('totalTables', data.tableStats.total);
                    animateNumber('occupiedTables', data.tableStats.occupied);
                    animateNumber('availableTables', data.tableStats.available);
                    animateNumber('reservedTables', data.tableStats.reserved);
                    
                    // Cập nhật tỷ lệ sử dụng
                    const occupancyEl = document.getElementById('occupancyRate');
                    if (occupancyEl) {
                        occupancyEl.textContent = data.tableStats.occupancy_rate + '%';
                    }
                }
            })
            .catch(err => {
                console.error('Error updating stats:', err);
                // Có thể thêm thông báo lỗi ở đây
            });
    }

    function animateNumber(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const currentText = element.textContent;
        const currentValue = parseInt(currentText.replace(/[^\d]/g, '')) || 0;
        
        if (currentValue === newValue) return;
        
        const duration = 800;
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(currentValue + (newValue - currentValue) * easeOut);
            
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = newValue;
            }
        }
        
        requestAnimationFrame(update);
    }

    // Auto refresh mỗi 30 giây
    function startAutoRefresh() {
        setInterval(updateTableStats, 30000); // 30 giây
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeAnimations();
        
        // Kiểm tra nếu có route cho quick stats thì bật auto refresh
        if (typeof route !== 'undefined' && route('admin.pos.quick-stats')) {
            startAutoRefresh();
        }
        
        // Thêm nút refresh thủ công
        const refreshButton = document.createElement('button');
        refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i>';
        refreshButton.className = 'fixed bottom-6 right-6 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition z-50 focus:outline-none focus:ring-2 focus:ring-blue-300';
        refreshButton.title = 'Làm mới thống kê';
        refreshButton.setAttribute('aria-label', 'Làm mới thống kê');
        refreshButton.onclick = function() {
            this.classList.add('animate-spin');
            updateTableStats();
            setTimeout(() => {
                this.classList.remove('animate-spin');
            }, 1000);
        };
        document.body.appendChild(refreshButton);
        
        // Hiển thị thông báo chào mừng sau khi trang tải xong
        setTimeout(() => {
            showNotification('POS Dashboard đã sẵn sàng!', 'success', 3000);
        }, 1000);
    });
</script>
@endsection