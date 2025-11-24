<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Main Content Styles */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .left-panel {
            width: 40%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 1.5rem;
            gap: 1.5rem;
        }

        .center-panel {
            width: 30%;
            background: white;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .right-panel {
            width: 30%;
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

        /* Bill Items */
        .bill-items {
            flex: 1;
            overflow: auto;
        }

        .bill-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .bill-item:hover {
            background: #f8fafc;
        }

        .bill-item-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .bill-item-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bill-item-time {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .bill-item-combo {
            background: #ede9fe;
            color: #7c3aed;
        }

        .bill-item-product {
            background: #dcfce7;
            color: #166534;
        }

        .bill-item-extra {
            background: #fef3c7;
            color: #92400e;
        }

        .bill-item-details {
            flex: 1;
        }

        .bill-item-name {
            font-weight: 600;
            color: #1e293b;
        }

        .bill-item-meta {
            font-size: 0.75rem;
            color: #64748b;
        }

        .bill-item-price {
            text-align: right;
        }

        .bill-item-quantity {
            font-size: 0.875rem;
            color: #64748b;
        }

        .bill-item-total {
            font-weight: 700;
            color: #1e293b;
        }

        /* Payment Methods */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method:hover {
            border-color: #cbd5e1;
        }

        .payment-method.selected {
            border-color: var(--primary);
            background: #eff6ff;
        }

        .payment-method-radio {
            margin-right: 1rem;
        }

        .payment-method-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .payment-method-cash {
            background: #dcfce7;
            color: #166534;
        }

        .payment-method-bank {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .payment-method-card {
            background: #ede9fe;
            color: #7c3aed;
        }

        .payment-method-info {
            flex: 1;
        }

        .payment-method-name {
            font-weight: 600;
            color: #1e293b;
        }

        .payment-method-desc {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Payment Details */
        .payment-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .payment-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }

        .payment-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .payment-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .payment-input.readonly {
            background: #f9fafb;
            color: #6b7280;
        }

        .payment-input.success {
            border-color: var(--success);
            background: #f0fdf4;
            color: var(--success);
            font-weight: 600;
        }

        .payment-input.warning {
            border-color: var(--warning);
            background: #fefce8;
            color: var(--warning);
        }

        .payment-input.error {
            border-color: var(--danger);
            background: #fef2f2;
            color: var(--danger);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1.5rem;
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
            background: var(--primary);
            color: white;
        }

        .action-btn-primary:hover {
            background: #2563eb;
        }

        .action-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .action-btn-secondary:hover {
            background: #e2e8f0;
        }

        /* Customer Info */
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .customer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .customer-label {
            font-weight: 500;
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .customer-value {
            font-weight: 600;
            color: #1e293b;
        }

        .customer-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #ede9fe;
            color: #7c3aed;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* QR Code Section */
        .qr-section {
            display: none;
            text-align: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .qr-section.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        .qr-code-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            display: inline-block;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            background: #f3f4f6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .qr-instructions {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .bank-info {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: left;
        }

        .bank-info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .bank-info-label {
            color: #64748b;
            font-weight: 500;
        }

        .bank-info-value {
            font-weight: 600;
            color: #1e293b;
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 350px;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(100%);
            opacity: 0;
        }

        .toast-success {
            background-color: var(--success);
        }

        .toast-error {
            background-color: var(--danger);
        }

        .toast-warning {
            background-color: var(--warning);
        }

        .toast-info {
            background-color: var(--primary);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
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

        /* Mobile Styles */
        @media (max-width: 1024px) {
            .main-content {
                flex-direction: column;
            }

            .left-panel,
            .center-panel,
            .right-panel {
                width: 100%;
                height: auto;
                border: none;
            }

            .panel {
                display: none;
            }

            .panel.active {
                display: flex;
            }

            .mobile-panel-tabs {
                display: flex;
            }

            .header {
                padding: 1rem;
            }

            .table-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .qr-code {
                width: 150px;
                height: 150px;
            }
        }

        @media (max-width: 768px) {
            .table-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .back-btn {
                align-self: flex-start;
            }

            .table-details h1 {
                font-size: 1.25rem;
            }

            .table-meta {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .payment-method {
                padding: 0.75rem;
            }

            .payment-method-icon {
                width: 2rem;
                height: 2rem;
                margin-right: 0.75rem;
            }

            .qr-code {
                width: 120px;
                height: 120px;
            }
        }

        /* Mobile Panel Tabs */
        .mobile-panel-tabs {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            z-index: 1000;
        }

        .mobile-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.75rem;
            color: #64748b;
            transition: all 0.2s;
        }

        .mobile-tab.active {
            color: var(--primary);
            background: #eff6ff;
        }

        .mobile-tab i {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 4px;
        }

        /* Utility classes */
        .mobile-only {
            display: none;
        }

        .desktop-only {
            display: block;
        }

        @media (max-width: 1024px) {
            .mobile-only {
                display: block;
            }

            .desktop-only {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>
        
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="table-info">
                <div class="table-title">
                    <a href="{{ route('admin.tables.detail', $bill->table_id) }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span class="desktop-only">Quay lại bàn</span>
                    </a>
                    <div class="table-details">
                        <h1>Thanh Toán - {{ $bill->bill_number }}</h1>
                        <div class="table-meta">
                            <span>Bàn: {{ $bill->table->table_name }}</span>
                            <span class="desktop-only">•</span>
                            <span>Tổng: {{ number_format(ceil($bill->final_amount / 1000) * 1000) }} ₫</span>
                        </div>
                    </div>
                </div>
                <div class="table-status">
                    <div class="status-badge status-occupied">
                        🔴 ĐANG THANH TOÁN
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Panel - Bill Details -->
            <div class="left-panel panel active" id="billPanel">
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-receipt text-blue-500"></i>
                            CHI TIẾT HÓA ĐƠN
                        </h2>
                    </div>

                    <div class="bill-items">
                        @php
                            $finalAmount = ceil($bill->final_amount / 1000) * 1000;
                            $roundedTimeCost = ceil($timeCost / 1000) * 1000;
                        @endphp

                        <!-- Time Usage -->
                        @if ($roundedTimeCost > 0)
                            <div class="bill-item fade-in">
                                <div class="bill-item-content">
                                    <div class="bill-item-icon bill-item-time">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="bill-item-details">
                                        <div class="bill-item-name">Giờ chơi</div>
                                        <div class="bill-item-meta">
                                            @php
                                                $totalMinutes = $timeDetails['total_minutes'] ?? 0;
                                                $hourlyRate = $timeDetails['hourly_rate'] ?? 0;
                                            @endphp
                                            {{ $totalMinutes }} phút @ {{ number_format(ceil($hourlyRate / 1000) * 1000) }}₫/giờ
                                        </div>
                                    </div>
                                </div>
                                <div class="bill-item-price">
                                    <div class="bill-item-total">{{ number_format($roundedTimeCost) }} ₫</div>
                                </div>
                            </div>
                        @endif

                        <!-- Combos -->
                        @foreach ($bill->billDetails->where('combo_id', '!=', null)->where('is_combo_component', false) as $comboDetail)
                            @php
                                $roundedComboPrice = ceil($comboDetail->unit_price / 1000) * 1000;
                                $roundedComboTotal = ceil($comboDetail->total_price / 1000) * 1000;
                            @endphp
                            <div class="bill-item fade-in">
                                <div class="bill-item-content">
                                    <div class="bill-item-icon bill-item-combo">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                    <div class="bill-item-details">
                                        <div class="bill-item-name">{{ $comboDetail->combo->name ?? 'Combo' }}</div>
                                        <div class="bill-item-meta">
                                            @if ($comboDetail->combo)
                                                @php
                                                    $components = $bill->billDetails->where(
                                                        'parent_bill_detail_id',
                                                        $comboDetail->id,
                                                    );
                                                @endphp
                                                @if ($components->count() > 0)
                                                    @foreach ($components as $component)
                                                        {{ $component->quantity }}x {{ $component->product->name ?? 'Sản phẩm' }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ $comboDetail->combo->description ?? 'Combo' }}
                                                @endif
                                            @else
                                                Combo đã bị xóa
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="bill-item-price">
                                    <div class="bill-item-quantity">{{ $comboDetail->quantity }} x {{ number_format($roundedComboPrice) }} ₫</div>
                                    <div class="bill-item-total">{{ number_format($roundedComboTotal) }} ₫</div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Individual Products -->
                        @foreach ($bill->billDetails->whereNull('combo_id')->where('is_combo_component', false) as $item)
                            @if ($item->product)
                                @php
                                    $roundedUnitPrice = ceil($item->unit_price / 1000) * 1000;
                                    $roundedItemTotal = ceil($item->total_price / 1000) * 1000;
                                @endphp
                                <div class="bill-item fade-in">
                                    <div class="bill-item-content">
                                        <div class="bill-item-icon bill-item-product">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <div class="bill-item-details">
                                            <div class="bill-item-name">{{ $item->product->name }}</div>
                                            <div class="bill-item-meta">Đơn giá: {{ number_format($roundedUnitPrice) }} ₫</div>
                                        </div>
                                    </div>
                                    <div class="bill-item-price">
                                        <div class="bill-item-quantity">{{ $item->quantity }} x {{ number_format($roundedUnitPrice) }} ₫</div>
                                        <div class="bill-item-total">{{ number_format($roundedItemTotal) }} ₫</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <!-- Extra Charges -->
                        @foreach ($bill->billDetails->whereNull('product_id')->whereNull('combo_id')->where('is_combo_component', false) as $extra)
                            @php
                                $roundedExtraPrice = ceil($extra->unit_price / 1000) * 1000;
                                $roundedExtraTotal = ceil($extra->total_price / 1000) * 1000;
                            @endphp
                            <div class="bill-item fade-in">
                                <div class="bill-item-content">
                                    <div class="bill-item-icon bill-item-extra">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div class="bill-item-details">
                                        <div class="bill-item-name">{{ $extra->note ?? 'Phí phát sinh' }}</div>
                                        <div class="bill-item-meta">Phí phát sinh</div>
                                    </div>
                                </div>
                                <div class="bill-item-price">
                                    <div class="bill-item-quantity">{{ $extra->quantity }} x {{ number_format($roundedExtraPrice) }} ₫</div>
                                    <div class="bill-item-total">{{ number_format($roundedExtraTotal) }} ₫</div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Total Amount -->
                        <div class="bill-item" style="border-top: 2px solid #e2e8f0; border-bottom: none; background: #f8fafc;">
                            <div class="bill-item-content">
                                <div class="bill-item-details">
                                    <div class="bill-item-name" style="font-size: 1.125rem;">TỔNG CỘNG</div>
                                </div>
                            </div>
                            <div class="bill-item-price">
                                <div class="bill-item-total" style="font-size: 1.25rem; color: var(--success);">{{ number_format($finalAmount) }} ₫</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Panel - Customer Info -->
            <div class="center-panel panel" id="customerPanel">
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-user text-purple-500"></i>
                            THÔNG TIN KHÁCH HÀNG
                        </h2>
                    </div>

                    @if ($bill->user)
                        <div class="customer-info">
                            <div class="customer-row fade-in">
                                <div class="customer-label">
                                    <i class="fas fa-user-circle text-blue-500"></i>
                                    Tên khách hàng
                                </div>
                                <div class="customer-value">{{ $bill->user->name }}</div>
                            </div>

                            <div class="customer-row fade-in">
                                <div class="customer-label">
                                    <i class="fas fa-phone text-green-500"></i>
                                    Điện thoại
                                </div>
                                <div class="customer-value">{{ $bill->user->phone }}</div>
                            </div>

                            <div class="customer-row fade-in">
                                <div class="customer-label">
                                    <i class="fas fa-tag text-purple-500"></i>
                                    Loại khách
                                </div>
                                <div class="customer-value">
                                    <span class="customer-badge">{{ $bill->user->customer_type ?? 'Khách mới' }}</span>
                                </div>
                            </div>

                            <div class="customer-row fade-in" style="border-bottom: none;">
                                <div class="customer-label">
                                    <i class="fas fa-history text-orange-500"></i>
                                    Số lần đến
                                </div>
                                <div class="customer-value">{{ $bill->user->total_visits ?? 0 }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state" style="text-align: center; padding: 2rem; color: #64748b;">
                            <i class="fas fa-user-slash" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                            <p class="text-lg font-medium mb-2">Không có thông tin khách hàng</p>
                            <p class="text-sm">Hóa đơn này không có thông tin khách hàng</p>
                        </div>
                    @endif
                </div>

                <!-- QR Code Section (for bank transfer) -->
                <div class="qr-section" id="qrSection">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-center">
                        <i class="fas fa-qrcode text-blue-500 mr-2"></i>
                        QUÉT MÃ ĐỂ THANH TOÁN
                    </h3>
                    
                    <div class="qr-code-container">
                        <div class="qr-code" id="qrCodePlaceholder">
                            <!-- Placeholder for QR code - bạn có thể thay thế bằng mã QR thực tế -->
                            <div style="text-align: center;">
                                <div style="margin-bottom: 0.5rem;">
                                    <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                                </div>
                                <div>Mã QR sẽ được hiển thị ở đây</div>
                            </div>
                        </div>
                    </div>

                    <div class="qr-instructions">
                        <p>Quét mã QR bằng ứng dụng ngân hàng của bạn để thanh toán</p>
                    </div>

                    <div class="bank-info">
                        <div class="bank-info-item">
                            <span class="bank-info-label">Số tiền:</span>
                            <span class="bank-info-value">{{ number_format($finalAmount) }} ₫</span>
                        </div>
                        <div class="bank-info-item">
                            <span class="bank-info-label">Nội dung:</span>
                            <span class="bank-info-value">{{ $bill->bill_number }}</span>
                        </div>
                        <div class="bank-info-item">
                            <span class="bank-info-label">Ngân hàng:</span>
                            <span class="bank-info-value">Vietcombank</span>
                        </div>
                        <div class="bank-info-item" style="border-bottom: none;">
                            <span class="bank-info-label">Số tài khoản:</span>
                            <span class="bank-info-value">0123456789</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Payment Methods -->
            <div class="right-panel panel" id="paymentPanel">
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-credit-card text-green-500"></i>
                            PHƯƠNG THỨC THANH TOÁN
                        </h2>
                    </div>

                    <form action="{{ route('admin.bills.process-payment', $bill->id) }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <div class="payment-method selected" data-method="cash">
                                <div class="payment-method-radio">
                                    <input type="radio" name="payment_method" value="cash" id="cash" checked>
                                </div>
                                <div class="payment-method-icon payment-method-cash">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Tiền mặt</div>
                                    <div class="payment-method-desc">Thanh toán bằng tiền mặt</div>
                                </div>
                            </div>

                            <div class="payment-method" data-method="bank">
                                <div class="payment-method-radio">
                                    <input type="radio" name="payment_method" value="bank" id="bank">
                                </div>
                                <div class="payment-method-icon payment-method-bank">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Chuyển khoản</div>
                                    <div class="payment-method-desc">Chuyển khoản ngân hàng</div>
                                </div>
                            </div>

                            <div class="payment-method" data-method="card">
                                <div class="payment-method-radio">
                                    <input type="radio" name="payment_method" value="card" id="card">
                                </div>
                                <div class="payment-method-icon payment-method-card">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Thẻ</div>
                                    <div class="payment-method-desc">Thẻ ATM/Visa/Mastercard</div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="payment-details">
                            <!-- Amount -->
                            <div class="payment-input-group">
                                <label class="payment-label">Số tiền thanh toán</label>
                                <input type="number" name="amount" value="{{ $finalAmount }}" 
                                       class="payment-input readonly" readonly>
                            </div>

                            <!-- Cash Received (only for cash) -->
                            <div id="cashAmountSection" class="payment-input-group">
                                <label class="payment-label">Khách đưa</label>
                                <input type="number" id="cash_received" name="cash_received" 
                                       value="{{ $finalAmount }}" min="{{ $finalAmount }}" step="1000"
                                       class="payment-input" oninput="calculateChange()">
                            </div>

                            <!-- Change Amount (only for cash) -->
                            <div id="changeAmountSection" class="payment-input-group">
                                <label class="payment-label">Tiền thối lại</label>
                                <input type="number" id="change_amount" name="change_amount" value="0" 
                                       class="payment-input success" readonly>
                            </div>

                            <!-- Note -->
                            <div class="payment-input-group">
                                <label class="payment-label">Ghi chú</label>
                                <textarea name="note" rows="3" 
                                          class="payment-input" 
                                          placeholder="Nhập ghi chú cho hóa đơn..."></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="action-btn action-btn-primary">
                                <i class="fas fa-check-circle"></i>
                                XÁC NHẬN THANH TOÁN
                            </button>

                            <a href="{{ route('admin.tables.detail', $bill->table_id) }}" class="action-btn action-btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                QUAY LẠI
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Panel Tabs -->
        <div class="mobile-panel-tabs">
            <button class="mobile-tab active" data-panel="billPanel">
                <i class="fas fa-receipt"></i>
                <span>Hóa đơn</span>
            </button>
            <button class="mobile-tab" data-panel="customerPanel">
                <i class="fas fa-user"></i>
                <span>Khách hàng</span>
            </button>
            <button class="mobile-tab" data-panel="paymentPanel">
                <i class="fas fa-credit-card"></i>
                <span>Thanh toán</span>
            </button>
        </div>
    </div>

    <script>
        const totalAmount = {{ $finalAmount }};

        // Toast Notification System
        function showToast(message, type = 'info', duration = 5000) {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            // Set icon based on type
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            if (type === 'warning') icon = 'exclamation-triangle';
            
            toast.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            `;
            
            toastContainer.appendChild(toast);
            
            // Show toast with animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Auto hide after duration
            setTimeout(() => {
                hideToast(toast);
            }, duration);
            
            // Click to dismiss
            toast.addEventListener('click', () => {
                hideToast(toast);
            });
        }
        
        function hideToast(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }

        // Loading Overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Mobile panel navigation
        function setupMobilePanels() {
            const mobileTabs = document.querySelectorAll('.mobile-tab');
            const panels = document.querySelectorAll('.panel');

            mobileTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const panelId = this.getAttribute('data-panel');
                    
                    // Remove active class from all tabs and panels
                    mobileTabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding panel
                    this.classList.add('active');
                    document.getElementById(panelId).classList.add('active');
                    
                    // Add animation effect
                    document.getElementById(panelId).classList.add('fade-in');
                    setTimeout(() => {
                        document.getElementById(panelId).classList.remove('fade-in');
                    }, 500);
                });
            });
        }

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove all selections
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                    m.querySelector('input[type="radio"]').checked = false;
                });

                // Select this method
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;

                // Show/hide cash sections
                const selectedMethod = this.dataset.method;
                const cashSection = document.getElementById('cashAmountSection');
                const changeSection = document.getElementById('changeAmountSection');
                const qrSection = document.getElementById('qrSection');

                if (selectedMethod === 'cash') {
                    cashSection.style.display = 'flex';
                    changeSection.style.display = 'flex';
                    qrSection.classList.remove('active');
                    calculateChange();
                } else if (selectedMethod === 'bank') {
                    cashSection.style.display = 'none';
                    changeSection.style.display = 'none';
                    qrSection.classList.add('active');
                } else {
                    cashSection.style.display = 'none';
                    changeSection.style.display = 'none';
                    qrSection.classList.remove('active');
                }
            });
        });

        // Calculate change
        function calculateChange() {
            const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;
            const changeAmount = Math.max(0, cashReceived - totalAmount);
            const roundedChange = Math.ceil(changeAmount / 1000) * 1000;

            const changeInput = document.getElementById('change_amount');
            changeInput.value = roundedChange;

            // Update styling based on change amount
            if (cashReceived < totalAmount) {
                changeInput.classList.remove('success');
                changeInput.classList.add('error');
            } else {
                changeInput.classList.remove('error');
                changeInput.classList.add('success');
            }
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;

            if (paymentMethod === 'cash' && cashReceived < totalAmount) {
                e.preventDefault();
                showToast('Số tiền khách đưa không đủ! Vui lòng nhập số tiền lớn hơn hoặc bằng ' + totalAmount.toLocaleString('vi-VN') + ' ₫', 'error', 5000);
                return false;
            }

            if (!confirm('Xác nhận thanh toán hóa đơn ' + totalAmount.toLocaleString('vi-VN') + ' ₫?')) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG XỬ LÝ...';
            submitBtn.disabled = true;

            // Re-enable button after 5 seconds if still on page (fallback)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupMobilePanels();
            document.querySelector('.payment-method[data-method="cash"]').click();
            document.getElementById('cash_received').value = totalAmount;
            calculateChange();

            // Add animation to page load
            document.querySelectorAll('.card').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
</body>

</html>