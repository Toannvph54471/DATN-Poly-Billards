<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi Tiết Bàn - {{ $table->table_name }}</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            @apply bg-white border border-gray-300 rounded-none shadow-sm px-6 py-5;
        }

        .btn-primary {
            @apply bg-gray-900 text-white border border-gray-900 rounded-none px-4 py-3 hover:bg-gray-800 transition-colors font-medium;
        }

        .btn-secondary {
            @apply bg-white text-gray-900 border border-gray-400 rounded-none px-4 py-3 hover:bg-gray-50 transition-colors font-medium;
        }

        .btn-warning {
            @apply bg-amber-500 text-white border border-amber-600 rounded-none px-4 py-3 hover:bg-amber-600 transition-colors font-medium;
        }

        .btn-success {
            @apply bg-green-600 text-white border border-green-700 rounded-none px-4 py-3 hover:bg-green-700 transition-colors font-medium;
        }

        .btn-danger {
            @apply bg-red-600 text-white border border-red-700 rounded-none px-4 py-3 hover:bg-red-700 transition-colors font-medium;
        }

        .status-badge {
            @apply text-xs font-medium px-3 py-1 border rounded-none;
        }

        .status-available {
            @apply bg-green-50 text-green-800 border-green-300;
        }

        .status-occupied {
            @apply bg-red-50 text-red-800 border-red-300;
        }

        .status-maintenance {
            @apply bg-yellow-50 text-yellow-800 border-yellow-300;
        }

        .time-display {
            @apply bg-gray-50 border border-gray-300 p-4 text-center;
        }

        .progress-bar {
            @apply w-full bg-gray-200 h-3;
        }

        .progress-fill {
            @apply bg-blue-600 h-3 transition-all duration-1000;
        }

        .combo-mode {
            @apply bg-purple-100 text-purple-800 border border-purple-300;
        }

        .regular-mode {
            @apply bg-blue-100 text-blue-800 border border-blue-300;
        }

        .paused-mode {
            @apply bg-amber-100 text-amber-800 border border-amber-300;
        }

        .blink {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .time-counter {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="max-w-7xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tables.index') }}" class="btn-secondary inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại danh sách
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $table->table_name }}</h1>
                    <p class="text-gray-600 mt-1">Số: {{ $table->table_number }} •
                        {{ $table->tableRate->name ?? 'Chưa phân loại' }}</p>
                </div>
            </div>
            <div class="text-right">
                <div
                    class="status-badge {{ $table->status === 'available' ? 'status-available' : ($table->status === 'occupied' ? 'status-occupied' : 'status-maintenance') }}">
                    {{ $table->status === 'available' ? 'TRỐNG' : ($table->status === 'occupied' ? 'ĐANG SỬ DỤNG' : 'BẢO TRÌ') }}
                </div>
                <div class="text-sm text-gray-600 mt-2">Giá giờ: {{ number_format($table->hourly_rate) }} ₫/h</div>
            </div>
        </div>

        {{-- Real-time Counter Banner --}}
        @if (
            $table->currentBill &&
                $table->currentBill->status === 'Open' &&
                isset($timeInfo['is_running']) &&
                $timeInfo['is_running'] &&
                !$timeInfo['is_paused']
        )
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="text-blue-600">
                        <i class="fas fa-clock blink text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-blue-600 font-medium">THỜI GIAN ĐANG CHẠY</div>
                                <div id="realTimeCounter" class="time-counter text-2xl font-bold text-blue-700">
                                    {{ floor($timeInfo['elapsed_minutes'] / 60) }}:{{ str_pad($timeInfo['elapsed_minutes'] % 60, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-blue-600">CHI PHÍ HIỆN TẠI</div>
                                <div id="realTimeCost" class="text-xl font-bold text-blue-700">
                                    {{ number_format($timeInfo['current_cost'] ?? 0) }} ₫
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            {{-- Left Sidebar --}}
            <div class="xl:col-span-1 space-y-6">
                {{-- Table Info Card --}}
                <div class="card">
                    <h2 class="text-xl font-bold mb-4 border-b border-gray-200 pb-3">THÔNG TIN BÀN</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tên bàn:</span>
                            <span class="font-semibold">{{ $table->table_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Số bàn:</span>
                            <span class="font-semibold">{{ $table->table_number }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Loại bàn:</span>
                            <span class="font-semibold">{{ $table->tableRate->name ?? 'Chưa phân loại' }}</span>
                        </div>
                    </div>

                    @if ($table->currentBill)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600 mb-2">Tổng hiện tại</div>
                            <div id="totalAmountDisplay" class="text-2xl font-bold text-green-600">
                                {{ number_format(round($table->currentBill->final_amount)) }} ₫
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="card">
                    <h3 class="text-lg font-bold mb-4 border-b border-gray-200 pb-3">THAO TÁC NHANH</h3>
                    <div class="space-y-3">
                        @if ($table->currentBill)
                            {{-- Pause/Resume Buttons --}}
                            @if (isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                                <form action="{{ route('bills.pause', $table->currentBill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full btn-warning text-center">
                                        <i class="fas fa-pause mr-2"></i>
                                        TẠM DỪNG
                                    </button>
                                </form>
                            @endif

                            @if (isset($timeInfo['is_paused']) && $timeInfo['is_paused'])
                                <form action="{{ route('bills.resume', $table->currentBill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full btn-success text-center">
                                        <i class="fas fa-play mr-2"></i>
                                        TIẾP TỤC
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                class="w-full btn-primary text-center block">
                                <i class="fas fa-credit-card mr-2"></i>
                                THANH TOÁN
                            </a>

                            <button onclick="updateBillTotal()" class="w-full btn-secondary text-center">
                                <i class="fas fa-sync-alt mr-2"></i>
                                CẬP NHẬT TỔNG
                            </button>

                            @if (isset($timeInfo['mode']) &&
                                    $timeInfo['mode'] === 'combo' &&
                                    isset($timeInfo['is_near_end']) &&
                                    $timeInfo['is_near_end']
                            )
                                <form action="{{ route('bills.extend-combo', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="extra_minutes" value="30">
                                    <button type="submit" class="w-full btn-warning text-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        GIA HẠN 30 PHÚT
                                    </button>
                                </form>
                            @endif

                            @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                                <form action="{{ route('bills.switch-regular', $table->currentBill->id) }}"
                                    method="POST" onsubmit="return confirm('Chuyển sang tính giờ thường?')">
                                    @csrf
                                    <button type="submit" class="w-full btn-secondary text-center">
                                        <i class="fas fa-exchange-alt mr-2"></i>
                                        CHUYỂN GIỜ THƯỜNG
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('bills.convert-to-quick', $table->currentBill->id) }}"
                                method="POST" onsubmit="return confirm('Chuyển thành bàn lẻ?')">
                                @csrf
                                <button type="submit" class="w-full btn-secondary text-center">
                                    <i class="fas fa-coins mr-2"></i>
                                    CHUYỂN BÀN LẺ
                                </button>
                            </form>
                        @else
                            <form action="{{ route('bills.create') }}" method="POST">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <input type="hidden" name="guest_count" value="1">
                                <button type="submit" class="w-full btn-primary text-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    TẠO HÓA ĐƠN MỚI
                                </button>
                            </form>

                            <form action="{{ route('bills.quick-create') }}" method="POST">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <button type="submit" class="w-full btn-secondary text-center">
                                    <i class="fas fa-bolt mr-2"></i>
                                    TẠO BÀN LẺ
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Thời gian tạm dừng --}}
                    @if (isset($timeInfo['paused_duration']) && $timeInfo['paused_duration'] > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">Thời gian tạm dừng:</div>
                            <div class="font-semibold text-amber-600">
                                {{ floor($timeInfo['paused_duration'] / 60) }}h
                                {{ $timeInfo['paused_duration'] % 60 }}p
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Customer Info --}}
                @if ($table->currentBill && $table->currentBill->user)
                    <div class="card">
                        <h3 class="text-lg font-bold mb-4 border-b border-gray-200 pb-3">THÔNG TIN KHÁCH HÀNG</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Tên khách hàng</div>
                                <div class="font-semibold">{{ $table->currentBill->user->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Số điện thoại</div>
                                <div class="font-semibold">{{ $table->currentBill->user->phone }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Loại khách</div>
                                <div>
                                    <span
                                        class="px-3 py-1 bg-purple-100 text-purple-800 border border-purple-300 text-xs font-medium">
                                        {{ $table->currentBill->user->customer_type ?? 'Khách mới' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Số lần đến</div>
                                <div class="font-semibold">{{ $table->currentBill->user->total_visits ?? 0 }} lần
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Main Content --}}
            <div class="xl:col-span-3 space-y-6">
                {{-- Time Tracking --}}
                <div class="card">
                    <div
                        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
                        <div>
                            <h2 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-3"></i>
                                THEO DÕI THỜI GIAN
                            </h2>
                            <p class="text-gray-600 mt-1">Cập nhật thời gian thực từ server</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div id="modeBadge"
                                class="px-4 py-2 text-sm font-semibold rounded-none 
                                {{ isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' ? 'combo-mode' : (isset($timeInfo['mode']) && $timeInfo['mode'] === 'regular' ? 'regular-mode' : 'bg-gray-100 text-gray-800 border border-gray-300') }}">
                                {{ isset($timeInfo['mode']) && $timeInfo['mode'] === 'regular' ? '🕒 GIỜ THƯỜNG' : (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' ? '🎁 COMBO TIME' : 'KHÔNG HOẠT ĐỘNG') }}
                            </div>
                            @if (isset($timeInfo['is_paused']) && $timeInfo['is_paused'])
                                <div class="paused-mode px-4 py-2 text-sm font-semibold rounded-none">
                                    ⏸️ TẠM DỪNG
                                </div>
                            @endif
                            @if (isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                                <div
                                    class="bg-green-100 text-green-800 border border-green-300 px-4 py-2 text-sm font-semibold rounded-none blink">
                                    ▶️ ĐANG CHẠY
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        {{-- Current Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-2">THỜI GIAN HIỆN TẠI</div>
                            <div id="currentTime" class="text-2xl font-mono font-bold text-gray-900">--:--:--</div>
                        </div>

                        {{-- Elapsed Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-2">ĐÃ SỬ DỤNG</div>
                            <div id="elapsedTimeDisplay" class="text-2xl font-mono font-bold text-blue-600">
                                {{ isset($timeInfo['elapsed_minutes']) ? sprintf('%02d:%02d:%02d', floor($timeInfo['elapsed_minutes'] / 60), $timeInfo['elapsed_minutes'] % 60, 0) : '00:00:00' }}
                            </div>
                        </div>

                        {{-- Remaining Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-2">THỜI GIAN CÒN LẠI</div>
                            <div id="remainingTimeDisplay" class="text-2xl font-mono font-bold text-green-600">
                                @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && isset($timeInfo['remaining_minutes']))
                                    {{ sprintf('%02d:%02d', floor($timeInfo['remaining_minutes'] / 60), $timeInfo['remaining_minutes'] % 60) }}
                                @else
                                    --:--
                                @endif
                            </div>
                        </div>

                        {{-- Current Cost --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-2">CHI PHÍ HIỆN TẠI</div>
                            <div id="currentCostDisplay" class="text-2xl font-bold text-amber-600">
                                {{ number_format(round($timeInfo['current_cost'] ?? 0)) }} ₫
                            </div>
                        </div>

                        {{-- Paused Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-2">TẠM DỪNG</div>
                            <div class="text-2xl font-bold text-gray-600">
                                {{ isset($timeInfo['paused_duration']) ? floor($timeInfo['paused_duration'] / 60) : 0 }}h
                                {{ isset($timeInfo['paused_duration']) ? $timeInfo['paused_duration'] % 60 : 0 }}p
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>TIẾN ĐỘ SỬ DỤNG COMBO</span>
                                <span id="progressText">
                                    @if (isset($timeInfo['total_minutes']) && $timeInfo['total_minutes'] > 0)
                                        {{ round(min(100, (($timeInfo['elapsed_minutes'] ?? 0) / $timeInfo['total_minutes']) * 100)) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                            <div class="progress-bar">
                                <div id="progressBar" class="progress-fill"
                                    style="width: {{ isset($timeInfo['total_minutes']) && $timeInfo['total_minutes'] > 0 ? min(100, (($timeInfo['elapsed_minutes'] ?? 0) / $timeInfo['total_minutes']) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Add Products & Combos --}}
                @if ($table->currentBill)
                    <div class="card">
                        <h2 class="text-2xl font-bold mb-6 border-b border-gray-200 pb-4">
                            <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                            THÊM SẢN PHẨM & COMBO
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Add Combo --}}
                            <div class="border border-gray-300 p-5 bg-purple-50">
                                <h3 class="text-lg font-bold mb-4 flex items-center text-purple-800">
                                    <i class="fas fa-gift mr-2"></i>
                                    THÊM COMBO
                                </h3>
                                <form action="{{ route('bills.add-combo', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <select name="combo_id"
                                            class="w-full border border-gray-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                            required>
                                            <option value="">Chọn combo...</option>
                                            @foreach ($combos as $combo)
                                                <option value="{{ $combo->id }}">{{ $combo->name }} -
                                                    {{ number_format($combo->price) }}₫</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-3">
                                            <input type="number" name="quantity" value="1" min="1"
                                                class="flex-1 border border-gray-300 px-4 py-3 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                                required>
                                            <button type="submit" class="btn-primary px-6">
                                                <i class="fas fa-plus mr-2"></i>
                                                THÊM
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- Add Product --}}
                            <div class="border border-gray-300 p-5 bg-green-50">
                                <h3 class="text-lg font-bold mb-4 flex items-center text-green-800">
                                    <i class="fas fa-utensils mr-2"></i>
                                    THÊM SẢN PHẨM
                                </h3>
                                <form action="{{ route('bills.add-product', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <select name="product_id"
                                            class="w-full border border-gray-300 px-4 py-3 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                            required>
                                            <option value="">Chọn sản phẩm...</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} -
                                                    {{ number_format($product->price) }}₫</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-3">
                                            <input type="number" name="quantity" value="1" min="1"
                                                class="flex-1 border border-gray-300 px-4 py-3 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                                                required>
                                            <button type="submit" class="btn-success px-6">
                                                <i class="fas fa-plus mr-2"></i>
                                                THÊM
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Bill Details --}}
                <div class="card">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-receipt text-gray-700 mr-3"></i>
                            CHI TIẾT HÓA ĐƠN
                        </h2>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">TỔNG HÓA ĐƠN</div>
                            <div id="finalAmountDisplay" class="text-3xl font-bold text-green-600">
                                {{ number_format(round($table->currentBill->final_amount ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    @if ($table->currentBill && $table->currentBill->billDetails && $table->currentBill->billDetails->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-300">
                                        <th class="text-left py-4 px-4 font-bold border-r border-gray-300">SẢN
                                            PHẨM/DỊCH VỤ</th>
                                        <th class="text-center py-4 px-4 font-bold border-r border-gray-300">SL</th>
                                        <th class="text-right py-4 px-4 font-bold border-r border-gray-300">ĐƠN GIÁ
                                        </th>
                                        <th class="text-right py-4 px-4 font-bold">THÀNH TIỀN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($table->currentBill->billDetails as $item)
                                        <tr class="border-b border-gray-300 hover:bg-gray-50">
                                            <td class="py-3 px-4 border-r border-gray-300">
                                                <div class="flex items-center">
                                                    @if ($item->product_id && $item->product)
                                                        <i class="fas fa-utensils text-green-600 mr-3"></i>
                                                        <div>
                                                            <div class="font-medium">{{ $item->product->name }}</div>
                                                            @if ($item->is_combo_component)
                                                                <div class="text-xs text-gray-500">Thành phần combo
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @elseif($item->combo_id && $item->combo)
                                                        <i class="fas fa-gift text-purple-600 mr-3"></i>
                                                        <div>
                                                            <div class="font-medium">{{ $item->combo->name }}</div>
                                                            <div class="text-xs text-gray-500">Combo</div>
                                                        </div>
                                                    @else
                                                        <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                                                        <div class="font-medium">{{ $item->note ?? 'Dịch vụ khác' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center py-3 px-4 border-r border-gray-300">
                                                <span
                                                    class="bg-gray-100 px-3 py-1 border border-gray-300 text-sm font-medium">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td class="text-right py-3 px-4 border-r border-gray-300 font-medium">
                                                {{ number_format(round($item->unit_price)) }} ₫
                                            </td>
                                            <td class="text-right py-3 px-4 font-bold text-green-600">
                                                {{ number_format(round($item->total_price)) }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-lg font-bold">TỔNG CỘNG:</div>
                                <div id="billTotalAmount" class="text-3xl font-bold text-green-600">
                                    {{ number_format(round($table->currentBill->final_amount)) }} ₫
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 border border-gray-300">
                            <i class="fas fa-receipt text-5xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 text-lg">CHƯA CÓ SẢN PHẨM NÀO TRONG HÓA ĐƠN</p>
                            <p class="text-gray-400 text-sm mt-2">Thêm sản phẩm hoặc combo để bắt đầu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Server data với giá trị mặc định
        const currentBillId = {{ $table->currentBill->id ?? 'null' }};
        const isRunning = {{ isset($timeInfo['is_running']) && $timeInfo['is_running'] ? 'true' : 'false' }};
        const isPaused = {{ isset($timeInfo['is_paused']) && $timeInfo['is_paused'] ? 'true' : 'false' }};
        const currentMode = '{{ $timeInfo['mode'] ?? 'none' }}';
        const hourlyRate = Number({{ $timeInfo['hourly_rate'] ?? 0 }});
        const totalComboMinutes = Number({{ $timeInfo['total_minutes'] ?? 0 }});
        const elapsedMinutesFromServer = Number({{ $timeInfo['elapsed_minutes'] ?? 0 }});
        const pausedDuration = Number({{ $timeInfo['paused_duration'] ?? 0 }});

        let startTimeMs = null;
        @if ($table->currentBill && isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
            startTimeMs = new Date('{{ $table->currentBill->start_time }}').getTime();
        @endif

        const totalComboSeconds = totalComboMinutes * 60;
        let rafId = null;
        let refreshInterval = null;

        // Format functions
        function pad(n) {
            return n.toString().padStart(2, '0');
        }

        function formatHMS(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            const secs = Math.floor(totalSeconds % 60);
            return `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
        }

        function formatHM(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            return `${pad(hrs)}:${pad(mins)}`;
        }

        function formatCurrency(amount) {
            const rounded = Math.round(amount);
            return new Intl.NumberFormat('vi-VN').format(rounded) + ' ₫';
        }

        function calculateCurrentCost(elapsedSeconds) {
            if (currentMode === 'regular') {
                return (hourlyRate / 3600) * elapsedSeconds;
            } else if (currentMode === 'combo') {
                const extraSeconds = Math.max(0, elapsedSeconds - totalComboSeconds);
                return (hourlyRate / 3600) * extraSeconds;
            }
            return 0;
        }

        // Update UI
        function render(elapsedSeconds) {
            // Current time
            const now = new Date();
            document.getElementById('currentTime').textContent =
                now.toLocaleTimeString('vi-VN', {
                    hour12: false,
                    timeZone: 'Asia/Ho_Chi_Minh'
                });

            // Elapsed time
            document.getElementById('elapsedTimeDisplay').textContent = formatHMS(elapsedSeconds);

            // Remaining time and progress
            if (currentMode === 'combo') {
                const remainingSeconds = totalComboSeconds - elapsedSeconds;
                document.getElementById('remainingTimeDisplay').textContent = formatHM(Math.max(0, remainingSeconds));

                const percent = totalComboSeconds > 0 ? Math.min(100, (elapsedSeconds / totalComboSeconds) * 100) : 0;
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').textContent = Math.round(percent) + '% đã sử dụng';
            }

            // Current cost
            const currentCost = calculateCurrentCost(elapsedSeconds);
            document.getElementById('currentCostDisplay').textContent = formatCurrency(currentCost);

            // Update real-time banner
            updateRealTimeBanner(elapsedSeconds);
        }

        // Update real-time banner
        function updateRealTimeBanner(elapsedSeconds) {
            const counterElement = document.getElementById('realTimeCounter');
            const costElement = document.getElementById('realTimeCost');

            if (counterElement && costElement) {
                const totalMinutes = elapsedSeconds / 60;
                const hours = Math.floor(totalMinutes / 60);
                const minutes = Math.floor(totalMinutes % 60);
                const seconds = Math.floor(elapsedSeconds % 60);

                counterElement.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

                const currentCost = calculateCurrentCost(elapsedSeconds);
                costElement.textContent = formatCurrency(currentCost);
            }
        }

        // Check combo expiration
        function checkComboExpiration(elapsedSeconds) {
            if (currentMode === 'combo' && totalComboSeconds > 0) {
                if (elapsedSeconds >= totalComboSeconds) {
                    stopTimer();

                    // Hiển thị thông báo tạm dừng thay vì chuyển sang giờ thường
                    if (confirm('Combo time đã hết! Bạn có muốn tạm dừng bàn không?')) {
                        // Gọi API để tạm dừng
                        pauseTable();
                    } else {
                        // Nếu không tạm dừng, tiếp tục tính giờ thường
                        switchToRegularTime();
                    }
                }
            }
        }

        // Hàm gọi API tạm dừng
        async function pauseTable() {
            try {
                const response = await fetch(`/admin/bills/${currentBillId}/pause`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    alert('Bàn đã được tạm dừng');
                    location.reload();
                } else {
                    throw new Error('Lỗi khi tạm dừng bàn');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tạm dừng bàn');
            }
        }

        // Hàm chuyển sang giờ thường
        async function switchToRegularTime() {
            try {
                const response = await fetch(`/admin/bills/${currentBillId}/switch-regular`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    throw new Error('Lỗi khi chuyển sang giờ thường');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi chuyển sang giờ thường');
            }
        }

        // Timer loop
        function loop() {
            if (!startTimeMs || isPaused) return;
            const elapsedSeconds = Math.floor((Date.now() - startTimeMs) / 1000);
            render(elapsedSeconds);
            checkComboExpiration(elapsedSeconds);
            rafId = requestAnimationFrame(loop);
        }

        function startTimer() {
            if (!startTimeMs || rafId || isPaused) return;
            rafId = requestAnimationFrame(loop);
        }

        function stopTimer() {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        }

        // Real-time counter for banner
        function startRealTimeCounter() {
            let totalSeconds = elapsedMinutesFromServer * 60;

            refreshInterval = setInterval(() => {
                if (isRunning && !isPaused) {
                    totalSeconds += 1;
                    updateRealTimeBanner(totalSeconds);
                    checkComboExpiration(totalSeconds);
                }
            }, 1000);
        }

        // Update bill total
        function updateBillTotal() {
            @if ($table->currentBill)
                fetch('{{ route('bills.update-total', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).then(r => {
                    if (!r.ok) throw new Error('Network response was not ok');
                    return r.json();
                }).then(data => {
                    if (data.success) {
                        const final = data.final_amount;
                        ['totalAmountDisplay', 'finalAmountDisplay', 'billTotalAmount'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = formatCurrency(final);
                        });
                    } else {
                        console.error('Update failed:', data.message);
                    }
                }).catch(error => {
                    console.error('Error updating bill total:', error);
                });
            @endif
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            @if ($table->currentBill && $table->currentBill->status === 'Open')
                const initialElapsedSeconds = Math.floor(elapsedMinutesFromServer * 60);
                render(initialElapsedSeconds);

                if (isRunning && startTimeMs && !isPaused) {
                    startTimer();
                }

                // Start real-time counter for banner
                if (isRunning && !isPaused) {
                    startRealTimeCounter();
                }

                // Auto update bill total every 30 seconds
                setInterval(updateBillTotal, 30000);
            @endif
        });

        window.addEventListener('beforeunload', function() {
            stopTimer();
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>

</html>
