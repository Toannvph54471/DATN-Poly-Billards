<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - Poly Billiards</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        'primary-dark': '#1e3a8a',
                        secondary: '#f59e0b',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                        dark: '#1F2937'
                    },
                    borderRadius: {
                        'none': '0',
                        'sm': '0',
                        DEFAULT: '0',
                        'md': '0',
                        'lg': '0',
                        'xl': '0',
                        '2xl': '0',
                        '3xl': '0',
                        'full': '0'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav-item {
            transition: all 0.3s ease;
            margin: 4px 8px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid var(--secondary);
        }

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

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="text-gray-800">

    @auth
        <div class="flex h-screen bg-gray-100">

            <!-- Sidebar -->
            <div class="sidebar w-64 flex-shrink-0 text-white flex flex-col">
                <!-- Logo -->
                <div class="p-6 border-b border-blue-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white flex items-center justify-center">
                            <i class="fas fa-billiard-ball text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Poly Billiards</h1>
                            <p class="text-blue-200 text-xs">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    @php
                        $userRole = Auth::user()->role->slug ?? '';

                        $isAdminOrManager = in_array($userRole, ['admin', 'manager']);
                        $isStaff = in_array($userRole, ['admin', 'manager', 'employee']);
                    @endphp

                    <!-- Menu cho Admin & Manager -->
                    @if ($isAdminOrManager)
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fas fa-chart-pie w-6 mr-3"></i>
                            <span class="font-medium">Tổng quan</span>
                        </a>

                        <a href="{{ route('admin.tables.index') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.tables.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fa-solid fa-table w-6 mr-3"></i>
                            <span class="font-medium">Quản lý bàn</span>
                        </a>

                        <a href="{{ route('admin.table_rates.index') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.table_rates.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fa-solid fa-clock w-6 mr-3"></i>
                            <span class="font-medium">Giá giờ bàn</span>
                        </a>

                        <a href="{{ route('admin.combos.index') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.combos.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fas fa-th-large w-6 mr-3"></i>
                            <span class="font-medium">Quản lý Combo</span>
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fas fa-cubes w-6 mr-3"></i>
                            <span class="font-medium">Sản phẩm</span>
                        </a>

                        <a href="{{ route('admin.promotions.index') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.promotions.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fas fa-percent w-6 mr-3"></i>
                            <span class="font-medium">Khuyến mại</span>
                        </a>

                        @if ($userRole === 'admin')
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                                <i class="fas fa-users-cog w-6 mr-3"></i>
                                <span class="font-medium">Người dùng hệ thống</span>
                            </a>

                            <a href="{{ route('admin.employees.index') }}"
                                class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.employees.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                                <i class="fas fa-user-tie w-6 mr-3"></i>
                                <span class="font-medium">Nhân viên</span>
                            </a>

                            <a href="{{ route('admin.roles.index') }}"
                                class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->routeIs('admin.roles.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                                <i class="fas fa-user-shield w-6 mr-3"></i>
                                <span class="font-medium">Phân quyền</span>
                            </a>
                        @endif
                    @endif

                    <!-- Menu cho Employee (admin/manager/employee đều thấy nếu không phải customer) -->
                    @if ($isStaff && $userRole === 'employee')
                        <a href="{{ route('admin.pos.dashboard') }}"
                            class="flex items-center p-3 text-white hover:bg-white/10 {{ request()->is('employee*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                            <i class="fas fa-cash-register w-6 mr-3"></i>
                            <span class="font-medium">Bán hàng (POS)</span>
                        </a>
                    @endif

                    <!-- Đăng xuất -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-10">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center p-3 text-left text-red-200 hover:text-white hover:bg-red-600 transition">
                            <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                            <span class="font-medium">Đăng xuất</span>
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="flex justify-between items-center px-6 py-4">
                        <div class="flex-1 max-w-xl">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" placeholder="Tìm kiếm..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">
                                    {{ Str::replace('_', ' ', Auth::user()->role->name) }}</p>
                            </div>
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                    <div x-data="posDashboard()" class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Open Bills -->
                            <div class="stat-card text-white p-4 shadow-lg hover-lift animate-fade-in">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm">Bills đang mở</p>
                                        <h3 class="text-2xl font-bold mt-1" id="openBillsCount">
                                            {{ $stats['open_bills'] ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-receipt text-2xl opacity-80"></i>
                                </div>
                            </div>

                            <!-- Occupied Tables -->
                            <div class="bg-gradient-to-r from-orange-500 to-amber-600 text-white p-4 shadow-lg hover-lift animate-fade-in"
                                style="animation-delay: 0.1s">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-amber-100 text-sm">Bàn đang dùng</p>
                                        <h3 class="text-2xl font-bold mt-1" id="occupiedTables">
                                            {{ $stats['occupied_tables'] ?? 0 }}
                                        </h3>
                                    </div>
                                    <i class="fas fa-table text-2xl opacity-80"></i>
                                </div>
                            </div>

                            <!-- Available Tables -->
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-4 shadow-lg hover-lift animate-fade-in"
                                style="animation-delay: 0.2s">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm">Bàn trống</p>
                                        <h3 class="text-2xl font-bold mt-1">{{ $stats['available_tables'] ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-chair text-2xl opacity-80"></i>
                                </div>
                            </div>

                            <!-- Reservations -->
                            <div class="bg-gradient-to-r from-pink-500 to-rose-600 text-white p-4 shadow-lg hover-lift animate-fade-in"
                                style="animation-delay: 0.3s">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-pink-100 text-sm">Đặt bàn hôm nay</p>
                                        <h3 class="text-2xl font-bold mt-1">{{ $stats['pending_reservations'] ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-calendar-check text-2xl opacity-80"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Main Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left Column -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Quick Actions -->
                                <div class="bg-white shadow-lg border border-gray-200">
                                    <div class="p-4 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                                            Thao tác nhanh
                                        </h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <!-- Tạo Bill Nhanh -->
                                            <button @click="openCreateBillModal()"
                                                class="p-3 bg-blue-50 hover:bg-blue-100 transition-colors group border-2 border-blue-200"
                                                data-tooltip="Tạo bill mới nhanh">
                                                <div class="text-center">
                                                    <i
                                                        class="fas fa-plus-circle text-blue-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                    <p class="mt-1 text-sm font-medium text-gray-700">Tạo Bill</p>
                                                </div>
                                            </button>

                                            <!-- Đặt Bàn -->
                                            <a href="{{ route('admin.reservations.create') }}"
                                                class="p-3 bg-green-50 hover:bg-green-100 transition-colors group border-2 border-green-200 text-center"
                                                data-tooltip="Tạo đặt bàn mới">
                                                <i
                                                    class="fas fa-calendar-plus text-green-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                <p class="mt-1 text-sm font-medium text-gray-700">Đặt bàn</p>
                                            </a>

                                            <!-- Quản lý Bàn -->
                                            <a href="{{ route('admin.tables.simple-dashboard') }}"
                                                class="p-3 bg-purple-50 hover:bg-purple-100 transition-colors group border-2 border-purple-200 text-center"
                                                data-tooltip="Xem trạng thái tất cả bàn">
                                                <i
                                                    class="fas fa-table-cells text-purple-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                <p class="mt-1 text-sm font-medium text-gray-700">Quản lý Bàn</p>
                                            </a>

                                            <!-- Danh sách Đặt Bàn -->
                                            <a href="{{ route('admin.reservations.index') }}"
                                                class="p-3 bg-orange-50 hover:bg-orange-100 transition-colors group border-2 border-orange-200 text-center"
                                                data-tooltip="Xem tất cả đặt bàn">
                                                <i
                                                    class="fas fa-list text-orange-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                <p class="mt-1 text-sm font-medium text-gray-700">DS Đặt Bàn</p>
                                            </a>

                                            <!-- Thanh Toán -->
                                            <button onclick="openPaymentPage()"
                                                class="p-3 bg-red-50 hover:bg-red-100 transition-colors group border-2 border-red-200"
                                                data-tooltip="Xử lý thanh toán">
                                                <div class="text-center">
                                                    <i
                                                        class="fas fa-credit-card text-red-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                    <p class="mt-1 text-sm font-medium text-gray-700">Thanh Toán</p>
                                                </div>
                                            </button>

                                            <!-- Chuyển Bàn -->
                                            <button onclick="openTransferTable()"
                                                class="p-3 bg-indigo-50 hover:bg-indigo-100 transition-colors group border-2 border-indigo-200"
                                                data-tooltip="Chuyển khách sang bàn khác">
                                                <div class="text-center">
                                                    <i
                                                        class="fas fa-exchange-alt text-indigo-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                    <p class="mt-1 text-sm font-medium text-gray-700">Chuyển Bàn</p>
                                                </div>
                                            </button>

                                            <!-- Check-in Bàn -->
                                            <button onclick="openCheckinTable()"
                                                class="p-3 bg-teal-50 hover:bg-teal-100 transition-colors group border-2 border-teal-200"
                                                data-tooltip="Check-in khách vào bàn">
                                                <div class="text-center">
                                                    <i
                                                        class="fas fa-sign-in-alt text-teal-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                    <p class="mt-1 text-sm font-medium text-gray-700">Check-in</p>
                                                </div>
                                            </button>

                                            <!-- Check-out Bàn -->
                                            <button onclick="openCheckoutTable()"
                                                class="p-3 bg-amber-50 hover:bg-amber-100 transition-colors group border-2 border-amber-200"
                                                data-tooltip="Check-out khách khỏi bàn">
                                                <div class="text-center">
                                                    <i
                                                        class="fas fa-sign-out-alt text-amber-600 text-xl group-hover:scale-110 transition-transform"></i>
                                                    <p class="mt-1 text-sm font-medium text-gray-700">Check-out</p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bàn trống (trước đây là Bàn đang sử dụng) -->
                                <div class="bg-white shadow-lg border border-gray-200">
                                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <i class="fas fa-chair text-green-500 mr-2"></i>
                                            Bàn trống
                                        </h3>
                                        <span class="bg-green-100 text-green-800 text-sm px-3 py-1 font-medium">
                                            {{ $availableTables->count() ?? 0 }} bàn
                                        </span>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @forelse($availableTables as $table)
                                                <button onclick="selectTable({{ $table->id }})"
                                                    class="p-4 bg-green-50 border-2 border-green-200 hover:border-green-400 hover:bg-green-100 transition-colors text-center group"
                                                    data-tooltip="Click để tạo bill cho bàn {{ $table->table_name }}">
                                                    <i
                                                        class="fas fa-table text-green-600 text-xl group-hover:scale-110 transition-transform mb-2"></i>
                                                    <p class="font-semibold text-gray-800 text-sm">
                                                        {{ $table->table_name }}</p>
                                                    <p class="text-xs text-gray-600 mt-1">{{ $table->capacity }} người</p>
                                                    <div class="mt-2">
                                                        <span
                                                            class="bg-green-500 text-white px-2 py-1 text-xs">Trống</span>
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="col-span-4 text-center py-8 text-gray-500">
                                                    <i class="fas fa-table text-4xl mb-3 opacity-50"></i>
                                                    <p>Không có bàn trống</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Trạng thái Bill (trước đây là Bàn trống) -->
                                <div class="bg-white shadow-lg border border-gray-200">
                                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <i class="fas fa-receipt text-blue-500 mr-2"></i>
                                            Trạng thái Bill
                                        </h3>
                                        <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 font-medium">
                                            {{ $openBills->count() ?? 0 }} bill
                                        </span>
                                    </div>
                                    <div class="p-4">
                                        <div class="space-y-4 max-h-96 overflow-y-auto">
                                            @forelse($openBills as $bill)
                                                <div
                                                    class="border-2 border-blue-200 p-4 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div>
                                                            <h4 class="font-semibold text-gray-800 flex items-center">
                                                                <i class="fas fa-receipt text-blue-600 mr-2"></i>
                                                                Bill #{{ $bill->bill_number }}
                                                            </h4>
                                                            <p class="text-sm text-gray-600">
                                                                Bàn:
                                                                <strong>{{ $bill->table->table_name ?? 'N/A' }}</strong>
                                                            </p>
                                                            <p class="text-sm text-gray-600">
                                                                Khách:
                                                                <strong>{{ $bill->user->name ?? 'Khách vãng lai' }}</strong>
                                                            </p>
                                                        </div>
                                                        <span class="bg-blue-500 text-white px-2 py-1 text-sm">
                                                            {{ $bill->status }}
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                                                        <div>
                                                            <p>Bắt đầu:
                                                                {{ \Carbon\Carbon::parse($bill->start_time)->format('H:i') }}
                                                            </p>
                                                            <p>Thời gian:
                                                                {{ \Carbon\Carbon::parse($bill->start_time)->diffForHumans(['parts' => 2, 'short' => true]) }}
                                                            </p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p>Tổng tiền:</p>
                                                            <p class="font-semibold text-green-600">
                                                                {{ number_format($bill->total_amount ?? 0, 0, ',', '.') }}₫
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('admin.tables.detail', $bill->table_id) }}"
                                                            class="flex-1 px-3 py-1 bg-blue-500 text-white hover:bg-blue-600 text-sm transition-colors text-center">
                                                            <i class="fas fa-eye mr-1"></i>Xem
                                                        </a>
                                                        <a href="{{ route('admin.payments.payment-page', $bill) }}"
                                                            class="flex-1 px-3 py-1 bg-green-500 text-white hover:bg-green-600 text-sm transition-colors text-center">
                                                            <i class="fas fa-credit-card mr-1"></i>Thanh toán
                                                        </a>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-8 text-gray-500">
                                                    <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
                                                    <p>Không có bill nào đang mở</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Today's Reservations -->
                                <div class="bg-white shadow-lg border border-gray-200">
                                    <div class="p-4 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <i class="fas fa-calendar-day text-purple-500 mr-2"></i>
                                            Đặt bàn hôm nay
                                        </h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="space-y-3">
                                            @forelse($todayReservations as $reservation)
                                                <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors cursor-pointer"
                                                    onclick="viewReservation({{ $reservation->id }})"
                                                    data-tooltip="Click để xem chi tiết">
                                                    <div>
                                                        <p class="font-medium text-gray-800 text-sm">
                                                            {{ $reservation->customer_name }}</p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                                            -
                                                            Bàn {{ $reservation->table->table_name }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="bg-purple-500 text-white px-2 py-1 text-xs">
                                                            {{ $reservation->status }}
                                                        </span>
                                                        @if ($reservation->status === 'confirmed')
                                                            <button
                                                                onclick="checkinReservation({{ $reservation->id }}, event)"
                                                                class="mt-1 bg-green-500 text-white px-2 py-1 hover:bg-green-600 transition-colors text-xs">
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
                </main>
            </div>
        </div>

        <!-- Create Bill Modal -->
        <div x-show="showCreateBillModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white shadow-2xl w-full max-w-md mx-4">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Tạo Bill Mới</h3>
                </div>
                <div class="p-6">
                    <form id="createBillForm" action="{{ route('admin.bills.create') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn bàn *</label>
                                <select name="table_id" required
                                    class="w-full px-3 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Chọn bàn --</option>
                                    @foreach ($availableTables as $table)
                                        <option value="{{ $table->id }}">{{ $table->table_name }}
                                            ({{ $table->capacity }} người)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại khách
                                    hàng</label>
                                <input type="text" name="customer_phone"
                                    class="w-full px-3 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Nhập số điện thoại (tùy chọn)">
                            </div>
                        </div>
                        <div class="mt-6 flex space-x-3">
                            <button type="button" @click="showCreateBillModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                                Hủy
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 transition-colors">
                                Tạo Bill
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Guest Layout -->
        <div class="min-h-screen bg-gray-50">
            <!-- Guest content here -->
        </div>
    @endauth

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js"></script>
    <script>
        function posDashboard() {
            return {
                showCreateBillModal: false,

                openCreateBillModal() {
                    this.showCreateBillModal = true;
                },

                init() {
                    console.log('POS Dashboard initialized');
                    // Auto-refresh stats every 30 seconds
                    setInterval(this.updateStats, 30000);
                },

                updateStats() {
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
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(amount);
                }
            }
        }

        function selectTable(tableId) {
            // Auto-fill the table selection in modal
            if (typeof posDashboard !== 'undefined') {
                posDashboard().openCreateBillModal();
                // Auto-select the table
                const select = document.querySelector('select[name="table_id"]');
                if (select) {
                    select.value = tableId;
                }
            }
        }

        // Các hàm xử lý action
        function openPaymentPage() {
            alert('Chức năng Thanh toán - Cần chọn bill cụ thể từ danh sách bàn đang sử dụng');
        }

        function openTransferTable() {
            alert('Chức năng Chuyển Bàn - Cần chọn bill cụ thể từ danh sách bàn đang sử dụng');
        }

        function openCheckinTable() {
            // Redirect đến trang quản lý bàn để check-in
            window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
        }

        function openCheckoutTable() {
            // Redirect đến trang quản lý bàn để check-out
            window.location.href = '{{ route('admin.tables.simple-dashboard') }}';
        }

        function openPaymentForBill(billId) {
            window.location.href = `/admin/payments/${billId}/payment`;
        }

        function openTransferForBill(billId) {
            window.location.href = `/admin/bills/${billId}/transfer`;
        }

        function viewReservation(reservationId) {
            window.location.href = `/admin/reservations/${reservationId}`;
        }

        function checkinReservation(reservationId, event) {
            event.stopPropagation();
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
                            alert('Check-in thành công!');
                            location.reload();
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi check-in');
                    });
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects
            const cards = document.querySelectorAll('.hover-lift');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(element => {
                element.addEventListener('mouseenter', function(e) {
                    const tooltip = document.createElement('div');
                    tooltip.className =
                        'fixed z-50 px-2 py-1 text-sm text-white bg-gray-900 shadow-lg';
                    tooltip.textContent = e.target.dataset.tooltip;
                    document.body.appendChild(tooltip);

                    const rect = e.target.getBoundingClientRect();
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                    tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) +
                        'px';

                    e.target._tooltip = tooltip;
                });

                element.addEventListener('mouseleave', function(e) {
                    if (e.target._tooltip) {
                        e.target._tooltip.remove();
                    }
                });
            });
        });

        // Cập nhật hàm selectTable để hoạt động với phần Bàn trống mới
        function selectTable(tableId) {
            // Auto-fill the table selection in modal
            if (typeof posDashboard !== 'undefined') {
                posDashboard().openCreateBillModal();
                // Auto-select the table
                const select = document.querySelector('select[name="table_id"]');
                if (select) {
                    select.value = tableId;
                }
            }
        }

        // Hàm xem chi tiết bill
        function viewBillDetail(billId) {
            window.location.href = `/admin/bills/${billId}`;
        }

        // Hàm thanh toán bill
        function payBill(billId) {
            window.location.href = `/admin/payments/${billId}/payment`;
        }
    </script>
</body>

</html>
