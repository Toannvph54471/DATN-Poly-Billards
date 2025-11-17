@extends('admin.layouts.app')

@section('title', 'Chi Tiết Bàn - ' . $table->table_name)

@section('styles')
    <style>
        .card {
            @apply bg-white border-2 rounded-xl shadow-lg px-6 py-5 transition-all duration-300 hover:shadow-xl;
        }

        .card-primary {
            @apply bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200;
        }

        .card-success {
            @apply bg-gradient-to-br from-green-50 to-emerald-50 border-green-200;
        }

        .card-warning {
            @apply bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200;
        }

        .card-purple {
            @apply bg-gradient-to-br from-purple-50 to-violet-50 border-purple-200;
        }

        .card-gray {
            @apply bg-gradient-to-br from-gray-50 to-slate-50 border-gray-200;
        }

        .btn-primary {
            @apply bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-0 rounded-xl px-6 py-3 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-md hover:shadow-lg;
        }

        .btn-secondary {
            @apply bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 rounded-xl px-6 py-3 hover:from-gray-200 hover:to-gray-300 transition-all duration-300 font-semibold shadow-sm hover:shadow-md;
        }

        .btn-success {
            @apply bg-gradient-to-r from-green-500 to-emerald-500 text-white border-0 rounded-xl px-6 py-3 hover:from-green-600 hover:to-emerald-600 transition-all duration-300 font-semibold shadow-md hover:shadow-lg;
        }

        .btn-warning {
            @apply bg-gradient-to-r from-amber-500 to-orange-500 text-white border-0 rounded-xl px-6 py-3 hover:from-amber-600 hover:to-orange-600 transition-all duration-300 font-semibold shadow-md hover:shadow-lg;
        }

        .btn-danger {
            @apply bg-gradient-to-r from-red-500 to-pink-500 text-white border-0 rounded-xl px-6 py-3 hover:from-red-600 hover:to-pink-600 transition-all duration-300 font-semibold shadow-md hover:shadow-lg;
        }

        .status-badge {
            @apply text-xs font-bold px-4 py-2 border-0 rounded-full shadow-sm;
        }

        .status-available {
            @apply bg-gradient-to-r from-green-100 to-emerald-100 text-green-800;
        }

        .status-occupied {
            @apply bg-gradient-to-r from-red-100 to-pink-100 text-red-800;
        }

        .status-maintenance {
            @apply bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800;
        }

        .status-paused {
            @apply bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800;
        }

        .time-display {
            @apply bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 p-6 text-center rounded-2xl shadow-sm;
        }

        .progress-bar {
            @apply w-full bg-gray-200 h-4 rounded-full overflow-hidden shadow-inner;
        }

        .progress-fill {
            @apply bg-gradient-to-r from-blue-500 to-purple-500 h-4 rounded-full transition-all duration-1000 shadow-md;
        }

        .combo-mode {
            @apply bg-gradient-to-r from-purple-100 to-violet-100 text-purple-800 border-0 rounded-full px-4 py-2 font-bold;
        }

        .regular-mode {
            @apply bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border-0 rounded-full px-4 py-2 font-bold;
        }

        .quick-mode {
            @apply bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border-0 rounded-full px-4 py-2 font-bold;
        }

        .paused-mode {
            @apply bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border-0 rounded-full px-4 py-2 font-bold;
        }

        .blink {
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        .time-counter {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            background: linear-gradient(135deg, #1e40af, #3730a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-box {
            @apply w-full border-2 border-gray-300 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm;
        }

        .quantity-input {
            @apply w-20 border-2 border-gray-300 px-3 py-2 rounded-lg text-center focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200;
        }

        .product-item,
        .combo-item {
            @apply border-2 border-gray-200 p-4 mb-3 cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 rounded-xl shadow-sm hover:shadow-md;
        }

        .scrollable-container {
            @apply border-2 border-gray-200 rounded-xl bg-white overflow-hidden shadow-inner;
        }

        .scroll-content {
            @apply max-h-80 overflow-y-auto p-4;
        }

        .scroll-content::-webkit-scrollbar {
            width: 8px;
        }

        .scroll-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .scroll-content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
            border-radius: 10px;
        }

        .scroll-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #94a3b8, #64748b);
        }

        .collapsible {
            transition: all 0.3s ease-in-out;
        }

        .collapsible-content {
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }

        .section-title {
            @apply text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent;
        }

        .price-tag {
            @apply bg-gradient-to-r from-emerald-500 to-green-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-md;
        }

        .info-badge {
            @apply bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-sm;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-8xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8 p-6 bg-white rounded-2xl shadow-lg border-2 border-gray-100">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tables.index') }}" class="btn-secondary inline-flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Quay lại danh sách
                </a>
                <div class="ml-4">
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        {{ $table->table_name }}
                    </h1>
                    <p class="text-gray-600 mt-2 flex items-center gap-2">
                        <span class="info-badge">Số: {{ $table->table_number }}</span>
                        <span class="text-gray-500">•</span>
                        <span class="font-medium text-gray-700">{{ $table->category->name ?? 'Chưa phân loại' }}</span>
                    </p>
                </div>
            </div>
            <div class="text-right">
                @php
                    $statusClass = match ($table->status) {
                        'available' => 'status-available',
                        'occupied' => 'status-occupied',
                        'maintenance' => 'status-maintenance',
                        'paused' => 'status-paused',
                        default => 'status-available',
                    };

                    $statusText = match ($table->status) {
                        'available' => '🟢 TRỐNG',
                        'occupied' => '🔴 ĐANG SỬ DỤNG',
                        'maintenance' => '🟡 BẢO TRÌ',
                        'paused' => '🔵 TẠM DỪNG',
                        default => '🟢 TRỐNG',
                    };
                @endphp
                <div class="status-badge {{ $statusClass }} text-lg">
                    {{ $statusText }}
                </div>
                <div class="mt-3 text-sm text-gray-600 flex items-center justify-end gap-2">
                    <i class="fas fa-clock text-blue-500"></i>
                    <span class="font-semibold">Giá giờ: <span
                            class="price-tag">{{ number_format($table->getHourlyRate()) }} ₫/h</span></span>
                </div>
            </div>
        </div>

        {{-- Real-time Counter Banner --}}
        @if (
            $table->currentBill &&
                in_array($table->currentBill->status, ['Open', 'quick']) &&
                isset($timeInfo['is_running']) &&
                $timeInfo['is_running']
        )
            <div class="mb-8 p-6 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-xl text-white">
                <div class="flex items-center space-x-6">
                    <div class="text-white">
                        <i class="fas fa-clock blink text-4xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-semibold text-blue-100 mb-2">THỜI GIAN ĐANG CHẠY</div>
                                <div id="realTimeCounter" class="time-counter text-3xl font-bold text-white">
                                    {{ floor($timeInfo['elapsed_minutes'] / 60) }}:{{ str_pad($timeInfo['elapsed_minutes'] % 60, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-blue-100 mb-2">CHI PHÍ HIỆN TẠI</div>
                                <div id="realTimeCost" class="text-2xl font-bold text-white">
                                    {{ number_format(round($timeInfo['current_cost'] ?? 0)) }} ₫
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            {{-- Left Side - Products & Combos --}}
            <div class="xl:col-span-4 space-y-8">
                {{-- Products Section --}}
                <div class="card card-success">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleCollapse('products')">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-utensils text-green-600 mr-3 text-2xl"></i>
                            <span class="section-title">SẢN PHẨM</span>
                            <span class="ml-3 info-badge bg-green-500">
                                {{ $products->count() }} sản phẩm
                            </span>
                        </h2>
                        <i class="fas fa-chevron-down text-green-500 transition-transform text-lg" id="productsIcon"></i>
                    </div>

                    <div class="collapsible-content" id="productsContent" style="max-height: 500px">
                        <div class="mt-6">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="productSearch" placeholder="Tìm kiếm sản phẩm..."
                                    class="search-box pl-12">
                            </div>

                            <div class="scrollable-container mt-4">
                                <div class="scroll-content">
                                    @foreach ($products as $product)
                                        <div class="product-item" data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                            <div class="flex justify-between items-center mb-3">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <span class="price-tag">{{ number_format($product->price) }}
                                                            ₫</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-700">Số lượng:</span>
                                                    <input type="number" min="1" value="1"
                                                        class="quantity-input product-quantity"
                                                        data-product-id="{{ $product->id }}">
                                                </div>
                                                <button type="button" class="btn-success px-4 py-2 text-sm add-product-btn"
                                                    data-product-id="{{ $product->id }}">
                                                    <i class="fas fa-plus mr-2"></i> Thêm
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Combos Section --}}
                <div class="card card-purple">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleCollapse('combos')">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-gift text-purple-600 mr-3 text-2xl"></i>
                            <span class="section-title">COMBO</span>
                            <span class="ml-3 info-badge bg-purple-500">
                                {{ $combos->count() }} combo
                            </span>
                        </h2>
                        <i class="fas fa-chevron-down text-purple-500 transition-transform text-lg" id="combosIcon"></i>
                    </div>

                    <div class="collapsible-content" id="combosContent" style="max-height: 500px">
                        <div class="mt-6">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="comboSearch" placeholder="Tìm kiếm combo..."
                                    class="search-box pl-12">
                            </div>

                            <div class="scrollable-container mt-4">
                                <div class="scroll-content">
                                    @foreach ($combos as $combo)
                                        <div class="combo-item" data-id="{{ $combo->id }}"
                                            data-name="{{ $combo->name }}" data-price="{{ $combo->price }}">
                                            <div class="flex justify-between items-center mb-3">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-800">{{ $combo->name }}</div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <span class="price-tag">{{ number_format($combo->price) }}
                                                            ₫</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-700">Số lượng:</span>
                                                    <input type="number" min="1" value="1"
                                                        class="quantity-input combo-quantity"
                                                        data-combo-id="{{ $combo->id }}">
                                                </div>
                                                <button type="button" class="btn-primary px-4 py-2 text-sm add-combo-btn"
                                                    data-combo-id="{{ $combo->id }}">
                                                    <i class="fas fa-plus mr-2"></i> Thêm
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Products for New Bill --}}
                @if (!$table->currentBill)
                    <div class="card card-warning">
                        <h2 class="text-xl font-bold mb-6 border-b border-amber-200 pb-4 flex items-center">
                            <i class="fas fa-bolt text-amber-600 mr-3 text-2xl"></i>
                            <span class="section-title">SẢN PHẨM NHANH</span>
                        </h2>

                        <div class="scrollable-container">
                            <div class="scroll-content">
                                @foreach ($products->take(8) as $product)
                                    <div class="product-item" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <span class="price-tag">{{ number_format($product->price) }} ₫</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-700">Số lượng:</span>
                                                <input type="number" min="1" value="1"
                                                    class="quantity-input quick-product-quantity"
                                                    data-product-id="{{ $product->id }}">
                                            </div>
                                            <button type="button"
                                                class="btn-warning px-4 py-2 text-sm add-quick-product-btn"
                                                data-product-id="{{ $product->id }}">
                                                <i class="fas fa-plus mr-2"></i> Thêm
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Center - Time Tracking & Bill Details --}}
            <div class="xl:col-span-5 space-y-8">
                {{-- Time Tracking --}}
                <div class="card card-primary">
                    <div
                        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 border-b border-blue-200 pb-6">
                        <div>
                            <h2 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-4 text-3xl"></i>
                                <span class="section-title">THEO DÕI THỜI GIAN</span>
                            </h2>
                            <p class="text-gray-600 mt-3 flex items-center gap-2">
                                <i class="fas fa-database text-blue-400"></i>
                                <span>Cập nhật từ dữ liệu server</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @php
                                $modeClass = match ($timeInfo['mode'] ?? 'none') {
                                    'combo' => 'combo-mode',
                                    'regular' => 'regular-mode',
                                    'quick' => 'quick-mode',
                                    default
                                        => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 rounded-full px-4 py-2 font-bold',
                                };

                                $modeText = match ($timeInfo['mode'] ?? 'none') {
                                    'regular' => '🕒 GIỜ THƯỜNG',
                                    'combo' => '🎁 COMBO TIME',
                                    'quick' => '⚡ BÀN LẺ',
                                    default => '⏸️ KHÔNG HOẠT ĐỘNG',
                                };
                            @endphp
                            <div id="modeBadge" class="{{ $modeClass }} text-sm">
                                {{ $modeText }}
                            </div>
                            @if (isset($timeInfo['is_paused']) && $timeInfo['is_paused'])
                                <div class="paused-mode text-sm">
                                    ⏸️ TẠM DỪNG
                                </div>
                            @endif
                            @if (isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                                <div class="quick-mode text-sm blink">
                                    ▶️ ĐANG CHẠY
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        {{-- Elapsed Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-3 font-semibold">ĐÃ SỬ DỤNG</div>
                            <div id="elapsedTimeDisplay" class="text-2xl font-mono font-bold text-blue-700">
                                {{ isset($timeInfo['elapsed_minutes']) ? sprintf('%02d:%02d:%02d', floor($timeInfo['elapsed_minutes'] / 60), $timeInfo['elapsed_minutes'] % 60, 0) : '00:00:00' }}
                            </div>
                        </div>

                        {{-- Remaining Time --}}
                        <div class="time-display">
                            <div class="text-xs text-gray-500 mb-3 font-semibold">THỜI GIAN CÒN LẠI</div>
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
                            <div class="text-xs text-gray-500 mb-3 font-semibold">CHI PHÍ HIỆN TẠI</div>
                            <div id="currentCostDisplay" class="text-2xl font-bold text-amber-600">
                                {{ number_format(round($timeInfo['current_cost'] ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                        <div class="border-t border-blue-200 pt-6">
                            <div class="flex justify-between text-sm text-gray-600 mb-3 font-semibold">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-chart-line text-purple-500"></i>
                                    TIẾN ĐỘ SỬ DỤNG COMBO
                                </span>
                                <span id="progressText" class="font-bold text-purple-600">
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

                {{-- Bill Details --}}
                <div class="card card-gray">
                    <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-6">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-receipt text-gray-600 mr-4 text-3xl"></i>
                            <span class="section-title">CHI TIẾT HÓA ĐƠN</span>
                        </h2>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 font-semibold mb-2">TỔNG HÓA ĐƠN</div>
                            <div id="finalAmountDisplay"
                                class="text-3xl font-bold text-green-600 bg-gradient-to-r from-green-100 to-emerald-100 px-4 py-2 rounded-xl">
                                {{ number_format(round($table->currentBill->final_amount ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                        <div class="overflow-x-auto rounded-xl border-2 border-gray-200">
                            <table class="w-full rounded-xl overflow-hidden">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                                        <th class="text-left py-5 px-6 font-bold text-gray-700 border-r border-gray-300">
                                            SẢN PHẨM/DỊCH VỤ</th>
                                        <th class="text-center py-5 px-6 font-bold text-gray-700 border-r border-gray-300">
                                            SL</th>
                                        <th class="text-right py-5 px-6 font-bold text-gray-700 border-r border-gray-300">
                                            ĐƠN GIÁ</th>
                                        <th class="text-right py-5 px-6 font-bold text-gray-700">THÀNH TIỀN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($table->currentBill->billDetails as $item)
                                        <tr
                                            class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                                            <td class="py-4 px-6 border-r border-gray-200">
                                                <div class="flex items-center">
                                                    @if ($item->product_id && $item->product)
                                                        <i class="fas fa-utensils text-green-500 mr-4 text-lg"></i>
                                                        <div>
                                                            <div class="font-semibold text-gray-800">
                                                                {{ $item->product->name }}</div>
                                                            @if ($item->is_combo_component)
                                                                <div
                                                                    class="text-xs text-gray-500 bg-green-100 px-2 py-1 rounded-full mt-1 inline-block">
                                                                    Thành phần combo</div>
                                                            @endif
                                                        </div>
                                                    @elseif($item->combo_id && $item->combo)
                                                        <i class="fas fa-gift text-purple-500 mr-4 text-lg"></i>
                                                        <div>
                                                            <div class="font-semibold text-gray-800">
                                                                {{ $item->combo->name }}</div>
                                                            <div
                                                                class="text-xs text-gray-500 bg-purple-100 px-2 py-1 rounded-full mt-1 inline-block">
                                                                Combo</div>
                                                        </div>
                                                    @else
                                                        <i class="fas fa-plus-circle text-blue-500 mr-4 text-lg"></i>
                                                        <div class="font-semibold text-gray-800">
                                                            {{ $item->note ?? 'Dịch vụ khác' }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center py-4 px-6 border-r border-gray-200">
                                                <span
                                                    class="bg-gradient-to-r from-blue-100 to-indigo-100 px-3 py-2 border border-blue-200 text-sm font-semibold rounded-full text-blue-800">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td
                                                class="text-right py-4 px-6 border-r border-gray-200 font-semibold text-gray-700">
                                                {{ number_format(round($item->unit_price)) }} ₫
                                            </td>
                                            <td class="text-right py-4 px-6 font-bold text-green-600">
                                                {{ number_format(round($item->total_price)) }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div
                                class="flex justify-between items-center bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-2xl border-2 border-green-200">
                                <div class="text-xl font-bold text-gray-800">TỔNG CỘNG:</div>
                                <div id="billTotalAmount" class="text-4xl font-bold text-green-600">
                                    {{ number_format(round($table->currentBill->final_amount)) }} ₫
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="text-center py-16 border-2 border-dashed border-gray-300 rounded-2xl bg-gradient-to-br from-gray-50 to-white">
                            <i class="fas fa-receipt text-6xl text-gray-400 mb-6"></i>
                            <p class="text-gray-500 text-xl font-semibold mb-3">CHƯA CÓ SẢN PHẨM NÀO TRONG HÓA ĐƠN</p>
                            <p class="text-gray-400 text-sm">Thêm sản phẩm hoặc combo để bắt đầu</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side - Table Info & Actions --}}
            <div class="xl:col-span-3 space-y-8">
                {{-- Table Info --}}
                <div class="card card-primary">
                    <h2 class="text-xl font-bold mb-6 border-b border-blue-200 pb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3 text-2xl"></i>
                        <span class="section-title">THÔNG TIN BÀN</span>
                    </h2>

                    <div class="space-y-5">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                            <span class="text-gray-600 font-medium">Tên bàn:</span>
                            <span class="font-semibold text-blue-700">{{ $table->table_name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                            <span class="text-gray-600 font-medium">Số bàn:</span>
                            <span class="font-semibold text-blue-700">{{ $table->table_number }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                            <span class="text-gray-600 font-medium">Giá giờ:</span>
                            <span class="font-semibold text-green-600">{{ number_format($table->getHourlyRate()) }}
                                ₫/h</span>
                        </div>
                        @if ($table->currentBill)
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl">
                                <span class="text-gray-600 font-medium">Mã bill:</span>
                                <span class="font-semibold text-purple-600">{{ $table->currentBill->bill_number }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl">
                                <span class="text-gray-600 font-medium">Nhân viên mở bàn:</span>
                                <span class="font-semibold text-purple-700">
                                    {{ $table->currentBill->staff->name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl">
                                <span class="text-gray-600 font-medium">Thời gian mở:</span>
                                <span class="font-semibold text-sm text-purple-700">
                                    {{ $table->currentBill->start_time ? $table->currentBill->start_time->format('H:i d/m/Y') : 'N/A' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($table->currentBill)
                        <div class="mt-8 pt-6 border-t border-blue-200">
                            <div class="text-sm text-gray-600 mb-3 font-semibold">TỔNG HIỆN TẠI</div>
                            <div id="totalAmountDisplay"
                                class="text-3xl font-bold text-green-600 bg-gradient-to-r from-green-100 to-emerald-100 p-4 rounded-2xl text-center">
                                {{ number_format(round($table->currentBill->final_amount)) }} ₫
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="card card-warning">
                    <h3 class="text-lg font-bold mb-6 border-b border-amber-200 pb-4 flex items-center">
                        <i class="fas fa-bolt text-amber-600 mr-3 text-xl"></i>
                        <span class="section-title">THAO TÁC NHANH</span>
                    </h3>
                    <div class="space-y-4">
                        @if ($table->currentBill)
                            {{-- Xử lý bàn lẻ --}}
                            @if ($table->currentBill->status === 'quick')
                                <form action="{{ route('bills.start-playing', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="w-full btn-primary text-center py-4">
                                        <i class="fas fa-play mr-3"></i>
                                        BẮT ĐẦU TÍNH GIỜ
                                    </button>
                                </form>
                            @else
                                {{-- Pause/Resume Buttons --}}
                                @if (isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                                    <form action="{{ route('bills.pause', $table->currentBill->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full btn-warning text-center py-4">
                                            <i class="fas fa-pause mr-3"></i>
                                            TẠM DỪNG
                                        </button>
                                    </form>
                                @endif

                                @if (isset($timeInfo['is_paused']) && $timeInfo['is_paused'])
                                    <form action="{{ route('bills.resume', $table->currentBill->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full btn-success text-center py-4">
                                            <i class="fas fa-play mr-3"></i>
                                            TIẾP TỤC
                                        </button>
                                    </form>
                                @endif

                                {{-- Thanh toán --}}
                                <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                    class="w-full btn-primary text-center block py-4">
                                    <i class="fas fa-credit-card mr-3"></i>
                                    THANH TOÁN
                                </a>

                                {{-- Cập nhật tổng --}}
                                <button onclick="updateBillTotal()" class="w-full btn-secondary text-center py-4">
                                    <i class="fas fa-sync-alt mr-3"></i>
                                    CẬP NHẬT TỔNG
                                </button>

                                {{-- Gia hạn combo --}}
                                @if (isset($timeInfo['mode']) &&
                                        $timeInfo['mode'] === 'combo' &&
                                        isset($timeInfo['is_near_end']) &&
                                        $timeInfo['is_near_end']
                                )
                                    <form action="{{ route('bills.extend-combo', $table->currentBill->id) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="extra_minutes" value="30">
                                        <button type="submit" class="w-full btn-warning text-center py-4">
                                            <i class="fas fa-clock mr-3"></i>
                                            GIA HẠN 30 PHÚT
                                        </button>
                                    </form>
                                @endif

                                {{-- Chuyển sang giờ thường --}}
                                @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                                    <form action="{{ route('bills.switch-regular', $table->currentBill->id) }}"
                                        method="POST" onsubmit="return confirm('Chuyển sang tính giờ thường?')">
                                        @csrf
                                        <button type="submit" class="w-full btn-secondary text-center py-4">
                                            <i class="fas fa-exchange-alt mr-3"></i>
                                            CHUYỂN GIỜ THƯỜNG
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- Chuyển thành bàn lẻ --}}
                            <form action="{{ route('bills.convert-to-quick', $table->currentBill->id) }}" method="POST"
                                onsubmit="return confirm('Chuyển thành bàn lẻ?')">
                                @csrf
                                <button type="submit" class="w-full btn-secondary text-center py-4">
                                    <i class="fas fa-coins mr-3"></i>
                                    CHUYỂN BÀN LẺ
                                </button>
                            </form>
                        @else
                            {{-- Tạo bill mới --}}
                            <form action="{{ route('bills.create') }}" method="POST">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <input type="hidden" name="guest_count" value="1">
                                <button type="submit" class="w-full btn-primary text-center py-4">
                                    <i class="fas fa-plus mr-3"></i>
                                    TẠO HÓA ĐƠN TÍNH GIỜ
                                </button>
                            </form>

                            {{-- Tạo bàn lẻ --}}
                            <form action="{{ route('bills.quick-create') }}" method="POST">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <button type="submit" class="w-full btn-warning text-center py-4">
                                    <i class="fas fa-bolt mr-3"></i>
                                    TẠO BÀN LẺ
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Thời gian tạm dừng --}}
                    @if (isset($timeInfo['paused_duration']) && $timeInfo['paused_duration'] > 0)
                        <div class="mt-6 pt-6 border-t border-amber-200">
                            <div class="text-sm text-gray-600 font-semibold mb-2">Thời gian tạm dừng:</div>
                            <div class="font-bold text-amber-600 text-xl bg-amber-100 p-3 rounded-xl text-center">
                                {{ floor($timeInfo['paused_duration'] / 60) }}h
                                {{ $timeInfo['paused_duration'] % 60 }}p
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Customer Info --}}
                @if ($table->currentBill && $table->currentBill->user)
                    <div class="card card-purple">
                        <h3 class="text-lg font-bold mb-6 border-b border-purple-200 pb-4 flex items-center">
                            <i class="fas fa-user text-purple-600 mr-3 text-xl"></i>
                            <span class="section-title">THÔNG TIN KHÁCH HÀNG</span>
                        </h3>
                        <div class="space-y-5">
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-sm text-gray-600 mb-1 font-semibold">Tên khách hàng</div>
                                <div class="font-semibold text-purple-700">{{ $table->currentBill->user->name }}</div>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-sm text-gray-600 mb-1 font-semibold">Số điện thoại</div>
                                <div class="font-semibold text-purple-700">{{ $table->currentBill->user->phone }}</div>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-sm text-gray-600 mb-1 font-semibold">Loại khách</div>
                                <div>
                                    <span
                                        class="px-3 py-2 bg-gradient-to-r from-purple-500 to-violet-500 text-white text-xs font-bold rounded-full shadow-md">
                                        {{ $table->currentBill->user->customer_type ?? 'Khách mới' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-sm text-gray-600 mb-1 font-semibold">Số lần đến</div>
                                <div class="font-semibold text-purple-700">
                                    {{ $table->currentBill->user->total_visits ?? 0 }} lần</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Collapse functionality
        function toggleCollapse(section) {
            const content = document.getElementById(section + 'Content');
            const icon = document.getElementById(section + 'Icon');

            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = '0px';
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.style.maxHeight = '500px';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Server data với giá trị mặc định
        const isRunning = {{ isset($timeInfo['is_running']) && $timeInfo['is_running'] ? 'true' : 'false' }};
        const isPaused = {{ isset($timeInfo['is_paused']) && $timeInfo['is_paused'] ? 'true' : 'false' }};
        const currentMode = '{{ $timeInfo['mode'] ?? 'none' }}';
        const hourlyRate = Number({{ $timeInfo['hourly_rate'] ?? 0 }});
        const totalComboMinutes = Number({{ $timeInfo['total_minutes'] ?? 0 }});
        const elapsedMinutesFromServer = Number({{ $timeInfo['elapsed_minutes'] ?? 0 }});
        const pausedDuration = Number({{ $timeInfo['paused_duration'] ?? 0 }});
        const currentBillId = {{ $table->currentBill->id ?? 'null' }};

        // Không sử dụng thời gian thực từ client, chỉ sử dụng dữ liệu từ server
        let serverElapsedSeconds = elapsedMinutesFromServer * 60;
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

        // Update UI từ dữ liệu server
        function renderFromServer() {
            // Elapsed time từ server
            document.getElementById('elapsedTimeDisplay').textContent = formatHMS(serverElapsedSeconds);

            // Remaining time and progress
            if (currentMode === 'combo') {
                const remainingSeconds = totalComboSeconds - serverElapsedSeconds;
                document.getElementById('remainingTimeDisplay').textContent = formatHM(Math.max(0, remainingSeconds));

                const percent = totalComboSeconds > 0 ? Math.min(100, (serverElapsedSeconds / totalComboSeconds) * 100) : 0;
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').textContent = Math.round(percent) + '% đã sử dụng';
            }

            // Current cost
            const currentCost = calculateCurrentCost(serverElapsedSeconds);
            document.getElementById('currentCostDisplay').textContent = formatCurrency(currentCost);

            // Update real-time banner
            updateRealTimeBanner(serverElapsedSeconds);
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

        // Real-time counter từ server data
        function startServerBasedCounter() {
            refreshInterval = setInterval(async () => {
                if (isRunning && !isPaused) {
                    // Tăng thời gian mỗi giây dựa trên dữ liệu server
                    serverElapsedSeconds += 1;
                    renderFromServer();
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
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        const final = data.final_amount;
                        ['totalAmountDisplay', 'finalAmountDisplay', 'billTotalAmount'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = formatCurrency(final);
                        });
                    }
                }).catch(console.error);
            @endif
        }

        // Search functionality
        function setupSearch() {
            const productSearch = document.getElementById('productSearch');
            const comboSearch = document.getElementById('comboSearch');

            if (productSearch) {
                productSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const productItems = document.querySelectorAll('#productsContent .product-item');

                    productItems.forEach(item => {
                        const name = item.getAttribute('data-name').toLowerCase();
                        if (name.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            if (comboSearch) {
                comboSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const comboItems = document.querySelectorAll('#combosContent .combo-item');

                    comboItems.forEach(item => {
                        const name = item.getAttribute('data-name').toLowerCase();
                        if (name.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        }

        // Get quantity from input
        function getQuantity(inputClass, itemId) {
            const input = document.querySelector(
                `${inputClass}[data-${inputClass.includes('product') ? 'product' : 'combo'}-id="${itemId}"]`);
            return input ? parseInt(input.value) || 1 : 1;
        }

        // Add product to bill
        function addProductToBill(productId, quantity = null) {
            @if ($table->currentBill)
                const finalQuantity = quantity || getQuantity('.product-quantity', productId);

                fetch('{{ route('bills.add-product', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: finalQuantity
                    })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra khi thêm sản phẩm');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thêm sản phẩm');
                });
            @endif
        }

        // Add combo to bill
        function addComboToBill(comboId, quantity = null) {
            @if ($table->currentBill)
                const finalQuantity = quantity || getQuantity('.combo-quantity', comboId);

                fetch('{{ route('bills.add-combo', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        combo_id: comboId,
                        quantity: finalQuantity
                    })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra khi thêm combo');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thêm combo');
                });
            @endif
        }

        // Add quick product (for new bill)
        function addQuickProduct(productId, quantity = null) {
            const finalQuantity = quantity || getQuantity('.quick-product-quantity', productId);

            fetch('{{ route('bills.quick-create') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    table_id: {{ $table->id }},
                    product_id: productId,
                    quantity: finalQuantity
                })
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra khi tạo bàn lẻ');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tạo bàn lẻ');
            });
        }

        // Event listeners for buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Product buttons
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addProductToBill(productId);
                });
            });

            // Combo buttons
            document.querySelectorAll('.add-combo-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const comboId = this.getAttribute('data-combo-id');
                    addComboToBill(comboId);
                });
            });

            // Quick product buttons
            document.querySelectorAll('.add-quick-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addQuickProduct(productId);
                });
            });

            // Render từ dữ liệu server ban đầu
            renderFromServer();

            // Start server-based counter
            if (isRunning && !isPaused) {
                startServerBasedCounter();
            }

            // Auto update bill total every 30 seconds
            setInterval(updateBillTotal, 30000);

            // Setup search functionality
            setupSearch();
        });

        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
@endsection
