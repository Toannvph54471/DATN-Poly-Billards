<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #374151;
            line-height: 1.5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bill-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .bill-meta {
            display: flex;
            gap: 12px;
            color: #6b7280;
            font-size: 14px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: #059669;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 300px 1fr 400px;
            gap: 20px;
            align-items: start;
        }

        .column {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .column-header {
            background: #f8fafc;
            color: #374151;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .column-header i {
            font-size: 18px;
            color: #6b7280;
        }

        .column-header h2 {
            font-size: 16px;
            font-weight: 600;
        }

        .column-content {
            padding: 16px;
        }

        /* Customer Info Styles */
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 6px;
            border-left: 3px solid #6b7280;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-value {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            background: #e5e7eb;
            color: #374151;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .no-customer {
            text-align: center;
            padding: 30px 16px;
            color: #6b7280;
        }

        .no-customer i {
            font-size: 32px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        /* Bill Items Styles */
        .bill-items {
            space-y: 8px;
        }

        .bill-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        }

        .bill-item:hover {
            background-color: #f9fafb;
        }

        .bill-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            color: #111827;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .item-details {
            font-size: 12px;
            color: #6b7280;
        }

        .item-price {
            text-align: right;
        }

        .item-quantity {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .item-total {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        .total-row {
            background: #f9fafb;
            margin: 0 -16px;
            padding: 16px;
            border-top: 2px solid #e5e7eb;
            margin-top: 12px;
        }

        .total-row .item-name {
            font-size: 16px;
            font-weight: 600;
        }

        .total-row .item-total {
            font-size: 18px;
            color: #059669;
            font-weight: 700;
        }

        /* Payment Methods Styles */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }

        .payment-method {
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .payment-method:hover {
            border-color: #9ca3af;
        }

        .payment-method.selected {
            border-color: #374151;
            background: #f8fafc;
        }

        .method-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .method-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: #e5e7eb;
            color: #374151;
        }

        .method-name {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        .method-desc {
            font-size: 12px;
            color: #6b7280;
        }

        .payment-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .input-label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }

        .input-field {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            background: white;
        }

        .input-field:focus {
            outline: none;
            border-color: #374151;
            box-shadow: 0 0 0 2px rgba(55, 65, 81, 0.1);
        }

        .input-field.readonly {
            background: #f9fafb;
            color: #6b7280;
        }

        .input-field.success {
            border-color: #059669;
            background: #f0fdf4;
            color: #047857;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary {
            background: #374151;
            color: white;
        }

        .btn-primary:hover {
            background: #111827;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .qr-section {
            background: #f9fafb;
            padding: 16px;
            border-radius: 6px;
            text-align: center;
            margin-top: 16px;
            display: none;
            border: 1px solid #e5e7eb;
        }

        .qr-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .qr-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #374151;
        }

        .qr-code {
            background: white;
            padding: 16px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
        }

        .qr-placeholder {
            width: 140px;
            height: 140px;
            background: #f3f4f6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 12px;
        }

        .bank-info {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-top: 12px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .bank-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        .bank-row:last-child {
            border-bottom: none;
        }

        .bank-label {
            color: #6b7280;
            font-weight: 500;
        }

        .bank-value {
            font-weight: 600;
            color: #111827;
        }

        /* Promotion Section */
        .promotion-section {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .promotion-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .promotion-header i {
            font-size: 16px;
            color: #6b7280;
        }

        .promotion-header h3 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .promotion-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .promotion-select:focus {
            outline: none;
            border-color: #374151;
        }

        .applied-promotion {
            background: #f0fdf4;
            border: 1px solid #059669;
            border-radius: 6px;
            padding: 12px;
            margin-top: 12px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .applied-promotion.active {
            display: block;
        }

        .promotion-success {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .promotion-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .promotion-icon {
            width: 24px;
            height: 24px;
            background: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .promotion-details h4 {
            font-size: 13px;
            font-weight: 600;
            color: #065f46;
            margin: 0 0 2px 0;
        }

        .promotion-details p {
            font-size: 12px;
            color: #047857;
            margin: 0;
            font-weight: 500;
        }

        .remove-promotion {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .remove-promotion:hover {
            background: #b91c1c;
        }

        .promotion-message {
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 12px;
            font-weight: 500;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .promotion-message.success {
            background: #f0fdf4;
            color: #065f46;
            border: 1px solid #059669;
            display: block;
        }

        .promotion-message.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #dc2626;
            display: block;
        }

        .promotion-message.loading {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #3b82f6;
            display: block;
        }

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .container {
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .bill-meta {
                flex-direction: column;
                gap: 6px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .column-content {
                padding: 12px;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="bill-info">
                    <h1>Thanh Toán - {{ $bill->bill_number }}</h1>
                    <div class="bill-meta">
                        <span>Bàn: {{ $bill->table->table_name }}</span>
                        <span>•</span>
                        <span>Nhân viên: {{ $bill->staff->name ?? 'N/A' }}</span>
                        <span>•</span>
                        <span>Thời gian: {{ \Carbon\Carbon::parse($bill->start_time)->format('H:i d/m/Y') }}</span>
                    </div>
                </div>
                <div class="total-amount" id="total_amount_display">
                    {{ number_format($finalAmount) }} ₫
                </div>
            </div>
        </div>

        <!-- Main Content - 3 Columns -->
        <div class="main-grid">
            <!-- Column 1: Thông tin khách hàng -->
            <div class="column">
                <div class="column-header">
                    <i class="fas fa-user"></i>
                    <h2>Thông tin khách hàng</h2>
                </div>
                <div class="column-content">
                    @if ($bill->user)
                        <div class="customer-info">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-user-circle"></i>
                                    Tên khách hàng
                                </div>
                                <div class="info-value">{{ $bill->user->name }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-phone"></i>
                                    Điện thoại
                                </div>
                                <div class="info-value">{{ $bill->user->phone }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-tag"></i>
                                    Loại khách
                                </div>
                                <div class="info-value">
                                    <span class="badge">{{ $bill->user->customer_type ?? 'Khách mới' }}</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-history"></i>
                                    Số lần đến
                                </div>
                                <div class="info-value">{{ $bill->user->total_visits ?? 0 }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Tổng chi tiêu
                                </div>
                                <div class="info-value">{{ number_format($bill->user->total_spent ?? 0) }} ₫</div>
                            </div>
                        </div>
                    @else
                        <div class="no-customer">
                            <i class="fas fa-user-slash"></i>
                            <h3 style="margin-bottom: 8px; color: #374151; font-size: 14px;">Không có thông tin khách
                                hàng</h3>
                            <p style="color: #6b7280; font-size: 12px;">Hóa đơn này không có thông tin khách hàng</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Column 2: Chi tiết hóa đơn -->
            <div class="column">
                <div class="column-header">
                    <i class="fas fa-receipt"></i>
                    <h2>Chi tiết hóa đơn</h2>
                </div>
                <div class="column-content">
                    <div class="bill-items">
                        @php
                            $totalMinutes = 0;
                            $hourlyRate = 0;
                            $timeCostValue = $timeCost;

                            // Tính thời gian chơi thực tế
                            if ($timeCost > 0) {
                                $timeUsage = $bill->billTimeUsages->first();
                                if ($timeUsage) {
                                    $totalMinutes = $timeUsage->duration_minutes ?? 0;
                                    $hourlyRate = $timeUsage->hourly_rate ?? 0;
                                }
                            }
                        @endphp

                        <!-- Time Usage -->
                        @if ($timeCost > 0)
                            <div class="bill-item">
                                <div class="item-info">
                                    <div class="item-name">Giờ chơi</div>
                                    <div class="item-details">
                                        {{ number_format($hourlyRate) }}₫/h ×
                                        {{ number_format($totalMinutes / 60, 1) }}h
                                    </div>
                                </div>
                                <div class="item-price">
                                    <div class="item-total">{{ number_format($timeCost) }} ₫</div>
                                </div>
                            </div>
                        @endif

                        <!-- Products and Combos -->
                        @foreach ($bill->billDetails->where('is_combo_component', false) as $detail)
                            @if ($detail->product || $detail->combo)
                                <div class="bill-item">
                                    <div class="item-info">
                                        <div class="item-name">
                                            @if ($detail->combo)
                                                [COMBO] {{ $detail->combo->name ?? 'Combo' }}
                                            @else
                                                {{ $detail->product->name ?? 'Sản phẩm' }}
                                            @endif
                                        </div>
                                        <div class="item-details">
                                            @if ($detail->combo && $detail->combo->is_time_combo)
                                                {{ $detail->combo->play_duration_minutes }} phút chơi
                                            @else
                                                Đơn giá: {{ number_format($detail->unit_price) }} ₫
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-price">
                                        <div class="item-quantity">{{ $detail->quantity }} x
                                            {{ number_format($detail->unit_price) }} ₫</div>
                                        <div class="item-total">{{ number_format($detail->total_price) }} ₫</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <!-- Summary -->
                        <div class="bill-item total-row">
                            <div class="item-info">
                                <div class="item-name">TỔNG CỘNG</div>
                                @if ($discountAmount > 0)
                                    <div class="item-details" style="color: #dc2626; font-weight: 500;">
                                        Đã giảm: -{{ number_format($discountAmount) }} ₫
                                    </div>
                                @endif
                            </div>
                            <div class="item-price">
                                <div class="item-total">{{ number_format($finalAmount) }} ₫</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 3: Phương thức thanh toán -->
            <div class="column">
                <div class="column-header">
                    <i class="fas fa-credit-card"></i>
                    <h2>Phương thức thanh toán</h2>
                </div>
                <div class="column-content">
                    <!-- Promotion Section -->
                    <div class="promotion-section">
                        <div class="promotion-header">
                            <i class="fas fa-tag"></i>
                            <h3>Mã giảm giá</h3>
                        </div>

                        @if ($appliedPromotion)
                            <!-- Hiển thị khuyến mãi đã áp dụng -->
                            <div class="applied-promotion active">
                                <div class="promotion-success">
                                    <div class="promotion-info">
                                        <div class="promotion-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="promotion-details">
                                            <h4>{{ $appliedPromotion['name'] }}</h4>
                                            <p>-{{ number_format($discountAmount) }} ₫</p>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-promotion" id="remove_promotion">
                                        <i class="fas fa-times"></i>
                                        Xóa
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="promotion_code" value="{{ $appliedPromotion['code'] }}"
                                id="current_promotion_code">
                        @else
                            <!-- Dropdown chọn mã giảm giá -->
                            <select class="promotion-select" id="promotion_select">
                                <option value="">-- Chọn mã giảm giá --</option>
                                @foreach ($availablePromotions as $promotion)
                                    <option value="{{ $promotion->promotion_code }}">
                                        {{ $promotion->promotion_code }} - {{ $promotion->name }}
                                        ({{ $promotion->discount_type == 'percent' ? $promotion->discount_value . '%' : number_format($promotion->discount_value) . '₫' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="promotion-message" id="promotion_message"></div>
                            <div class="applied-promotion" id="applied_promotion">
                                <!-- Dynamic content sẽ được thêm bằng JavaScript -->
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.bills.preview-payment', $bill->id) }}" method="POST"
                        id="paymentForm">
                        @csrf

                        <!-- Hidden promotion code field -->
                        <input type="hidden" name="promotion_code" id="promotion_code_field"
                            value="{{ $appliedPromotion['code'] ?? '' }}">

                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <div class="payment-method selected" data-method="cash">
                                <div class="method-header">
                                    <div class="method-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <div class="method-name">Tiền mặt</div>
                                        <div class="method-desc">Thanh toán bằng tiền mặt</div>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="cash" checked hidden>
                            </div>

                            <div class="payment-method" data-method="bank">
                                <div class="method-header">
                                    <div class="method-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div>
                                        <div class="method-name">Chuyển khoản</div>
                                        <div class="method-desc">Chuyển khoản ngân hàng</div>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="bank" hidden>
                            </div>

                            <div class="payment-method" data-method="card">
                                <div class="method-header">
                                    <div class="method-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="method-name">Thẻ</div>
                                        <div class="method-desc">Thẻ ATM/Visa/Mastercard</div>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="card" hidden>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="payment-details">
                            <div class="input-group">
                                <label class="input-label">Số tiền thanh toán</label>
                                <input type="number" name="amount" value="{{ $finalAmount }}"
                                    class="input-field readonly" readonly id="total_amount">
                            </div>

                            <div class="input-group">
                                <label class="input-label">Ghi chú</label>
                                <textarea name="note" rows="3" class="input-field" placeholder="Nhập ghi chú cho hóa đơn...">{{ $bill->note ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- QR Section for Bank Transfer -->
                        <div class="qr-section" id="qrSection">
                            <div class="qr-title">Quét mã QR để thanh toán</div>
                            <div class="qr-code">
                                <div class="qr-placeholder">
                                    <div style="text-align: center;">
                                        <i class="fas fa-qrcode text-2xl mb-2"></i>
                                        <div style="font-size: 11px;">Mã QR thanh toán</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bank-info">
                                <div class="bank-row">
                                    <span class="bank-label">Số tiền:</span>
                                    <span class="bank-value">{{ number_format($finalAmount) }} ₫</span>
                                </div>
                                <div class="bank-row">
                                    <span class="bank-label">Nội dung:</span>
                                    <span class="bank-value">{{ $bill->bill_number }}</span>
                                </div>
                                <div class="bank-row">
                                    <span class="bank-label">Ngân hàng:</span>
                                    <span class="bank-value">Vietcombank</span>
                                </div>
                                <div class="bank-row">
                                    <span class="bank-label">Số tài khoản:</span>
                                    <span class="bank-value">0123456789</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Quay lại
                            </button>
                            <button type="submit" class="btn btn-primary" id="submit_btn">
                                <i class="fas fa-print"></i>
                                In hóa đơn & Xác nhận thanh toán
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalAmount = {{ $totalAmount }};
        const originalFinalAmount = {{ $finalAmount }};
        let currentDiscount = {{ $discountAmount }};
        let currentPromotion = @json($appliedPromotion);

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

                // Show/hide relevant sections
                const selectedMethod = this.dataset.method;
                const qrSection = document.getElementById('qrSection');

                if (selectedMethod === 'bank') {
                    qrSection.classList.add('active');
                } else {
                    qrSection.classList.remove('active');
                }
            });
        });

        // Promotion selection
        document.getElementById('promotion_select')?.addEventListener('change', async function() {
            const promotionCode = this.value;
            const promotionMessage = document.getElementById('promotion_message');

            if (!promotionCode) {
                removePromotion();
                return;
            }

            try {
                // Hiển thị loading
                showPromotionMessage('<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra mã...', 'loading');

                // Gọi API kiểm tra mã giảm giá
                const response = await fetch('{{ route('admin.payments.check-promotion') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        promotion_code: promotionCode,
                        bill_id: {{ $bill->id }}
                    })
                });

                const result = await response.json();

                if (result.valid) {
                    // Áp dụng mã giảm giá
                    const applyResponse = await fetch('{{ route('admin.payments.apply-promotion') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            promotion_code: promotionCode,
                            bill_id: {{ $bill->id }}
                        })
                    });

                    const applyResult = await applyResponse.json();

                    if (applyResult.success) {
                        currentDiscount = applyResult.discount_amount;
                        currentPromotion = applyResult.promotion;

                        updatePromotionUI(applyResult.promotion.name, currentDiscount, promotionCode);
                        showPromotionMessage(`✅ ${applyResult.message}`, 'success');
                        celebratePromotion();

                        // Cập nhật hidden field
                        document.getElementById('promotion_code_field').value = promotionCode;
                    } else {
                        throw new Error(applyResult.message);
                    }
                } else {
                    throw new Error(result.message);
                }

            } catch (error) {
                showPromotionMessage(`❌ ${error.message}`, 'error');
                // Reset select
                this.value = '';
            }
        });

        // Remove promotion với API call
        document.getElementById('remove_promotion')?.addEventListener('click', async function() {
            try {
                const response = await fetch('{{ route('admin.payments.remove-promotion') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        bill_id: {{ $bill->id }}
                    })
                });

                const result = await response.json();

                if (result.success) {
                    removePromotion();
                    showPromotionMessage(`✅ ${result.message}`, 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showPromotionMessage(`❌ ${error.message}`, 'error');
            }
        });

        function removePromotion() {
            currentDiscount = 0;
            currentPromotion = null;

            // Hide applied promotion
            document.getElementById('applied_promotion')?.classList.remove('active');

            // Reset select
            document.getElementById('promotion_select').value = '';

            // Update total amount
            updateTotalAmount();

            // Clear hidden field
            document.getElementById('promotion_code_field').value = '';
        }

        // Update promotion UI
        function updatePromotionUI(promotionName, discountAmount, promotionCode) {
            const appliedPromotion = document.getElementById('applied_promotion');
            const promotionNameElem = document.getElementById('promotion_name');
            const promotionDiscountElem = document.getElementById('promotion_discount');

            // Create or update elements
            if (!promotionNameElem) {
                appliedPromotion.innerHTML = `
                    <div class="promotion-success">
                        <div class="promotion-info">
                            <div class="promotion-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="promotion-details">
                                <h4 id="promotion_name">${promotionName}</h4>
                                <p id="promotion_discount">-${formatCurrency(discountAmount)}</p>
                            </div>
                        </div>
                        <button type="button" class="remove-promotion" id="remove_promotion">
                            <i class="fas fa-times"></i>
                            Xóa
                        </button>
                    </div>
                `;

                // Re-attach event listener
                document.getElementById('remove_promotion').addEventListener('click', async function() {
                    try {
                        const response = await fetch('{{ route('admin.payments.remove-promotion') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                bill_id: {{ $bill->id }}
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            removePromotion();
                            showPromotionMessage(`✅ ${result.message}`, 'success');
                        } else {
                            throw new Error(result.message);
                        }
                    } catch (error) {
                        showPromotionMessage(`❌ ${error.message}`, 'error');
                    }
                });
            } else {
                promotionNameElem.textContent = promotionName;
                promotionDiscountElem.textContent = `-${formatCurrency(discountAmount)}`;
            }

            appliedPromotion.classList.add('active');

            // Update total amount
            updateTotalAmount();
        }

        // Update total amount display
        function updateTotalAmount() {
            const finalAmount = totalAmount - currentDiscount;
            const totalAmountInput = document.getElementById('total_amount');
            const totalAmountDisplay = document.getElementById('total_amount_display');

            totalAmountInput.value = finalAmount;
            totalAmountDisplay.textContent = formatCurrency(finalAmount);
        }

        // Show promotion message
        function showPromotionMessage(message, type) {
            const promotionMessage = document.getElementById('promotion_message');
            promotionMessage.innerHTML = message;
            promotionMessage.className = `promotion-message ${type}`;
        }

        // Simple celebration effect
        function celebratePromotion() {
            const appliedPromotion = document.getElementById('applied_promotion');
            appliedPromotion.style.transform = 'scale(1.05)';
            setTimeout(() => {
                appliedPromotion.style.transform = 'scale(1)';
            }, 300);
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Show confirmation dialog
        function showConfirmation() {
            const finalAmount = totalAmount - currentDiscount;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            let paymentMethodText = '';
            switch (paymentMethod) {
                case 'cash':
                    paymentMethodText = 'Tiền mặt';
                    break;
                case 'bank':
                    paymentMethodText = 'Chuyển khoản';
                    break;
                case 'card':
                    paymentMethodText = 'Thẻ';
                    break;
            }

            return Swal.fire({
                title: 'Xác nhận in hóa đơn',
                html: `
            <div style="text-align: center;">
                <div style="font-size: 32px; color: #4f46e5; margin-bottom: 12px;">
                    <i class="fas fa-print"></i>
                </div>
                <p style="margin-bottom: 8px; font-size: 16px; color: #374151;">
                    <strong>Bạn sắp in hóa đơn để khách hàng thanh toán</strong>
                </p>
                <div style="background: #f0f9ff; padding: 12px; border-radius: 8px; margin: 16px 0; border: 1px solid #3b82f6;">
                    <p style="font-size: 20px; font-weight: 700; color: #059669; margin-bottom: 8px;">
                        ${formatCurrency(finalAmount)}
                    </p>
                    <p style="color: #6b7280; margin-bottom: 4px;">
                        Phương thức: <strong>${paymentMethodText}</strong>
                    </p>
                    <p style="color: #6b7280; font-size: 13px;">
                        Số hóa đơn: <strong>{{ $bill->bill_number }}</strong>
                    </p>
                </div>
                ${currentDiscount > 0 ? `
                                    <div style="background: #f0fdf4; padding: 8px; border-radius: 6px; margin: 12px 0; border: 1px solid #059669;">
                                        <p style="margin: 0; color: #065f46; font-weight: 600; font-size: 14px;">
                                            <i class="fas fa-tag"></i> Đã áp dụng mã giảm giá: -${formatCurrency(currentDiscount)}
                                        </p>
                                    </div>
                                ` : ''}
                <div style="background: #fefce8; padding: 12px; border-radius: 6px; margin-top: 16px; border: 1px solid #eab308;">
                    <p style="margin: 0; color: #854d0e; font-size: 13px; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Lưu ý:
                    </p>
                    <p style="margin: 4px 0 0 0; color: #854d0e; font-size: 12px;">
                        Hệ thống sẽ in hóa đơn trước. Sau đó nhân viên xác nhận với khách hàng:
                        <br>• Đã thanh toán → Xác nhận hoàn tất
                        <br>• Chưa thanh toán → Hủy và thanh toán sau
                    </p>
                </div>
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Tiếp tục in hóa đơn',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-deny'
                }
            });
        }

        // Show loading state
        function showLoading() {
            const submitBtn = document.getElementById('submit_btn');
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }

        // Hide loading state
        function hideLoading() {
            const submitBtn = document.getElementById('submit_btn');
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const finalAmount = totalAmount - currentDiscount;

            // Show confirmation dialog
            const result = await showConfirmation();

            if (!result.isConfirmed) {
                return false;
            }

            showLoading();

            // Submit form
            setTimeout(() => {
                this.submit();
            }, 500);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.payment-method[data-method="cash"]').click();

            // Nếu đã có khuyến mãi áp dụng, cập nhật UI
            if (currentPromotion) {
                updatePromotionUI(currentPromotion.name, currentDiscount, currentPromotion.code);
            }
        });
    </script>
</body>

</html>
