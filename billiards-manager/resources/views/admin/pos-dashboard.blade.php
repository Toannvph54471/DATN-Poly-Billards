@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Poly Billiards')

@section('styles')
    <style>
        /* ... (giữ nguyên các style khác) ... */
    </style>
@endsection

@section('content')
    <div class="space-y-4 md:space-y-6">
        <!-- Notification Area -->
        <div id="notification-area" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"></div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
            <!-- Open Bills -->
            <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-float-in"
                data-delay="0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs md:text-sm">Bills đang mở</p>
                        <h3 class="text-lg md:text-2xl font-bold mt-1" id="openBillsCount">
                            {{ $stats['open_bills'] ?? 0 }}
                        </h3>
                    </div>
                    <i class="fas fa-receipt text-lg md:text-2xl opacity-80"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-400 progress-bar"></div>
            </div>

            <!-- Occupied Tables -->
            <div class="stat-card bg-gradient-to-r from-orange-500 to-amber-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-float-in"
                data-delay="100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-xs md:text-sm">Bàn đang dùng</p>
                        <h3 class="text-lg md:text-2xl font-bold mt-1" id="occupiedTables">
                            {{ $stats['occupied_tables'] ?? 0 }}
                        </h3>
                    </div>
                    <i class="fas fa-table text-lg md:text-2xl opacity-80"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-400 progress-bar"></div>
            </div>

            <!-- Available Tables -->
            <div class="stat-card bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-float-in"
                data-delay="200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs md:text-sm">Bàn trống</p>
                        <h3 class="text-lg md:text-2xl font-bold mt-1">{{ $stats['available_tables'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-chair text-lg md:text-2xl opacity-80"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-purple-400 progress-bar"></div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 main-grid">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-4 md:space-y-6">
                <!-- Quick Actions - ĐÃ SỬA: Loại bỏ hiệu ứng animation -->
                <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                    <div class="p-3 md:p-4 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                            Thao tác nhanh
                        </h3>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="grid grid-cols-3 md:grid-cols-4 gap-2 md:gap-3">
                            @foreach ([['route' => 'admin.bills.index', 'color' => 'blue', 'icon' => 'plus-circle', 'text' => 'Hóa đơn'], ['route' => 'admin.reservations.create', 'color' => 'green', 'icon' => 'calendar-plus', 'text' => 'Đặt bàn'], ['route' => 'admin.tables.simple-dashboard', 'color' => 'purple', 'icon' => 'table-cells', 'text' => 'Quản lý Bàn'], ['route' => 'admin.reservations.index', 'color' => 'orange', 'icon' => 'list', 'text' => 'DS Đặt Bàn'], ['route' => 'admin.bills.index', 'color' => 'red', 'icon' => 'credit-card', 'text' => 'Thanh Toán'], ['route' => 'admin.tables.simple-dashboard', 'color' => 'indigo', 'icon' => 'exchange-alt', 'text' => 'Chuyển Bàn'], ['route' => 'admin.tables.simple-dashboard', 'color' => 'teal', 'icon' => 'sign-in-alt', 'text' => 'Check-in'], ['route' => 'admin.tables.simple-dashboard', 'color' => 'amber', 'icon' => 'sign-out-alt', 'text' => 'Check-out']] as $action)
                                <a href="{{ route($action['route']) }}"
                                    class="quick-action-btn p-2 md:p-3 bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors group border-2 border-{{ $action['color'] }}-200 rounded-lg text-center">
                                    <div>
                                        <i
                                            class="fas fa-{{ $action['icon'] }} text-{{ $action['color'] }}-600 text-base md:text-xl"></i>
                                        <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">
                                            {{ $action['text'] }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Bàn trống -->
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="900">
                    <div class="p-3 md:p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-chair text-green-500 mr-2"></i>
                            Bàn trống
                        </h3>
                        <span
                            class="bg-green-100 text-green-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium">
                            {{ $availableTables->count() ?? 0 }} bàn
                        </span>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4"
                            id="available-tables-container">
                            @forelse($availableTables as $table)
                                <a href="{{ route('admin.bills.create') }}?table_id={{ $table->id }}"
                                    class="table-card p-3 md:p-4 bg-green-50 border-2 border-green-200 hover:border-green-400 hover:bg-green-100 transition-colors rounded-lg text-center group">
                                    <i class="fas fa-table text-green-600 text-base md:text-xl mb-1 md:mb-2"></i>
                                    <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">
                                        {{ $table->table_name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                    <div class="mt-1 md:mt-2">
                                        <span class="bg-green-500 text-white px-2 py-1 text-xs rounded-full">Trống</span>
                                    </div>
                                </a>
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
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="1100">
                    <div class="p-3 md:p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-receipt text-blue-500 mr-2"></i>
                            Trạng thái Bill
                        </h3>
                        <span
                            class="bg-blue-100 text-blue-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium">
                            {{ $openBills->count() ?? 0 }} bill
                        </span>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="space-y-3 max-h-80 md:max-h-96 overflow-y-auto scroll-touch" id="bills-container">
                            @forelse($openBills as $bill)
                                <div
                                    class="bill-card border-2 border-blue-200 p-3 md:p-4 bg-blue-50 hover:bg-blue-100 transition-colors rounded-lg">
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
                                        <span
                                            class="bg-blue-500 text-white px-2 py-1 text-xs md:text-sm rounded-full ml-2 flex-shrink-0">
                                            {{ $bill->status }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                        <div>
                                            <p class="truncate">Bắt đầu:
                                                {{ \Carbon\Carbon::parse($bill->start_time)->format('H:i') }}</p>
                                            <p class="truncate">Thời gian:
                                                {{ \Carbon\Carbon::parse($bill->start_time)->diffForHumans(['parts' => 1, 'short' => true]) }}
                                            </p>
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
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="1300">
                    <div class="p-3 md:p-4 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-calendar-day text-purple-500 mr-2"></i>
                            Đặt bàn hôm nay
                        </h3>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="space-y-2 md:space-y-3" id="reservations-container">
                            @forelse($todayReservations as $reservation)
                                <a href="/admin/reservations/{{ $reservation->id }}"
                                    class="flex items-center justify-between p-2 md:p-3 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors rounded-lg text-sm">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800 text-sm truncate">
                                            {{ $reservation->customer_name }}</p>
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
                                            <form action="/admin/reservations/{{ $reservation->id }}/checkin"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-green-500 text-white px-2 py-1 hover:bg-green-600 transition-colors text-xs rounded w-full">
                                                    Check-in
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </a>
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
        // Giữ nguyên các function cần thiết khác
        function showNotification(message, type = 'info', duration = 5000) {
            const notificationArea = document.getElementById('notification-area');
            const notificationId = 'notification-' + Date.now();

            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `p-4 rounded-lg shadow-lg border-l-4 animate-bounce-in ${
                type === 'success' ? 'bg-green-50 border-green-400 text-green-800' :
                type === 'error' ? 'bg-red-50 border-red-400 text-red-800' :
                type === 'warning' ? 'bg-yellow-50 border-yellow-400 text-yellow-800' :
                'bg-blue-50 border-blue-400 text-blue-800'
            }`;

            notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-${
                        type === 'success' ? 'check-circle' :
                        type === 'error' ? 'exclamation-circle' :
                        type === 'warning' ? 'exclamation-triangle' :
                        'info-circle'
                    } mr-3"></i>
                    <span>${message}</span>
                </div>
                <button onclick="document.getElementById('${notificationId}').remove()" 
                        class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            notificationArea.appendChild(notification);

            setTimeout(() => {
                if (document.getElementById(notificationId)) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, duration);
        }

        // Chỉ giữ lại các animation cần thiết cho phần khác
        function initializeAnimations() {
            // Chỉ áp dụng animation cho các phần tử có data-delay (không bao gồm Quick Actions)
            const animatedElements = document.querySelectorAll('[data-delay]');
            animatedElements.forEach(element => {
                const delay = parseInt(element.getAttribute('data-delay'));
                element.style.animationDelay = delay + 'ms';
                element.classList.add('animate-float-in');
            });
        }

        // Real-time updates
        function startRealTimeUpdates() {
            setInterval(() => {
                fetch('{{ route('admin.pos.dashboard') }}')
                    .then(response => response.json())
                    .then(data => {
                        animateNumberChange('openBillsCount', data.open_bills);
                        animateNumberChange('occupiedTables', data.occupied_tables);
                    })
                    .catch(error => {
                        console.error('Error updating stats:', error);
                    });
            }, 30000);
        }

        // Animate number changes
        function animateNumberChange(elementId, newValue) {
            const element = document.getElementById(elementId);
            if (!element) return;

            const oldValue = parseInt(element.textContent);
            if (oldValue === newValue) return;

            const duration = 1000;
            const startTime = performance.now();

            function updateNumber(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const currentValue = Math.floor(oldValue + (newValue - oldValue) * easeOutQuart);

                element.textContent = currentValue;

                if (progress < 1) {
                    requestAnimationFrame(updateNumber);
                } else {
                    element.textContent = newValue;
                }
            }

            requestAnimationFrame(updateNumber);
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeAnimations();
            startRealTimeUpdates();

            // Show welcome notification
            setTimeout(() => {
                showNotification('Chào mừng đến với POS Dashboard!', 'success', 3000);
            }, 1000);
        });
    </script>
@endsection
