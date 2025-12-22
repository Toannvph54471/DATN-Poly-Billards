{{-- resources/views/admin/pos-dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Quản lý bàn')

@section('styles')
    <style>
        .animate-float-in {
            animation: floatIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes floatIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glow-card {
            position: relative;
            overflow: hidden;
        }

        .glow-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: 0.5s;
        }

        .glow-card:hover::before {
            left: 100%;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .table-occupied {
            border-left: 4px solid #ef4444;
        }

        .table-available {
            border-left: 4px solid #10b981;
        }

        .table-reserved {
            border-left: 4px solid #f59e0b;
        }

        .table-maintenance {
            border-left: 4px solid #6b7280;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .rate-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            margin-top: 4px;
        }

        .price-badge {
            font-size: 10px;
            background: rgba(34, 197, 94, 0.2);
            color: #10b981;
            padding: 1px 4px;
            border-radius: 4px;
            margin-top: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="min-h-screen bg-gray-900 text-gray-100">
        <!-- Header -->
        <div class="bg-gray-800 border-b border-gray-700">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i>
                            <span class="gradient-text">POS Dashboard</span>
                            <span class="ml-4 text-sm font-normal bg-gray-700 px-3 py-1 rounded-full">
                                <i class="far fa-clock mr-1"></i>
                                {{ now()->format('H:i | d/m/Y') }}
                            </span>
                        </h1>
                        <p class="text-gray-400 text-sm mt-1">Quản lý bàn và hóa đơn thời gian thực</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center space-x-4">
                        <div class="flex items-center text-sm">
                            <div class="bg-gray-700 px-3 py-2 rounded-lg">
                                <i class="fas fa-user-shield text-blue-400 mr-2"></i>
                                {{ auth()->user()->name }}
                            </div>
                        </div>
                        <button id="refresh-stats" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-lg transition">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Total Tables -->
                <div class="glow-card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-xl p-5 shadow-xl animate-float-in"
                    data-delay="0">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Tổng số bàn</p>
                            <h3 class="text-3xl font-bold text-white" id="totalTables">{{ $totalTables ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-table text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="text-sm text-gray-400">
                            @php
                                $uniqueRates = $tables->pluck('table_rate_id')->filter()->unique()->count();
                            @endphp
                            {{ $uniqueRates }} loại bàn
                        </div>
                    </div>
                </div>

                <!-- Occupied Tables -->
                <div class="glow-card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-xl p-5 shadow-xl animate-float-in"
                    data-delay="100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Bàn đang dùng</p>
                            <h3 class="text-3xl font-bold text-white" id="occupiedTables">{{ $occupiedCount ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-red-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-400">
                                @php
                                    $occupancyRate =
                                        $totalTables > 0 ? round(($occupiedCount / $totalTables) * 100, 1) : 0;
                                @endphp
                                Tỷ lệ: {{ $occupancyRate }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Tables -->
                <div class="glow-card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-xl p-5 shadow-xl animate-float-in"
                    data-delay="200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Bàn trống</p>
                            <h3 class="text-3xl font-bold text-white" id="availableTables">{{ $availableCount ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-chair text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="text-sm text-gray-400">
                            Sẵn sàng phục vụ
                        </div>
                    </div>
                </div>

                <!-- Reserved Tables -->
                <div class="glow-card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-xl p-5 shadow-xl animate-float-in"
                    data-delay="300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Bàn đã đặt</p>
                            <h3 class="text-3xl font-bold text-white">{{ $reservedCount ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="text-sm text-gray-400">
                            Đã đặt trước
                        </div>
                    </div>
                </div>

                <!-- Open Bills -->
                <div class="glow-card bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-xl p-5 shadow-xl animate-float-in"
                    data-delay="400">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Hóa đơn mở</p>
                            <h3 class="text-3xl font-bold text-white">{{ $openBills->count() ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-receipt text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="text-sm text-gray-400">
                            Chờ thanh toán
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Available & Occupied Tables -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Available Tables -->
                    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden animate-float-in"
                        data-delay="500">
                        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between bg-gray-800/50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-chair text-green-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Bàn trống</h3>
                                    <p class="text-sm text-gray-400">{{ $availableCount ?? 0 }} bàn sẵn sàng</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.tables.simple-dashboard') }}"
                                class="text-sm font-medium text-blue-400 hover:text-blue-300 transition flex items-center">
                                Quản lý bàn
                                <i class="fas fa-external-link-alt ml-2"></i>
                            </a>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @forelse($availableTables as $table)
                                    <div class="table-available bg-gray-700/50 hover:bg-gray-700 border border-gray-600 rounded-lg p-4 transition cursor-pointer group"
                                        onclick="window.location='{{ route('admin.tables.detail', $table->id) }}'"
                                        data-table-id="{{ $table->id }}"
                                        data-table-rate-id="{{ $table->table_rate_id ?? '' }}">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-12 h-12 bg-green-900/30 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition">
                                                <i class="fas fa-table text-green-400 text-lg"></i>
                                            </div>
                                            <div class="text-center">
                                                <h4 class="font-bold text-white text-sm mb-1">{{ $table->table_name }}</h4>
                                                <div class="space-y-1">
                                                    <p class="text-xs text-gray-400">
                                                        <i class="fas fa-users mr-1"></i>
                                                        {{ $table->capacity }} người
                                                    </p>
                                                    <!-- Hiển thị thông tin loại bàn -->
                                                    @if ($table->table_rate_id && isset($table->rate_name))
                                                        @php
                                                            $rateColors = [
                                                                1 => 'bg-blue-900/30 text-blue-400 border-blue-700',
                                                                2 => 'bg-purple-900/30 text-purple-400 border-purple-700',
                                                                3 => 'bg-amber-900/30 text-amber-400 border-amber-700',
                                                                4 => 'bg-emerald-900/30 text-emerald-400 border-emerald-700',
                                                                5 => 'bg-rose-900/30 text-rose-400 border-rose-700',
                                                                6 => 'bg-cyan-900/30 text-cyan-400 border-cyan-700',
                                                            ];
                                                            $colorClass =
                                                                $rateColors[
                                                                    $table->table_rate_id % count($rateColors)
                                                                ] ?? 'bg-gray-700 text-gray-400 border-gray-600';
                                                        @endphp
                                                        <div class="rate-badge {{ $colorClass }} border">
                                                            <i class="fas fa-tag mr-1 text-xs"></i>
                                                            {{ $table->rate_name }}
                                                        </div>
                                                        @if (isset($table->hourly_rate) && $table->hourly_rate > 0)
                                                            <div class="price-badge">
                                                                {{ number_format($table->hourly_rate) }}đ/giờ
                                                            </div>
                                                        @endif
                                                    @elseif($table->table_rate_id)
                                                        <div
                                                            class="rate-badge bg-gray-700 text-gray-400 border border-gray-600">
                                                            <i class="fas fa-hashtag mr-1 text-xs"></i>
                                                            Loại {{ $table->table_rate_id }}
                                                        </div>
                                                    @else
                                                        <div
                                                            class="rate-badge bg-gray-700 text-gray-400 border border-gray-600">
                                                            <i class="fas fa-question-circle mr-1 text-xs"></i>
                                                            Chưa phân loại
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 w-full">
                                                <div
                                                    class="bg-green-900/30 text-green-400 text-xs font-medium py-1.5 rounded text-center">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Sẵn sàng
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-10 text-center">
                                        <div
                                            class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-chair text-gray-500 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500">Không có bàn trống</p>
                                        <p class="text-sm text-gray-600 mt-1">Tất cả bàn đang được sử dụng</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Occupied Tables -->
                    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden animate-float-in"
                        data-delay="700">
                        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between bg-gray-800/50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-red-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Bàn đang dùng</h3>
                                    <p class="text-sm text-gray-400">{{ $occupiedCount ?? 0 }} bàn đang phục vụ</p>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-400">
                                Tổng doanh thu: <span
                                    class="text-green-400">{{ number_format($occupiedTables->sum('current_bill_total') ?? 0) }}đ</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($occupiedTables as $table)
                                    <div
                                        class="table-occupied bg-gray-700/50 border border-gray-600 rounded-lg p-4 hover:bg-gray-700 transition">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-red-900/30 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-table text-red-400"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-white">{{ $table->table_name }}</h4>
                                                    <p class="text-xs text-gray-400">{{ $table->capacity }} người</p>
                                                    <!-- Hiển thị loại bàn -->
                                                    @if ($table->table_rate_id && isset($table->rate_name))
                                                        <div class="mt-1">
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded bg-gray-800 text-blue-300">
                                                                <i class="fas fa-tag mr-1"></i>
                                                                {{ $table->rate_name }}
                                                                @if (isset($table->hourly_rate) && $table->hourly_rate > 0)
                                                                    <span class="text-green-300 ml-1">
                                                                        ({{ number_format($table->hourly_rate) }}đ/h)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @elseif($table->table_rate_id)
                                                        <div class="mt-1">
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded bg-gray-800 text-gray-400">
                                                                <i class="fas fa-hashtag mr-1"></i>
                                                                Loại {{ $table->table_rate_id }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <span
                                                class="bg-red-900/50 text-red-300 px-3 py-1 text-xs rounded-full font-medium">
                                                <i class="fas fa-clock mr-1"></i>
                                                Đang dùng
                                            </span>
                                        </div>

                                        @if ($table->current_bill)
                                            <div class="mb-4 space-y-2">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-gray-400">Hóa đơn:</span>
                                                    <a href="{{ route('admin.bills.show', $table->current_bill) }}"
                                                        class="text-blue-400 hover:text-blue-300 font-medium">
                                                        #{{ $table->current_bill }}
                                                    </a>
                                                </div>
                                                @if ($table->start_time)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-400">Thời gian:</span>
                                                        <span
                                                            class="text-white">{{ \Carbon\Carbon::parse($table->start_time)->diffForHumans() }}</span>
                                                    </div>
                                                @endif
                                                @isset($table->customer_name)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-400">Khách hàng:</span>
                                                        <span class="text-white">{{ $table->customer_name }}</span>
                                                    </div>
                                                @endisset
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ route('admin.tables.detail', $table->id) }}"
                                                class="py-2 bg-gray-600 hover:bg-gray-500 text-white text-center text-xs rounded transition">
                                                <i class="fas fa-eye mr-1"></i> Chi tiết
                                            </a>
                                            @if ($table->current_bill)
                                                <a href="{{ route('admin.bills.show', $table->current_bill) }}"
                                                    class="py-2 bg-blue-600 hover:bg-blue-500 text-white text-center text-xs rounded transition">
                                                    <i class="fas fa-receipt mr-1"></i> Hóa đơn
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-10 text-center">
                                        <div
                                            class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-users text-gray-500 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500">Không có bàn đang dùng</p>
                                        <p class="text-sm text-gray-600 mt-1">Tất cả bàn đều trống</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - All Tables & Open Bills -->
                <div class="space-y-6">
                    <!-- All Tables List -->
                    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden animate-float-in"
                        data-delay="900">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-list text-blue-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-white">Tất cả bàn</h3>
                                        <p class="text-sm text-gray-400">{{ $totalTables ?? 0 }} bàn</p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-400">
                                    <span class="text-green-400">{{ $availableCount ?? 0 }}</span>/<span
                                        class="text-white">{{ $totalTables ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar">
                                @forelse($tables as $table)
                                    <div class="bg-gray-700/30 hover:bg-gray-700 rounded-lg p-3 transition cursor-pointer"
                                        onclick="window.location='{{ route('admin.tables.detail', $table->id) }}'">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 rounded-full flex items-center justify-center mr-3 
                                                @if ($table->status == 'available') bg-green-900/30 text-green-400
                                                @elseif($table->status == 'occupied') bg-red-900/30 text-red-400
                                                @elseif($table->status == 'reserved') bg-yellow-900/30 text-yellow-400
                                                @else bg-gray-700 text-gray-400 @endif">
                                                    <i class="fas fa-table text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-white text-sm">{{ $table->table_name }}
                                                        </p>
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-users mr-1"></i>{{ $table->capacity }}
                                                        </span>
                                                    </div>
                                                    <!-- Hiển thị loại bàn -->
                                                    <div class="mt-1">
                                                        @if ($table->table_rate_id && isset($table->rate_name))
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded bg-gray-800 text-blue-300 inline-flex items-center">
                                                                <i class="fas fa-tag mr-1 text-xs"></i>
                                                                {{ $table->rate_name }}
                                                                @if (isset($table->hourly_rate) && $table->hourly_rate > 0)
                                                                    <span class="text-green-300 ml-1 text-xs">
                                                                        ({{ number_format($table->hourly_rate) }}đ/h)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        @elseif($table->table_rate_id)
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded bg-gray-800 text-gray-400 inline-flex items-center">
                                                                <i class="fas fa-hashtag mr-1 text-xs"></i>
                                                                Loại {{ $table->table_rate_id }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded bg-gray-800 text-gray-500">
                                                                <i class="fas fa-question-circle mr-1 text-xs"></i>
                                                                Chưa phân loại
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-2">
                                                @if ($table->status == 'available')
                                                    <span
                                                        class="bg-green-900/30 text-green-400 px-2 py-1 text-xs rounded-full">Trống</span>
                                                @elseif($table->status == 'occupied')
                                                    <span
                                                        class="bg-red-900/30 text-red-400 px-2 py-1 text-xs rounded-full">Đang
                                                        dùng</span>
                                                @elseif($table->status == 'reserved')
                                                    <span
                                                        class="bg-yellow-900/30 text-yellow-400 px-2 py-1 text-xs rounded-full">Đã
                                                        đặt</span>
                                                @else
                                                    <span
                                                        class="bg-gray-700 text-gray-400 px-2 py-1 text-xs rounded-full">Bảo
                                                        trì</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <i class="fas fa-table text-4xl text-gray-600 mb-3"></i>
                                        <p class="text-gray-500">Không có dữ liệu bàn</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Open Bills -->
                    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden animate-float-in"
                        data-delay="1100">
                        <div class="px-6 py-4 border-b border-gray-700 bg-gray-800/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-receipt text-purple-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-white">Hóa đơn đang mở</h3>
                                        <p class="text-sm text-gray-400">{{ $openBills->count() }} hóa đơn</p>
                                    </div>
                                </div>
                                <span class="bg-purple-900/30 text-purple-400 px-3 py-1 rounded-full text-sm font-medium">
                                    Chờ thanh toán
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                @forelse($openBills as $bill)
                                    <a href="{{ route('admin.bills.show', $bill->id) }}"
                                        class="block bg-gray-700/30 hover:bg-gray-700 border border-gray-600 rounded-lg p-4 transition group">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <div
                                                        class="w-8 h-8 bg-purple-900/30 rounded-full flex items-center justify-center mr-3">
                                                        <i class="fas fa-receipt text-purple-400 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-white">#{{ $bill->bill_number }}</p>
                                                        <p class="text-xs text-gray-400">
                                                            {{ $bill->table_name }}
                                                            @if (isset($bill->rate_name))
                                                                <span class="ml-2 text-blue-300">
                                                                    ({{ $bill->rate_name }})
                                                                </span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="space-y-1 pl-11">
                                                    <div class="flex items-center text-sm">
                                                        <span class="text-gray-400 w-20">Bắt đầu:</span>
                                                        <span
                                                            class="text-white">{{ \Carbon\Carbon::parse($bill->start_time)->format('H:i') }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm">
                                                        <span class="text-gray-400 w-20">Khách:</span>
                                                        <span
                                                            class="text-white">{{ $bill->customer_name ?? 'Khách vãng lai' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm">
                                                        <span class="text-gray-400 w-20">Tổng tiền:</span>
                                                        <span
                                                            class="text-green-400 font-bold">{{ number_format($bill->total_amount ?? 0) }}đ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <span class="text-xs text-gray-400 group-hover:text-blue-400 transition">
                                                Xem chi tiết <i class="fas fa-arrow-right ml-1"></i>
                                            </span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-receipt text-gray-500 text-xl"></i>
                                        </div>
                                        <p class="text-gray-500">Không có hóa đơn đang mở</p>
                                        <p class="text-sm text-gray-600 mt-1">Tất cả hóa đơn đã thanh toán</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 border-t border-gray-800 py-4">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                        <span>Phiên bản 1.0.0 | POS System |
                            @php
                                $uniqueRates = $tables->pluck('table_rate_id')->filter()->unique()->count();
                            @endphp
                            {{ $uniqueRates }} loại bàn
                        </span>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <span class="flex items-center">
                            <span class="status-indicator bg-green-500"></span>
                            Hệ thống hoạt động bình thường
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards with delay
            document.querySelectorAll('[data-delay]').forEach(el => {
                const delay = parseInt(el.getAttribute('data-delay'));
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, delay);
            });

            // Refresh button
            const refreshBtn = document.getElementById('refresh-stats');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    this.classList.add('animate-spin');
                    updateTableStats();
                    setTimeout(() => {
                        this.classList.remove('animate-spin');
                    }, 1000);
                });
            }

            // Auto refresh every 30 seconds
            setInterval(updateTableStats, 30000);

            // Show welcome notification
            setTimeout(() => {
                showNotification('Hệ thống POS đã sẵn sàng!', 'success', 3000);
            }, 1500);
        });

        // Update table stats
        function updateTableStats() {
            fetch('{{ route('admin.pos.quick-stats') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Animate numbers
                        animateNumber('totalTables', data.tableStats.total);
                        animateNumber('occupiedTables', data.tableStats.occupied);
                        animateNumber('availableTables', data.tableStats.available);

                        // Show notification if occupancy rate changed significantly
                        const currentOccupancy = parseInt(document.querySelector('#occupiedTables').textContent);
                        if (Math.abs(data.tableStats.occupied - currentOccupancy) > 2) {
                            showNotification('Thống kê bàn đã được cập nhật', 'info', 2000);
                        }
                    }
                })
                .catch(err => console.error('Error updating stats:', err));
        }

        // Animate number changes
        function animateNumber(elementId, newValue) {
            const element = document.getElementById(elementId);
            if (!element) return;

            const current = parseInt(element.textContent) || 0;
            if (current === newValue) return;

            const duration = 500;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing
                const ease = 1 - Math.pow(1 - progress, 3);
                const currentNumber = Math.floor(current + (newValue - current) * ease);

                element.textContent = currentNumber;

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    element.textContent = newValue;
                }
            }

            requestAnimationFrame(update);
        }

        // Notification system
        function showNotification(message, type = 'success', duration = 4000) {
            const container = document.createElement('div');
            container.className = `fixed top-4 right-4 z-50 max-w-sm animate-slide-in`;

            const colors = {
                success: 'bg-green-900/80 border-green-700 text-green-100',
                error: 'bg-red-900/80 border-red-700 text-red-100',
                info: 'bg-blue-900/80 border-blue-700 text-blue-100',
                warning: 'bg-yellow-900/80 border-yellow-700 text-yellow-100'
            };

            container.innerHTML = `
            <div class="${colors[type]} border rounded-lg shadow-lg p-4 backdrop-blur-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                                        type === 'error' ? 'exclamation-circle' : 
                                        type === 'warning' ? 'exclamation-triangle' : 'info-circle'} 
                              text-lg"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            class="ml-4 text-gray-400 hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

            document.body.appendChild(container);

            setTimeout(() => {
                container.style.opacity = '0';
                container.style.transform = 'translateX(100%)';
                setTimeout(() => container.remove(), 300);
            }, duration);
        }

        // Add slide-in animation
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    `;
        document.head.appendChild(style);
    </script>
@endsection
