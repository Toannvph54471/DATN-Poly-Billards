<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Bàn - {{ $table->table_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .table-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #475569;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: #e2e8f0;
            transform: translateX(-2px);
        }

        .table-details h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .table-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .table-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-occupied {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .status-paused {
            background: #dbeafe;
            color: #1e40af;
        }

        .hourly-rate {
            font-size: 0.875rem;
            color: #475569;
        }

        /* Main Content Styles */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .left-panel {
            width: 35%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 1.5rem;
            gap: 1.5rem;
        }

        .center-panel {
            width: 40%;
            background: white;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .right-panel {
            width: 25%;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Real-time Banner */
        .real-time-banner {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .time-counter {
            font-size: 1.5rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* Time Tracking */
        .time-tracking {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .time-box {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s;
        }

        .time-box:hover {
            transform: translateY(-2px);
        }

        .time-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .time-value {
            font-size: 1.25rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        .time-elapsed {
            color: #3b82f6;
        }

        .time-remaining {
            color: #10b981;
        }

        .time-cost {
            color: #f59e0b;
        }

        /* Progress Bar */
        .progress-container {
            margin-top: 1rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Products List */
        .products-list {
            flex: 1;
            overflow: auto;
        }

        .products-list table {
            min-width: 100%;
        }

        .products-list th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 10;
        }

        .quantity-btn {
            transition: all 0.2s;
        }

        .quantity-btn:hover:not(:disabled) {
            background: #3b82f6 !important;
            color: white;
            border-color: #3b82f6;
        }

        .add-btn:disabled {
            background: #cbd5e1 !important;
            cursor: not-allowed;
            transform: none;
        }

        .add-btn:disabled:hover {
            background: #cbd5e1 !important;
            transform: none;
        }

        /* Products & Combos Section */
        .products-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .products-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s;
        }

        .tab:hover {
            color: #3b82f6;
        }

        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .search-box {
            width: 100%;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
        }

        .search-box:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .products-container {
            flex: 1;
            overflow: auto;
        }

        /* Bill Details */
        .bill-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .bill-container {
            flex: 1;
            overflow: auto;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bill-table th {
            text-align: left;
            padding: 0.75rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            position: sticky;
            top: 0;
        }

        .bill-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .total-amount {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            color: #10b981;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        /* Right Panel Content */
        .right-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: auto;
            padding: 1.5rem;
        }

        .info-section {
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .action-btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-btn-primary {
            background: #3b82f6;
            color: white;
        }

        .action-btn-primary:hover {
            background: #2563eb;
        }

        .action-btn-success {
            background: #10b981;
            color: white;
        }

        .action-btn-success:hover {
            background: #059669;
        }

        .action-btn-warning {
            background: #f59e0b;
            color: white;
        }

        .action-btn-warning:hover {
            background: #d97706;
        }

        .action-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .action-btn-secondary:hover {
            background: #e2e8f0;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow: auto;
        }

        .modal-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animation for real-time banner */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .left-panel {
                width: 40%;
            }

            .center-panel {
                width: 35%;
            }

            .right-panel {
                width: 25%;
            }
        }

        @media (max-width: 1200px) {
            .main-content {
                flex-direction: column;
            }

            .left-panel,
            .center-panel,
            .right-panel {
                width: 100%;
                border: none;
            }

            .center-panel {
                order: 1;
                border-top: 1px solid #e2e8f0;
                border-bottom: 1px solid #e2e8f0;
            }

            .left-panel {
                order: 2;
            }

            .right-panel {
                order: 3;
            }
        }

        /* Empty State */
        .products-grid:empty::after {
            content: "Không có sản phẩm nào";
            display: block;
            text-align: center;
            padding: 3rem;
            color: #64748b;
            font-size: 1.1rem;
            grid-column: 1 / -1;
        }

        /* Warning Banner */
        .warning-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .warning-banner-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #92400e;
        }

        .warning-banner-content i {
            font-size: 1.25rem;
        }

        .warning-banner-text {
            flex: 1;
        }

        .warning-banner-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .warning-banner-description {
            font-size: 0.875rem;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Header -->
        <div class="header">
            <div class="table-info">
                <div class="table-title">
                    <a href="{{ route('admin.tables.index') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <div class="table-details">
                        <h1>{{ $table->table_name }}</h1>
                        <div class="table-meta">
                            <span>Số: {{ $table->table_number }}</span>
                            <span>•</span>
                            <span>{{ $table->category->name ?? 'Chưa phân loại' }}</span>
                        </div>
                    </div>
                </div>
                <div class="table-status">
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
                    <div class="status-badge {{ $statusClass }}">
                        {{ $statusText }}
                    </div>
                    <div class="hourly-rate">
                        Giá giờ: <strong>{{ number_format($table->getHourlyRate()) }} ₫/h</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Panel - Products & Combos -->
            <div class="left-panel">
                <!-- Time Tracking -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-clock text-blue-500"></i>
                            THEO DÕI THỜI GIAN
                        </h2>
                        <div>
                            @php
                                $modeText = match ($timeInfo['mode'] ?? 'none') {
                                    'regular' => '🕒 GIỜ THƯỜNG',
                                    'combo' => '🎁 COMBO TIME',
                                    'quick' => '⚡ BÀN LẺ',
                                    'combo_ended' => '⏹️ COMBO ĐÃ HẾT',
                                    default => '⏸️ KHÔNG HOẠT ĐỘNG',
                                };
                            @endphp
                            <span class="text-sm font-medium text-gray-600">{{ $modeText }}</span>
                        </div>
                    </div>

                    <div class="time-tracking">
                        <div class="time-box">
                            <div class="time-label">ĐÃ SỬ DỤNG</div>
                            <div id="elapsedTimeDisplay" class="time-value time-elapsed">
                                {{ isset($timeInfo['elapsed_minutes']) ? sprintf('%02d:%02d:%02d', floor($timeInfo['elapsed_minutes'] / 60), $timeInfo['elapsed_minutes'] % 60, 0) : '00:00:00' }}
                            </div>
                        </div>

                        <div class="time-box">
                            <div class="time-label">THỜI GIAN CÒN LẠI</div>
                            <div id="remainingTimeDisplay" class="time-value time-remaining">
                                @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && isset($timeInfo['remaining_minutes']))
                                    {{ sprintf('%02d:%02d', floor($timeInfo['remaining_minutes'] / 60), $timeInfo['remaining_minutes'] % 60) }}
                                @elseif (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo_ended')
                                    <span class="text-red-500">00:00</span>
                                @else
                                    --:--
                                @endif
                            </div>
                        </div>

                        <div class="time-box">
                            <div class="time-label">CHI PHÍ HIỆN TẠI</div>
                            <div id="currentCostDisplay" class="time-value time-cost">
                                {{ number_format(round($timeInfo['current_cost'] ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                        <div class="progress-container">
                            <div class="progress-header">
                                <span>TIẾN ĐỘ SỬ DỤNG COMBO</span>
                                <span id="progressText" class="font-bold">
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

                    <!-- Thông báo combo đã hết -->
                    @if (isset($timeInfo['needs_switch']) && $timeInfo['needs_switch'])
                        <div class="warning-banner">
                            <div class="warning-banner-content">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                <div class="warning-banner-text">
                                    <div class="warning-banner-title">Combo đã hết thời gian!</div>
                                    <div class="warning-banner-description">Vui lòng chuyển sang giờ thường để tiếp tục
                                        tính giờ.</div>
                                </div>
                                <form action="{{ route('admin.bills.switch-to-regular', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn-primary">
                                        <i class="fas fa-exchange-alt"></i>
                                        Chuyển
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Products & Combos Section -->
                <div class="card products-section">
                    <div class="products-tabs">
                        <div class="tab active" data-tab="products">
                            <i class="fas fa-utensils text-green-500"></i>
                            SẢN PHẨM ({{ $products->count() }})
                        </div>
                        <div class="tab" data-tab="combos">
                            <i class="fas fa-gift text-purple-500"></i>
                            COMBO ({{ $combos->count() }})
                        </div>
                    </div>

                    <input type="text" id="productSearch" placeholder="Tìm kiếm sản phẩm..." class="search-box">

                    <div class="products-container">
                        <!-- Products List -->
                        <div id="productsList" class="products-list">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="p-3 text-left text-sm font-medium text-gray-600">Sản phẩm</th>
                                        <th class="p-3 text-right text-sm font-medium text-gray-600 w-24">Giá</th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-32">Số lượng</th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-20">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                            alt="{{ $product->name }}"
                                                            class="w-10 h-10 rounded-lg object-cover">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $product->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2">
                                                            @if ($product->stock_quantity <= 0)
                                                                <span class="text-red-500 font-medium">Hết hàng</span>
                                                            @elseif($product->stock_quantity < 10)
                                                                <span class="text-orange-500 font-medium">Còn
                                                                    {{ $product->stock_quantity }}</span>
                                                            @else
                                                                <span class="text-green-500 font-medium">Còn
                                                                    {{ $product->stock_quantity }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="font-bold text-green-600">
                                                    {{ number_format($product->price) }} ₫</div>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button
                                                        class="quantity-btn minus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-product-id="{{ $product->id }}">-</button>
                                                    <input type="number" min="1"
                                                        max="{{ $product->stock_quantity }}" value="1"
                                                        class="quantity-input product-quantity w-12 text-center border rounded py-1"
                                                        data-product-id="{{ $product->id }}"
                                                        {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                    <button
                                                        class="quantity-btn plus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-product-id="{{ $product->id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button
                                                    class="add-btn add-product-btn bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors"
                                                    data-product-id="{{ $product->id }}"
                                                    {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus mr-1"></i>
                                                    Thêm
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Combos List -->
                        <div id="combosList" class="products-list" style="display: none;">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="p-3 text-left text-sm font-medium text-gray-600">Combo</th>
                                        <th class="p-3 text-right text-sm font-medium text-gray-600 w-24">Giá</th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-32">Số lượng
                                        </th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-20">Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($combos as $combo)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($combo->image)
                                                        <img src="{{ asset('storage/' . $combo->image) }}"
                                                            alt="{{ $combo->name }}"
                                                            class="w-10 h-10 rounded-lg object-cover">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                            <i class="fas fa-gift text-purple-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $combo->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2">
                                                            @if ($combo->is_time_combo)
                                                                <span
                                                                    class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-medium">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ $combo->play_duration_minutes }} phút
                                                                </span>
                                                            @endif
                                                            @if ($combo->actual_value > $combo->price)
                                                                <span class="text-green-600 font-medium">
                                                                    Tiết kiệm
                                                                    {{ number_format($combo->actual_value - $combo->price) }}
                                                                    ₫
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="font-bold text-green-600">
                                                    {{ number_format($combo->price) }} ₫</div>
                                                @if ($combo->actual_value > $combo->price)
                                                    <div class="text-sm text-gray-400 line-through">
                                                        {{ number_format($combo->actual_value) }} ₫</div>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button
                                                        class="quantity-btn minus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-combo-id="{{ $combo->id }}">-</button>
                                                    <input type="number" min="1" value="1"
                                                        class="quantity-input combo-quantity w-12 text-center border rounded py-1"
                                                        data-combo-id="{{ $combo->id }}">
                                                    <button
                                                        class="quantity-btn plus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-combo-id="{{ $combo->id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button
                                                    class="add-btn add-combo-btn bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors"
                                                    data-combo-id="{{ $combo->id }}"
                                                    {{ $table->currentBill && $table->currentBill->status === 'quick' ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus mr-1"></i>
                                                    Thêm
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Panel - Bill Details -->
            <div class="center-panel">
                <div class="right-content">
                    <!-- Bill Details -->
                    <div class="card bill-details">
                        <div class="card-header">
                            <h2 class="section-title">
                                <i class="fas fa-receipt text-gray-600"></i>
                                CHI TIẾT HÓA ĐƠN
                            </h2>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">TỔNG HÓA ĐƠN</div>
                                <div id="finalAmountDisplay" class="text-xl font-bold text-green-600">
                                    {{ number_format(round($table->currentBill->final_amount ?? 0)) }} ₫
                                </div>
                            </div>
                        </div>

                        <div class="bill-container">
                            @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                                <table class="bill-table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm/Dịch vụ</th>
                                            <th width="80">SL</th>
                                            <th width="120">Đơn giá</th>
                                            <th width="140">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table->currentBill->billDetails as $item)
                                            <tr>
                                                <td>
                                                    @if ($item->product_id && $item->product)
                                                        <i class="fas fa-utensils text-green-500 mr-2"></i>
                                                        {{ $item->product->name }}
                                                        @if ($item->is_combo_component)
                                                            <span class="text-xs text-gray-500">(Thành phần
                                                                combo)</span>
                                                        @endif
                                                    @elseif($item->combo_id && $item->combo)
                                                        <i class="fas fa-gift text-purple-500 mr-2"></i>
                                                        {{ $item->combo->name }}
                                                    @else
                                                        <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                                                        {{ $item->note ?? 'Dịch vụ khác' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-right">{{ number_format(round($item->unit_price)) }} ₫
                                                </td>
                                                <td class="text-right font-semibold">
                                                    {{ number_format(round($item->total_price)) }} ₫</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <p class="text-lg font-medium mb-2">Chưa có sản phẩm nào trong hóa đơn</p>
                                    <p class="text-sm">Thêm sản phẩm hoặc combo để bắt đầu</p>
                                </div>
                            @endif
                        </div>

                        @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                            <div class="total-amount">
                                Tổng cộng: {{ number_format(round($table->currentBill->final_amount)) }} ₫
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel - Table Info & Actions -->
            <div class="right-panel">
                <div class="right-content">
                    <!-- Table Info -->
                    <div class="card info-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            THÔNG TIN BÀN
                        </h2>

                        <div class="space-y-2">
                            <div class="info-item">
                                <span class="info-label">Tên bàn:</span>
                                <span class="info-value">{{ $table->table_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Số bàn:</span>
                                <span class="info-value">{{ $table->table_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Giá giờ:</span>
                                <span class="info-value text-green-600">{{ number_format($table->getHourlyRate()) }}
                                    ₫/h</span>
                            </div>

                            @if ($table->currentBill)
                                <div class="border-t border-gray-200 pt-3 mt-2">
                                    <div class="info-item">
                                        <span class="info-label">Mã bill:</span>
                                        <span class="info-value">{{ $table->currentBill->bill_number }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Nhân viên:</span>
                                        <span
                                            class="info-value">{{ $table->currentBill->staff->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Thời gian mở:</span>
                                        <span class="info-value text-sm">
                                            {{ $table->currentBill->start_time ? $table->currentBill->start_time->format('H:i d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-3 mt-2">
                                    <div class="info-item">
                                        <span class="info-label">Tổng hiện tại:</span>
                                        <span class="info-value text-green-600 font-bold">
                                            {{ number_format(round($table->currentBill->final_amount)) }} ₫
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card info-section">
                        <h2 class="section-title">
                            <i class="fas fa-bolt text-amber-500"></i>
                            THAO TÁC NHANH
                        </h2>

                        <div class="action-buttons">
                            @if ($table->currentBill)
                                <!-- Xử lý bàn lẻ -->
                                @if ($table->currentBill->status === 'quick')
                                    <form action="{{ route('bills.start-playing', $table->currentBill->id) }}"
                                        method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-primary">
                                            <i class="fas fa-play"></i>
                                            BẮT ĐẦU TÍNH GIỜ
                                        </button>
                                    </form>

                                    <!-- NÚT THANH TOÁN CHO BÀN LẺ -->
                                    <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                        class="action-btn action-btn-success">
                                        <i class="fas fa-credit-card"></i>
                                        THANH TOÁN BÀN LẺ
                                    </a>
                                @else
                                    <!-- Pause/Resume Buttons - CHỈ HIỆN VỚI COMBO TIME -->
                                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                                        @if (isset($timeInfo['is_running']) && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                                            <form action="{{ route('bills.pause', $table->currentBill->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn-warning">
                                                    <i class="fas fa-pause"></i>
                                                    TẠM DỪNG COMBO
                                                </button>
                                            </form>
                                        @endif

                                        @if (isset($timeInfo['is_paused']) && $timeInfo['is_paused'])
                                            <form action="{{ route('bills.resume', $table->currentBill->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn-success">
                                                    <i class="fas fa-play"></i>
                                                    TIẾP TỤC COMBO
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <!-- Thanh toán -->
                                    <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                        class="action-btn action-btn-primary">
                                        <i class="fas fa-credit-card"></i>
                                        THANH TOÁN
                                    </a>

                                    <!-- Cập nhật tổng -->
                                    <form action="{{ route('bills.update-total', $table->currentBill->id) }}"
                                        method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary">
                                            <i class="fas fa-sync-alt"></i>
                                            CẬP NHẬT TỔNG
                                        </button>
                                    </form>

                                    <!-- Chuyển sang giờ thường - CHỈ HIỆN KHI COMBO ĐÃ HẾT -->
                                    @if (isset($timeInfo['needs_switch']) && $timeInfo['needs_switch'])
                                        <form
                                            action="{{ route('admin.bills.switch-to-regular', $table->currentBill->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn chuyển sang tính giờ thường?')"
                                            class="w-full">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-success">
                                                <i class="fas fa-exchange-alt"></i>
                                                CHUYỂN GIỜ THƯỜNG
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Chuyển bàn -->
                                    <a href="{{ route('admin.bills.transfer.form', $table->currentBill->id) }}"
                                        class="action-btn action-btn-secondary">
                                        <i class="fas fa-exchange-alt"></i>
                                        CHUYỂN BÀN
                                    </a>
                                @endif

                                
                            @else
                                <!-- Tạo bill mới -->
                                <button onclick="showCreateBillModal()" class="action-btn action-btn-primary">
                                    <i class="fas fa-plus"></i>
                                    TẠO HÓA ĐƠN TÍNH GIỜ
                                </button>

                                <!-- Tạo bàn lẻ -->
                                <button onclick="showQuickBillModal()" class="action-btn action-btn-warning">
                                    <i class="fas fa-bolt"></i>
                                    TẠO BÀN LẺ
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if ($table->currentBill && $table->currentBill->user)
                        <div class="card info-section">
                            <h2 class="section-title">
                                <i class="fas fa-user text-purple-500"></i>
                                THÔNG TIN KHÁCH HÀNG
                            </h2>

                            <div class="space-y-2">
                                <div class="info-item">
                                    <span class="info-label">Tên khách hàng</span>
                                    <span class="info-value">{{ $table->currentBill->user->name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Số điện thoại</span>
                                    <span class="info-value">{{ $table->currentBill->user->phone }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Loại khách</span>
                                    <span class="info-value">
                                        @php
                                            $customerType = $table->currentBill->user->customer_type ?? 'Mới';
                                            $typeClass = match ($customerType) {
                                                'VIP' => 'text-red-600 font-bold',
                                                'Thân thiết' => 'text-purple-600 font-semibold',
                                                'Quay lại' => 'text-blue-600',
                                                default => 'text-gray-600',
                                            };
                                        @endphp
                                        <span class="{{ $typeClass }}">{{ $customerType }}</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Số lần đến</span>
                                    <span class="info-value font-bold text-green-600">
                                        {{ $table->currentBill->user->total_visits ?? 1 }} lần
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tổng chi tiêu</span>
                                    <span class="info-value font-bold text-orange-600">
                                        {{ number_format($table->currentBill->user->total_spent ?? 0) }} ₫
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Bill Modal -->
    <div id="createBillModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tạo Hóa Đơn Tính Giờ</h3>
                <button class="close-btn" onclick="hideCreateBillModal()">&times;</button>
            </div>
            <form id="createBillForm" action="{{ route('bills.create') }}" method="POST">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id }}">

                <div class="form-group">
                    <label class="form-label">Số điện thoại khách hàng</label>
                    <input type="text" name="user_phone" class="form-input" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" name="user_name" class="form-input" placeholder="Nhập tên khách hàng">
                </div>

                <div class="form-group">
                    <label class="form-label">Số lượng khách</label>
                    <input type="number" name="guest_count" class="form-input" value="1" min="1"
                        required>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideCreateBillModal()"
                        class="action-btn action-btn-secondary flex-1">
                        Hủy
                    </button>
                    <button type="submit" class="action-btn action-btn-primary flex-1">
                        <i class="fas fa-plus"></i> Tạo Hóa Đơn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Bill Modal -->
    <div id="quickBillModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tạo Bàn Lẻ</h3>
                <button class="close-btn" onclick="hideQuickBillModal()">&times;</button>
            </div>
            <form id="quickBillForm" action="{{ route('bills.quick-create') }}" method="POST">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id }}">

                <div class="form-group">
                    <label class="form-label">Số điện thoại khách hàng (tùy chọn)</label>
                    <input type="text" name="user_phone" class="form-input" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Tên khách hàng (tùy chọn)</label>
                    <input type="text" name="user_name" class="form-input" placeholder="Nhập tên khách hàng">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideQuickBillModal()"
                        class="action-btn action-btn-secondary flex-1">
                        Hủy
                    </button>
                    <button type="submit" class="action-btn action-btn-warning flex-1">
                        <i class="fas fa-bolt"></i> Tạo Bàn Lẻ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Server data với giá trị mặc định
        const isRunning = {{ isset($timeInfo['is_running']) && $timeInfo['is_running'] ? 'true' : 'false' }};
        const isPaused = {{ isset($timeInfo['is_paused']) && $timeInfo['is_paused'] ? 'true' : 'false' }};
        const currentMode = '{{ $timeInfo['mode'] ?? 'none' }}';
        const hourlyRate = Number({{ $timeInfo['hourly_rate'] ?? 0 }});
        const totalComboMinutes = Number({{ $timeInfo['total_minutes'] ?? 0 }});
        const elapsedMinutesFromServer = Number({{ $timeInfo['elapsed_minutes'] ?? 0 }});
        const currentBillId = {{ $table->currentBill->id ?? 'null' }};
        const needsSwitch = {{ isset($timeInfo['needs_switch']) && $timeInfo['needs_switch'] ? 'true' : 'false' }};

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
                const totalComboSeconds = totalComboMinutes * 60;
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
                const totalComboSeconds = totalComboMinutes * 60;
                const remainingSeconds = totalComboSeconds - serverElapsedSeconds;
                document.getElementById('remainingTimeDisplay').textContent = formatHM(Math.max(0, remainingSeconds));

                const percent = totalComboSeconds > 0 ? Math.min(100, (serverElapsedSeconds / totalComboSeconds) * 100) : 0;
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').textContent = Math.round(percent) + '% đã sử dụng';
            } else if (currentMode === 'combo_ended') {
                document.getElementById('remainingTimeDisplay').innerHTML = '<span class="text-red-500">00:00</span>';
            }

            // Current cost
            const currentCost = calculateCurrentCost(serverElapsedSeconds);
            document.getElementById('currentCostDisplay').textContent = formatCurrency(currentCost);
        }

        // Real-time counter từ server data
        function startServerBasedCounter() {
            refreshInterval = setInterval(async () => {
                if (isRunning && !isPaused && currentMode !== 'combo_ended') {
                    // Tăng thời gian mỗi giây dựa trên dữ liệu server
                    serverElapsedSeconds += 1;
                    renderFromServer();
                }
            }, 1000);
        }

        // Modal functions
        function showCreateBillModal() {
            document.getElementById('createBillModal').style.display = 'flex';
        }

        function hideCreateBillModal() {
            document.getElementById('createBillModal').style.display = 'none';
        }

        function showQuickBillModal() {
            document.getElementById('quickBillModal').style.display = 'flex';
        }

        function hideQuickBillModal() {
            document.getElementById('quickBillModal').style.display = 'none';
        }

        // Tab functionality
        function setupTabs() {
            const tabs = document.querySelectorAll('.tab');
            const productsList = document.getElementById('productsList');
            const combosList = document.getElementById('combosList');
            const searchBox = document.getElementById('productSearch');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));

                    // Add active class to clicked tab
                    tab.classList.add('active');

                    // Show/hide lists
                    const tabName = tab.getAttribute('data-tab');
                    if (tabName === 'products') {
                        productsList.style.display = 'block';
                        combosList.style.display = 'none';
                        searchBox.placeholder = 'Tìm kiếm sản phẩm...';
                    } else {
                        productsList.style.display = 'none';
                        combosList.style.display = 'block';
                        searchBox.placeholder = 'Tìm kiếm combo...';
                    }

                    // Reset search
                    searchBox.value = '';
                    filterProducts(searchBox.value);
                });
            });
        }

        // Search functionality
        function setupSearch() {
            const searchBox = document.getElementById('productSearch');

            searchBox.addEventListener('input', function() {
                filterProducts(this.value);
            });
        }

        function filterProducts(searchTerm) {
            const activeTab = document.querySelector('.tab.active').getAttribute('data-tab');
            const list = activeTab === 'products' ?
                document.getElementById('productsList') :
                document.getElementById('combosList');

            const rows = list.querySelectorAll('tbody tr');
            const term = searchTerm.toLowerCase();

            rows.forEach(row => {
                const name = row.querySelector('.font-medium').textContent.toLowerCase();
                if (name.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Quantity controls functionality
        function setupQuantityControls() {
            // Plus buttons
            document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const comboId = this.getAttribute('data-combo-id');
                    const input = productId ?
                        document.querySelector(`.product-quantity[data-product-id="${productId}"]`) :
                        document.querySelector(`.combo-quantity[data-combo-id="${comboId}"]`);

                    if (input) {
                        const max = parseInt(input.getAttribute('max')) || 999;
                        const currentValue = parseInt(input.value) || 1;
                        if (currentValue < max) {
                            input.value = currentValue + 1;
                        }
                    }
                });
            });

            // Minus buttons
            document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const comboId = this.getAttribute('data-combo-id');
                    const input = productId ?
                        document.querySelector(`.product-quantity[data-product-id="${productId}"]`) :
                        document.querySelector(`.combo-quantity[data-combo-id="${comboId}"]`);

                    if (input) {
                        const currentValue = parseInt(input.value) || 1;
                        if (currentValue > 1) {
                            input.value = currentValue - 1;
                        }
                    }
                });
            });

            // Input validation
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const min = parseInt(this.getAttribute('min')) || 1;
                    const max = parseInt(this.getAttribute('max')) || 999;
                    let value = parseInt(this.value) || min;

                    if (value < min) value = min;
                    if (value > max) value = max;

                    this.value = value;
                });
            });
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
                const button = event.target;
                const originalText = button.innerHTML;

                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

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
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thêm sản phẩm');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            @else
                alert('Vui lòng tạo hóa đơn trước khi thêm sản phẩm');
            @endif
        }

        // Add combo to bill
        function addComboToBill(comboId, quantity = null) {
            @if ($table->currentBill)
                const finalQuantity = quantity || getQuantity('.combo-quantity', comboId);
                const button = event.target;
                const originalText = button.innerHTML;

                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

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
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thêm combo');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            @else
                alert('Vui lòng tạo hóa đơn trước khi thêm combo');
            @endif
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

            // Render từ dữ liệu server ban đầu
            renderFromServer();

            // Start server-based counter
            if (isRunning && !isPaused && currentMode !== 'combo_ended') {
                startServerBasedCounter();
            }

            // Setup tabs and search functionality
            setupTabs();
            setupSearch();
            setupQuantityControls();

            // Close modals when clicking outside
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });
        });

        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>

</html>
