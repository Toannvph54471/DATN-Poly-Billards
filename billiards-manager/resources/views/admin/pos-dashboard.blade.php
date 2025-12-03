@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Poly Billiards')

@section('styles')
    <style>
        .stat-card {
            min-height: 70px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .quick-action-btn {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .quick-action-btn:hover::after {
            width: 100px;
            height: 100px;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px) scale(1.05);
        }

        .table-card {
            min-height: 80px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .table-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .table-card:hover::before {
            left: 100%;
        }

        .table-card:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .bill-card {
            min-height: 100px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .bill-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Floating animation for new elements */
        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-float-in {
            animation: floatIn 0.6s ease-out forwards;
        }

        /* Pulse animation for important elements */
        @keyframes gentlePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .animate-gentle-pulse {
            animation: gentlePulse 3s ease-in-out infinite;
        }

        /* Shimmer effect for loading */
        @keyframes shimmer {
            0% {
                background-position: -468px 0;
            }

            100% {
                background-position: 468px 0;
            }
        }

        .shimmer {
            background: linear-gradient(to right, #f6f7f8 8%, #edeef1 18%, #f6f7f8 33%);
            background-size: 800px 104px;
            animation: shimmer 1.5s infinite linear;
        }

        /* Glow effect for notifications */
        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
            }
        }

        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }

        /* Bounce animation for alerts */
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-bounce-in {
            animation: bounceIn 0.6s ease-out;
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

        /* Progress bar animation */
        @keyframes progressBar {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        .progress-bar {
            animation: progressBar 2s ease-in-out infinite;
        }

        /* Typewriter effect */
        @keyframes typewriter {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        .typewriter {
            overflow: hidden;
            border-right: 2px solid;
            white-space: nowrap;
            animation: typewriter 3s steps(40) 1s 1 normal both;
        }

        /* Ripple effect styles */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.7);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
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

            <!-- Reservations -->
            <div class="stat-card bg-gradient-to-r from-pink-500 to-rose-600 text-white p-3 md:p-4 rounded-lg shadow-lg animate-float-in"
                data-delay="300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-xs md:text-sm">Đặt bàn hôm nay</p>
                        <h3 class="text-lg md:text-2xl font-bold mt-1">{{ $stats['pending_reservations'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-calendar-check text-lg md:text-2xl opacity-80"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-pink-400 progress-bar"></div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 main-grid">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-4 md:space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="400">
                    <div class="p-3 md:p-4 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-bolt text-yellow-500 mr-2 animate-gentle-pulse"></i>
                            Thao tác nhanh
                        </h3>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="grid grid-cols-3 md:grid-cols-4 gap-2 md:gap-3">
                            @foreach ([
                                ['route' => 'admin.bills.index', 'color' => 'blue', 'icon' => 'plus-circle', 'text' => 'Hóa đơn'], 
                                ['route' => 'admin.reservations.create', 'color' => 'green', 'icon' => 'calendar-plus', 'text' => 'Đặt bàn'], 
                                ['route' => 'admin.tables.simple-dashboard', 'color' => 'purple', 'icon' => 'table-cells', 'text' => 'Quản lý Bàn'], 
                                ['route' => 'admin.reservations.index', 'color' => 'orange', 'icon' => 'list', 'text' => 'DS Đặt Bàn'], 
                                ['function' => 'openPaymentPage', 'color' => 'red', 'icon' => 'credit-card', 'text' => 'Thanh Toán'], 
                                ['function' => 'openTransferTable', 'color' => 'indigo', 'icon' => 'exchange-alt', 'text' => 'Chuyển Bàn'], 
                                ['function' => 'openCheckinTable', 'color' => 'teal', 'icon' => 'sign-in-alt', 'text' => 'Check-in'], 
                                ['function' => 'openCheckoutTable', 'color' => 'amber', 'icon' => 'sign-out-alt', 'text' => 'Check-out'],
                                ['route' => 'attendance.my-qr', 'color' => 'pink', 'icon' => 'qrcode', 'text' => 'Mã QR Check-in']
                            ] as $index => $action)
                                @php
                                    $delay = 500 + $index * 50;
                                @endphp
                                @if (isset($action['route']))
                                    <a href="{{ route($action['route']) }}"
                                        class="quick-action-btn p-2 md:p-3 bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors group border-2 border-{{ $action['color'] }}-200 rounded-lg text-center animate-float-in"
                                        data-delay="{{ $delay }}">
                                        <div>
                                            <i
                                                class="fas fa-{{ $action['icon'] }} text-{{ $action['color'] }}-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                            <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">
                                                {{ $action['text'] }}</p>
                                        </div>
                                    </a>
                                @else
                                    <button onclick="{{ $action['function'] }}()"
                                        class="quick-action-btn p-2 md:p-3 bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors group border-2 border-{{ $action['color'] }}-200 rounded-lg text-center animate-float-in"
                                        data-delay="{{ $delay }}">
                                        <div>
                                            <i
                                                class="fas fa-{{ $action['icon'] }} text-{{ $action['color'] }}-600 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                                            <p class="mt-1 text-xs md:text-sm font-medium text-gray-700">
                                                {{ $action['text'] }}</p>
                                        </div>
                                    </button>
                                @endif
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
                            class="bg-green-100 text-green-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium animate-gentle-pulse">
                            {{ $availableTables->count() ?? 0 }} bàn
                        </span>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4"
                            id="available-tables-container">
                            @forelse($availableTables as $index => $table)
                                <button onclick="selectTableWithAnimation({{ $table->id }}, this)"
                                    class="table-card p-3 md:p-4 bg-green-50 border-2 border-green-200 hover:border-green-400 hover:bg-green-100 transition-colors rounded-lg text-center group animate-float-in"
                                    data-delay="{{ 1000 + $index * 50 }}" data-table-id="{{ $table->id }}">
                                    <i
                                        class="fas fa-table text-green-600 text-base md:text-xl group-hover:scale-110 transition-transform mb-1 md:mb-2"></i>
                                    <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">
                                        {{ $table->table_name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                    <div class="mt-1 md:mt-2">
                                        <span
                                            class="bg-green-500 text-white px-2 py-1 text-xs rounded-full animate-glow">Trống</span>
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
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 animate-float-in" data-delay="1100">
                    <div class="p-3 md:p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">
                            <i class="fas fa-receipt text-blue-500 mr-2"></i>
                            Trạng thái Bill
                        </h3>
                        <span
                            class="bg-blue-100 text-blue-800 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium animate-gentle-pulse">
                            {{ $openBills->count() ?? 0 }} bill
                        </span>
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="space-y-3 max-h-80 md:max-h-96 overflow-y-auto scroll-touch" id="bills-container">
                            @forelse($openBills as $index => $bill)
                                <div class="bill-card border-2 border-blue-200 p-3 md:p-4 bg-blue-50 hover:bg-blue-100 transition-colors rounded-lg animate-float-in"
                                    data-delay="{{ 1200 + $index * 100 }}" data-bill-id="{{ $bill->id }}">
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
                                            class="bg-blue-500 text-white px-2 py-1 text-xs md:text-sm rounded-full ml-2 flex-shrink-0 animate-glow">
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
                                            class="flex-1 px-2 md:px-3 py-1 bg-blue-500 text-white hover:bg-blue-600 text-xs transition-colors rounded text-center action-btn">
                                            <i class="fas fa-eye mr-1"></i>Xem
                                        </a>
                                        <a href="{{ route('admin.payments.payment-page', $bill) }}"
                                            class="flex-1 px-2 md:px-3 py-1 bg-green-500 text-white hover:bg-green-600 text-xs transition-colors rounded text-center action-btn">
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
                            @forelse($todayReservations as $index => $reservation)
                                <div class="flex items-center justify-between p-2 md:p-3 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors rounded-lg cursor-pointer text-sm animate-float-in"
                                    data-delay="{{ 1400 + $index * 100 }}"
                                    onclick="viewReservationWithAnimation({{ $reservation->id }}, this)">
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
                                            <button
                                                onclick="checkinReservationWithAnimation({{ $reservation->id }}, event)"
                                                class="bg-green-500 text-white px-2 py-1 hover:bg-green-600 transition-colors text-xs rounded w-full action-btn">
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
        // Enhanced notification system
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

        // Enhanced animation system
        function initializeAnimations() {
            // Stagger animations for elements with data-delay
            const animatedElements = document.querySelectorAll('[data-delay]');
            animatedElements.forEach(element => {
                const delay = parseInt(element.getAttribute('data-delay'));
                element.style.animationDelay = delay + 'ms';
                element.classList.add('animate-float-in');
            });

            // Add hover effects
            const interactiveElements = document.querySelectorAll('.stat-card, .quick-action-btn, .table-card, .bill-card');
            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });

                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click ripple effect
            document.addEventListener('click', function(e) {
                if (e.target.closest('.action-btn') || e.target.closest('.quick-action-btn')) {
                    createRipple(e);
                }
            });
        }

        // Ripple effect for clicks
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = diameter + 'px';
            circle.style.left = (event.clientX - button.getBoundingClientRect().left - radius) + 'px';
            circle.style.top = (event.clientY - button.getBoundingClientRect().top - radius) + 'px';
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);

            setTimeout(() => circle.remove(), 600);
        }

        // Enhanced table selection with animation
        function selectTableWithAnimation(tableId, element) {
            // Add selection animation
            element.classList.add('animate-bounce-in');
            element.style.transform = 'scale(0.95)';

            // Show loading state
            const originalContent = element.innerHTML;
            element.innerHTML = `
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
            </div>
        `;

            // Simulate loading and redirect
            setTimeout(() => {
                window.location.href = '{{ route('admin.bills.create') }}?table_id=' + tableId;
            }, 800);
        }

        // Enhanced reservation viewing
        function viewReservationWithAnimation(reservationId, element) {
            element.classList.add('animate-bounce-in');
            setTimeout(() => {
                window.location.href = `/admin/reservations/${reservationId}`;
            }, 300);
        }

        // Enhanced functions with animations
        function openPaymentPage() {
            showNotification('Chuyển hướng đến trang thanh toán...', 'info');
            setTimeout(() => {
                window.location.href = '{{ route('admin.bills.index') }}';
            }, 1000);
        }

        function openTransferTable() {
            showNotification('Mở tính năng chuyển bàn...', 'info');
            // Add your transfer table logic here
        }

        function openCheckinTable() {
            showNotification('Đang chuyển đến quản lý bàn...', 'info');
            setTimeout(() => {
                window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
            }, 800);
        }

        function openCheckoutTable() {
            showNotification('Đang chuyển đến quản lý bàn...', 'info');
            setTimeout(() => {
                window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
            }, 800);
        }

        function checkinReservationWithAnimation(reservationId, event) {
            event.stopPropagation();

            const button = event.target;
            const originalText = button.innerHTML;

            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang xử lý...';
            button.disabled = true;

            // Using SweetAlert for confirmation
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Xác nhận check-in?',
                    text: 'Bạn có chắc muốn check-in cho đặt bàn này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Check-in',
                    cancelButtonText: 'Hủy',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(`/admin/reservations/${reservationId}/checkin`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message || 'Có lỗi xảy ra');
                                }
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        showNotification('Check-in thành công!', 'success');
                        // Reload the page after a delay to show the animation
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Reset button state
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                });
            } else {
                // Fallback if SweetAlert is not available
                if (confirm('Xác nhận check-in cho đặt bàn này?')) {
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
                                showNotification('Check-in thành công!', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showNotification(data.message || 'Có lỗi xảy ra', 'error');
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            showNotification('Có lỗi xảy ra', 'error');
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                } else {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }
        }

        // Real-time updates with animations
        function startRealTimeUpdates() {
            // Update stats every 30 seconds with animation
            setInterval(() => {
                fetch('{{ route('admin.pos.dashboard') }}')
                    .then(response => response.json())
                    .then(data => {
                        // Animate number changes
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

            // Add visual feedback
            if (newValue > oldValue) {
                showNotification(`Cập nhật: ${newValue - oldValue} bill mới`, 'success', 2000);
            }
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeAnimations();
            startRealTimeUpdates();

            // Show welcome notification
            setTimeout(() => {
                showNotification('Chào mừng đến với POS Dashboard!', 'success', 3000);
            }, 1000);
        });

        // Enhanced touch interactions for mobile
        document.addEventListener('touchstart', function() {}, {
            passive: true
        });
    </script>
@endsection
