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
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
            border-left: 4px solid #1e40af;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bill-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .bill-meta {
            display: flex;
            gap: 16px;
            color: #64748b;
            font-size: 14px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: #059669;
        }

        .back-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 8px;
            color: #475569;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: #e2e8f0;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .column {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .column-header {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .column-header i {
            font-size: 20px;
        }

        .column-header h2 {
            font-size: 18px;
            font-weight: 600;
        }

        .column-content {
            padding: 20px;
        }

        /* Bill Items Styles */
        .bill-items {
            space-y: 12px;
        }

        .bill-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
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
            margin-bottom: 4px;
        }

        .item-details {
            font-size: 14px;
            color: #6b7280;
        }

        .item-price {
            text-align: right;
        }

        .item-quantity {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .item-total {
            font-weight: 600;
            color: #111827;
        }

        .total-row {
            background: #f9fafb;
            margin: 0 -20px;
            padding: 20px;
            border-top: 2px solid #e5e7eb;
            margin-top: 16px;
        }

        .total-row .item-name {
            font-size: 18px;
            font-weight: 600;
        }

        .total-row .item-total {
            font-size: 20px;
            color: #059669;
            font-weight: 700;
        }

        /* Customer Info Styles */
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #8b5cf6;
        }

        .info-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-weight: 600;
            color: #111827;
            font-size: 16px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e0e7ff;
            color: #3730a3;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .no-customer {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .no-customer i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Payment Methods Styles */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .payment-method {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .payment-method:hover {
            border-color: #9ca3af;
        }

        .payment-method.selected {
            border-color: #1e40af;
            background: #eff6ff;
        }

        .method-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .method-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .icon-cash {
            background: #dcfce7;
            color: #166534;
        }

        .icon-bank {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .icon-card {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .method-name {
            font-weight: 600;
            color: #111827;
        }

        .method-desc {
            font-size: 14px;
            color: #6b7280;
        }

        .payment-details {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .input-field {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.2s;
            background: white;
        }

        .input-field:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-field.readonly {
            background: #f9fafb;
            color: #6b7280;
        }

        .input-field.success {
            border-color: #10b981;
            background: #f0fdf4;
            color: #047857;
            font-weight: 600;
        }

        .input-field.error {
            border-color: #ef4444;
            background: #fef2f2;
            color: #dc2626;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #1e40af;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3a8a;
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
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
            display: none;
        }

        .qr-section.active {
            display: block;
        }

        .qr-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #374151;
        }

        .qr-code {
            background: white;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .qr-placeholder {
            width: 160px;
            height: 160px;
            background: #f3f4f6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 14px;
        }

        .bank-info {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
            text-align: left;
        }

        .bank-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
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

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .container {
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            
            .bill-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .column-content {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 12px;
            }
            
            .header {
                padding: 20px;
            }
            
            .total-amount {
                font-size: 24px;
            }
            
            .btn {
                padding: 12px 16px;
                font-size: 14px;
            }
        }

        /* Custom SweetAlert2 Styles */
        .swal2-popup {
            border-radius: 12px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .swal2-title {
            font-size: 24px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
        }

        .swal2-html-container {
            font-size: 16px !important;
            color: #64748b !important;
            line-height: 1.5 !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 24px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            transition: all 0.2s !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3) !important;
        }

        .swal2-deny {
            background: #f3f4f6 !important;
            color: #374151 !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            padding: 12px 24px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            transition: all 0.2s !important;
        }

        .swal2-deny:hover {
            background: #e5e7eb !important;
        }

        .swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        .swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        .swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        .swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
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
                <div class="total-amount">{{ number_format(ceil($bill->final_amount / 1000) * 1000) }} ₫</div>
            </div>
        </div>

        <!-- Main Content - 3 Columns -->
        <div class="main-grid">
            <!-- Column 1: Chi tiết hóa đơn -->
            <div class="column">
                <div class="column-header">
                    <i class="fas fa-receipt"></i>
                    <h2>Chi tiết hóa đơn</h2>
                </div>
                <div class="column-content">
                    <div class="bill-items">
                        @php
                            $finalAmount = ceil($bill->final_amount / 1000) * 1000;
                            $roundedTimeCost = ceil($timeCost / 1000) * 1000;
                        @endphp

                        <!-- Time Usage -->
                        @if ($roundedTimeCost > 0)
                            <div class="bill-item">
                                <div class="item-info">
                                    <div class="item-name">Giờ chơi</div>
                                    <div class="item-details">
                                        @php
                                            $totalMinutes = $timeDetails['total_minutes'] ?? 0;
                                            $hourlyRate = $timeDetails['hourly_rate'] ?? 0;
                                        @endphp
                                        {{ $totalMinutes }} phút @ {{ number_format(ceil($hourlyRate / 1000) * 1000) }}₫/giờ
                                    </div>
                                </div>
                                <div class="item-price">
                                    <div class="item-total">{{ number_format($roundedTimeCost) }} ₫</div>
                                </div>
                            </div>
                        @endif

                        <!-- Combos -->
                        @foreach ($bill->billDetails->where('combo_id', '!=', null)->where('is_combo_component', false) as $comboDetail)
                            @php
                                $roundedComboPrice = ceil($comboDetail->unit_price / 1000) * 1000;
                                $roundedComboTotal = ceil($comboDetail->total_price / 1000) * 1000;
                            @endphp
                            <div class="bill-item">
                                <div class="item-info">
                                    <div class="item-name">{{ $comboDetail->combo->name ?? 'Combo' }}</div>
                                    <div class="item-details">
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
                                <div class="item-price">
                                    <div class="item-quantity">{{ $comboDetail->quantity }} x {{ number_format($roundedComboPrice) }} ₫</div>
                                    <div class="item-total">{{ number_format($roundedComboTotal) }} ₫</div>
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
                                <div class="bill-item">
                                    <div class="item-info">
                                        <div class="item-name">{{ $item->product->name }}</div>
                                        <div class="item-details">Đơn giá: {{ number_format($roundedUnitPrice) }} ₫</div>
                                    </div>
                                    <div class="item-price">
                                        <div class="item-quantity">{{ $item->quantity }} x {{ number_format($roundedUnitPrice) }} ₫</div>
                                        <div class="item-total">{{ number_format($roundedItemTotal) }} ₫</div>
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
                            <div class="bill-item">
                                <div class="item-info">
                                    <div class="item-name">{{ $extra->note ?? 'Phí phát sinh' }}</div>
                                    <div class="item-details">Phí phát sinh</div>
                                </div>
                                <div class="item-price">
                                    <div class="item-quantity">{{ $extra->quantity }} x {{ number_format($roundedExtraPrice) }} ₫</div>
                                    <div class="item-total">{{ number_format($roundedExtraTotal) }} ₫</div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Total Amount -->
                        <div class="bill-item total-row">
                            <div class="item-info">
                                <div class="item-name">TỔNG CỘNG</div>
                            </div>
                            <div class="item-price">
                                <div class="item-total">{{ number_format($finalAmount) }} ₫</div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.tables.detail', $bill->table_id) }}" class="back-btn" style="margin-top: 20px;">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại bàn
                    </a>
                </div>
            </div>

            <!-- Column 2: Thông tin khách hàng -->
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
                            <h3 style="margin-bottom: 8px; color: #374151;">Không có thông tin khách hàng</h3>
                            <p style="color: #6b7280;">Hóa đơn này không có thông tin khách hàng</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Column 3: Phương thức thanh toán -->
            <div class="column">
                <div class="column-header">
                    <i class="fas fa-credit-card"></i>
                    <h2>Phương thức thanh toán</h2>
                </div>
                <div class="column-content">
                    <form action="{{ route('admin.payments.process-payment', $bill->id) }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <div class="payment-method selected" data-method="cash">
                                <div class="method-header">
                                    <div class="method-icon icon-cash">
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
                                    <div class="method-icon icon-bank">
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
                                    <div class="method-icon icon-card">
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
                                       class="input-field readonly" readonly>
                            </div>

                            <div class="input-group" id="cashAmountSection">
                                <label class="input-label">Khách đưa</label>
                                <input type="number" id="cash_received" name="cash_received" 
                                       value="{{ $finalAmount }}" min="{{ $finalAmount }}" step="1000"
                                       class="input-field" oninput="calculateChange()">
                            </div>

                            <div class="input-group" id="changeAmountSection">
                                <label class="input-label">Tiền thối lại</label>
                                <input type="number" id="change_amount" name="change_amount" value="0" 
                                       class="input-field success" readonly>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Ghi chú</label>
                                <textarea name="note" rows="3" class="input-field" placeholder="Nhập ghi chú cho hóa đơn..."></textarea>
                            </div>
                        </div>

                        <!-- QR Section for Bank Transfer -->
                        <div class="qr-section" id="qrSection">
                            <div class="qr-title">Quét mã QR để thanh toán</div>
                            <div class="qr-code">
                                <div class="qr-placeholder">
                                    <div style="text-align: center;">
                                        <i class="fas fa-qrcode text-3xl mb-2"></i>
                                        <div>Mã QR thanh toán</div>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i>
                                Xác nhận thanh toán
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalAmount = {{ $finalAmount }};

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

        // Show beautiful error message
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi thanh toán',
                html: message,
                confirmButtonText: 'Đã hiểu',
                customClass: {
                    confirmButton: 'swal2-confirm'
                }
            });
        }

        // Show confirmation dialog
        function showConfirmation() {
            return Swal.fire({
                title: 'Xác nhận thanh toán',
                html: `Bạn có chắc muốn thanh toán hóa đơn <strong>${totalAmount.toLocaleString('vi-VN')} ₫</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận thanh toán',
                cancelButtonText: 'Hủy',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-deny'
                }
            });
        }

        // Show success message
        function showSuccess(message) {
            return Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal2-confirm'
                }
            });
        }

        // Show loading state
        function showLoading() {
            Swal.fire({
                title: 'Đang xử lý...',
                html: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;

            // Validate cash payment
            if (paymentMethod === 'cash' && cashReceived < totalAmount) {
                showError(`
                    <div style="text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
                        <p style="margin-bottom: 8px;">Số tiền khách đưa không đủ!</p>
                        <p style="font-weight: 600; color: #1e293b;">Vui lòng nhập số tiền lớn hơn hoặc bằng <span style="color: #059669;">${totalAmount.toLocaleString('vi-VN')} ₫</span></p>
                    </div>
                `);
                return false;
            }

            // Show confirmation dialog
            const result = await showConfirmation();
            
            if (!result.isConfirmed) {
                return false;
            }

            // Show loading state
            showLoading();

            // Submit the form after a short delay to show the loading state
            setTimeout(() => {
                this.submit();
            }, 1000);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.payment-method[data-method="cash"]').click();
            document.getElementById('cash_received').value = totalAmount;
            calculateChange();
        });
    </script>
</body>
</html>