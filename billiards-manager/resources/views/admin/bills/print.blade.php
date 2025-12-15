<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In hóa đơn - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            @page {
                size: 80mm 297mm;
                margin: 0;
            }

            body {
                width: 80mm;
                margin: 0;
                padding: 0;
                font-size: 12px;
                line-height: 1.2;
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .print-section {
                display: block !important;
                width: 80mm !important;
                margin: 0 !important;
                padding: 10px !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Đảm bảo tất cả nội dung hiển thị khi in */
            .receipt-item {
                opacity: 1 !important;
                transform: none !important;
            }

            /* Print styles cho QR */
            .qr-container {
                border: 1px solid #000;
                page-break-inside: avoid;
            }

            .qr-overlay {
                display: none;
            }

            .qr-info {
                font-size: 9px;
            }
        }

        @media screen {
            .print-section {
                width: 80mm;
                margin: 20px auto;
                border: 1px solid #ccc;
                padding: 10px;
                background: white;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
        }

        .receipt-line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        .text-xs-print {
            font-size: 10px;
        }

        .text-sm-print {
            font-size: 11px;
        }

        .text-lg-print {
            font-size: 14px;
        }

        .redirect-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease;
            display: none;
        }

        .redirect-overlay.show {
            opacity: 1;
            display: flex;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s ease infinite;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: #f0f;
            opacity: 0;
            z-index: 1001;
        }

        .print-btn {
            position: relative;
            overflow: hidden;
        }

        .receipt-item {
            opacity: 1;
            transform: translateY(0);
        }

        .receipt-item.animated {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .receipt-item.animated.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Màu sắc cho làm tròn */
        .rounded-info {
            background: #fff9e6;
            border-left: 3px solid #f59e0b;
        }

        .rounding-diff {
            color: #f59e0b;
            font-weight: bold;
        }

        .time-comparison {
            font-size: 9px;
            color: #6b7280;
        }

        .rounding-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 9px;
            display: inline-block;
            margin-left: 4px;
        }

        /* QR Code styles */
        .qr-container {
            position: relative;
            margin: 0 auto;
            width: 130px;
            height: 130px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 5px;
        }

        /* MB Bank specific colors */
        .bg-mbbank {
            background-color: #9e1f63;
        }

        .text-mbbank {
            color: #9e1f63;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .countdown-number {
            display: inline-block;
            animation: pulse 1s infinite;
        }

        /* Custom styles for receipt */
        .font-mono {
            font-family: 'Courier New', monospace;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .space-y-1>*+* {
            margin-top: 0.25rem;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .items-center {
            align-items: center;
        }

        .flex-1 {
            flex: 1;
        }

        .bg-white {
            background-color: white;
        }

        .bg-gray-100 {
            background-color: #f7fafc;
        }

        .bg-blue-600 {
            background-color: #2563eb;
        }

        .bg-green-600 {
            background-color: #059669;
        }

        .bg-purple-600 {
            background-color: #7c3aed;
        }

        .bg-gray-600 {
            background-color: #4b5563;
        }

        .bg-pink-600 {
            background-color: #db2777;
        }

        .bg-red-600 {
            background-color: #e53e3e;
        }

        .bg-yellow-600 {
            background-color: #d97706;
        }

        .text-white {
            color: white;
        }

        .text-gray-600 {
            color: #718096;
        }

        .text-red-600 {
            color: #e53e3e;
        }

        .text-green-600 {
            color: #059669;
        }

        .text-yellow-600 {
            color: #d97706;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .px-5 {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .py-2\.5 {
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8;
        }

        .hover\:bg-green-700:hover {
            background-color: #047857;
        }

        .hover\:bg-purple-700:hover {
            background-color: #6d28d9;
        }

        .hover\:bg-gray-700:hover {
            background-color: #374151;
        }

        .hover\:bg-pink-700:hover {
            background-color: #be185d;
        }

        .hover\:bg-red-700:hover {
            background-color: #c53030;
        }

        .hover\:bg-yellow-700:hover {
            background-color: #b45309;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .duration-300 {
            transition-duration: 300ms;
        }

        .transform {
            transform: translateX(0) translateY(0) rotate(0) skewX(0) skewY(0) scaleX(1) scaleY(1);
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .fixed {
            position: fixed;
        }

        .top-4 {
            top: 1rem;
        }

        .left-4 {
            left: 1rem;
        }

        .z-50 {
            z-index: 50;
        }

        .flex-col {
            flex-direction: column;
        }

        .space-y-3>*+* {
            margin-top: 0.75rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .border {
            border-width: 1px;
        }

        .border-gray-300 {
            border-color: #d1d5db;
        }

        .object-contain {
            object-fit: contain;
        }

        .underline {
            text-decoration: underline;
        }

        .space-x-1>*+* {
            margin-left: 0.25rem;
        }

        .space-y-2>*+* {
            margin-top: 0.5rem;
        }

        /* Toast notification styles */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        }

        .toast-success {
            background: #10b981;
        }

        .toast-error {
            background: #ef4444;
        }

        .toast-info {
            background: #3b82f6;
        }

        /* Confirmation buttons styles */
        .confirmation-section {
            max-width: 300px;
            margin: 20px auto;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
        }

        .confirmation-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 15px 0;
        }

        .confirm-btn,
        .cancel-btn {
            padding: 14px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .confirm-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .confirm-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .cancel-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .cancel-btn:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .notice-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 12px;
            margin-top: 15px;
        }

        .notice-box strong {
            color: #92400e;
        }

        .notice-box p {
            color: #78350f;
            margin: 5px 0;
            font-size: 12px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Redirect Overlay - CHỈ HIỂN THỊ KHI ĐÃ THANH TOÁN -->
    <div id="redirectOverlay" class="redirect-overlay no-print">
        <div class="text-center transform transition-all duration-500 scale-90" id="overlayContent">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-lg-print font-bold mb-4">In hóa đơn thành công!</h2>
            <p class="text-sm-print mb-6">Tự động chuyển về danh sách bàn sau <span id="countdown"
                    class="font-bold countdown-number">3</span> giây...</p>
            <div class="flex space-x-3">
                <button onclick="redirectNow()"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-forward mr-2"></i>Chuyển ngay
                </button>
                <button onclick="stayHere()"
                    class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-times mr-2"></i>Ở lại
                </button>
            </div>
        </div>
    </div>

    <!-- Nút điều khiển - chỉ hiển thị trên màn hình -->
    <div class="no-print fixed top-4 left-4 z-50 flex flex-col space-y-3">
        <button id="printBtn" onclick="printReceipt()"
            class="print-btn bg-blue-600 text-white px-5 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 flex items-center">
            <i class="fas fa-print mr-2"></i> In hóa đơn
        </button>
        <button onclick="goBackWithConfirmation()"
            class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-105 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </button>
        <button id="animateBtn" onclick="animateReceipt()"
            class="bg-purple-600 text-white px-5 py-3 rounded-lg shadow-lg hover:bg-purple-700 transition-all duration-300 transform hover:scale-105 flex items-center">
            <i class="fas fa-play mr-2"></i> Hiệu ứng hóa đơn
        </button>
    </div>

    <!-- Nội dung hóa đơn -->
    <div class="print-section bg-white p-4 font-mono">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="font-bold text-lg-print uppercase receipt-item">BILLIARDS CLUB</h1>
            <p class="text-sm-print receipt-item">HÓA ĐƠN THANH TOÁN</p>
            <div class="receipt-line receipt-item"></div>
        </div>

        <!-- Thông tin hóa đơn -->
        <div class="space-y-1 text-sm-print">
            <div class="flex justify-between receipt-item">
                <span>Mã HĐ:</span>
                <span class="font-bold">{{ $bill->bill_number }}</span>
            </div>
            <div class="flex justify-between receipt-item">
                <span>Bàn:</span>
                <span>{{ $bill->table->table_number }} - {{ $bill->table->table_name }}</span>
            </div>
            <div class="flex justify-between receipt-item">
                <span>Khách hàng:</span>
                <span>{{ $bill->user->name ?? 'Khách vãng lai' }}</span>
            </div>
            <div class="flex justify-between receipt-item">
                <span>Nhân viên:</span>
                <span>{{ $staff }}</span>
            </div>
            <div class="flex justify-between receipt-item">
                <span>Giờ vào:</span>
                <span>{{ \Carbon\Carbon::parse($bill->start_time)->format('H:i d/m/Y') }}</span>
            </div>
            @if ($bill->end_time)
                <div class="flex justify-between receipt-item">
                    <span>Giờ ra:</span>
                    <span>{{ \Carbon\Carbon::parse($bill->end_time)->format('H:i d/m/Y') }}</span>
                </div>
            @endif
            <div class="receipt-line receipt-item"></div>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="mt-3">
            <div class="text-center font-bold text-sm-print mb-2 receipt-item">CHI TIẾT HÓA ĐƠN</div>

            @php
                // Tính toán các giá trị
                $finalAmount = $bill->final_amount ?? 0;
                $totalAmount = $bill->total_amount ?? 0;
                $discountAmount = $bill->discount_amount ?? 0;

                // Tính tiền giờ và sản phẩm
                $productDetails = $bill->billDetails->where('is_combo_component', false) ?? collect();
                $productTotal = $productDetails->sum('total_price') ?? 0;
                $timeCost = $totalAmount - $productTotal;

                // QR Code URL
                $qrUrl =
                    'https://img.vietqr.io/image/MB-0368015218-qr_only.png?' .
                    http_build_query([
                        'amount' => $finalAmount,
                        'addInfo' => "TT Bill {$bill->bill_number}",
                    ]);
            @endphp

            @if ($productDetails->count() > 0)
                <div class="space-y-1 text-xs-print">
                    @foreach ($productDetails as $detail)
                        @php
                            $itemName = $detail->product->name ?? ($detail->combo->name ?? 'Sản phẩm');
                            $itemTotal = $detail->total_price ?? 0;

                            if ($detail->combo_id && !$detail->is_combo_component) {
                                $itemName = '[COMBO] ' . $itemName;
                            }
                        @endphp
                        <div class="flex justify-between receipt-item">
                            <div class="flex-1">
                                <span>{{ $itemName }}</span>
                                <span class="text-gray-600">x{{ $detail->quantity ?? 1 }}</span>
                            </div>
                            <div class="text-right">
                                {{ number_format($itemTotal, 0, ',', '.') }}₫
                            </div>
                        </div>
                        @if ($detail->combo && $detail->combo->is_time_combo)
                            <div class="text-xs-print receipt-item text-gray-600 ml-2">
                                ↳ {{ $detail->combo->play_duration_minutes ?? 0 }} phút chơi
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="receipt-line receipt-item"></div>
            @endif

            <!-- Thời gian chơi -->
            @if ($timeCost > 0)
                <div class="flex justify-between text-sm-print receipt-item">
                    <span>Tiền giờ:</span>
                    <span>{{ number_format($timeCost, 0, ',', '.') }}₫</span>
                </div>
            @endif

            <!-- Tổng tạm tính -->
            <div class="flex justify-between text-sm-print receipt-item">
                <span>Tổng tạm tính:</span>
                <span>{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
            </div>

            <!-- Giảm giá & Khuyến mãi -->
            @if ($discountAmount > 0)
                <div class="flex justify-between text-sm-print receipt-item">
                    <span>Giảm giá:</span>
                    <span class="text-red-600">-{{ number_format($discountAmount, 0, ',', '.') }}₫</span>
                </div>
                <div class="receipt-line receipt-item"></div>
            @endif

            <!-- Tổng cộng -->
            <div class="flex justify-between font-bold text-sm-print mt-2 receipt-item">
                <span>TỔNG CỘNG:</span>
                <span>{{ number_format($finalAmount, 0, ',', '.') }}₫</span>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        @if ($bill->payment_method)
            <div class="mt-3 text-sm-print">
                <div class="flex justify-between receipt-item">
                    <span>PT thanh toán:</span>
                    <span class="font-bold">
                        @switch($bill->payment_method)
                            @case('cash')
                                Tiền mặt
                            @break

                            @case('bank')
                                Chuyển khoản
                            @break

                            @case('card')
                                Thẻ
                            @break

                            @default
                                {{ $bill->payment_method }}
                        @endswitch
                    </span>
                </div>
            </div>
        @endif

        <!-- QR Code Thanh Toán -->
        <div class="mt-4 text-center receipt-item">
            <div class="receipt-line mb-2"></div>

            <!-- QR Code Image -->
            <div class="flex justify-center mb-2">
                <img src="{{ $qrUrl }}" alt="QR Code Thanh Toán MB Bank"
                    class="w-32 h-32 object-contain mx-auto border border-gray-300 rounded qr-container" id="qrImage">
            </div>

            <!-- Thông tin thanh toán QR -->
            <div class="text-xs-print space-y-1 mb-2">
                <div class="flex justify-between">
                    <span>Số tài khoản:</span>
                    <span class="font-bold text-mbbank">0368015218</span>
                </div>
                <div class="flex justify-between">
                    <span>Ngân hàng:</span>
                    <span class="font-bold text-mbbank">MB Bank</span>
                </div>
                <div class="flex justify-between">
                    <span>Chủ tài khoản:</span>
                    <span class="font-bold">BILLIARDS CLUB</span>
                </div>
                <div class="flex justify-between">
                    <span>Số tiền:</span>
                    <span class="font-bold text-green-600">{{ number_format($finalAmount, 0, ',', '.') }}₫</span>
                </div>
                <div class="text-left">
                    <span>Nội dung:</span>
                    <span class="font-bold">TT Bill {{ $bill->bill_number }}</span>
                </div>
            </div>

            <p class="text-xs-print text-gray-600 mb-2">
                <i class="fas fa-qrcode mr-1"></i>
                Quét mã QR để thanh toán {{ number_format($finalAmount, 0, ',', '.') }}₫
            </p>

            <div class="receipt-line mt-2"></div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs-print">
            <div class="receipt-line receipt-item"></div>
            <p class="mt-2 receipt-item">Cảm ơn quý khách!</p>
            <p class="receipt-item">Hẹn gặp lại</p>
            <p class="mt-1 receipt-item">In lúc: {{ $printTime ?? now()->format('H:i d/m/Y') }}</p>
        </div>

        <!-- Khoảng trắng cuối bill -->
        <div class="mt-8"></div>
    </div>

    <!-- Phần xác nhận thanh toán - CHỈ HIỂN THỊ KHI LÀ PREVIEW -->
    @if (isset($isPreview) && $isPreview)
        <div class="confirmation-section no-print">
            <h3 class="font-bold text-sm-print mb-3 text-blue-600 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                XÁC NHẬN THANH TOÁN VỚI KHÁCH HÀNG
            </h3>

            <div class="confirmation-buttons">
                <!-- Form xác nhận thanh toán thành công -->
                <form action="{{ route('admin.bills.confirm-payment', $bill->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" onclick="return confirmPaymentSuccess()" class="confirm-btn">
                        <i class="fas fa-check-circle mr-2"></i>
                        Xác nhận thanh toán thành công
                    </button>
                    <p class="text-xs text-gray-600 mt-1 text-center">
                        Nhấn khi khách hàng đã thanh toán xong
                    </p>
                </form>

                <!-- Form hủy thanh toán (thanh toán lúc khác) -->
                <form action="{{ route('admin.bills.cancel-payment', $bill->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" onclick="return confirmCancelPayment()" class="cancel-btn">
                        <i class="fas fa-clock mr-2"></i>
                        Hủy - Thanh toán lúc khác
                    </button>
                    <p class="text-xs text-gray-600 mt-1 text-center">
                        Nhấn nếu khách hàng chưa thanh toán
                    </p>
                </form>

                <!-- Nút in lại -->
                <button onclick="window.print()"
                    class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 flex items-center justify-center w-full mt-2">
                    <i class="fas fa-print mr-2"></i>
                    In lại hóa đơn
                </button>
            </div>

            <!-- Thông báo quan trọng -->
            <div class="notice-box">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-yellow-800">LƯU Ý QUAN TRỌNG:</p>
                        <p class="text-xs text-yellow-700">
                            1. Hãy đưa hóa đơn cho khách hàng xác nhận<br>
                            2. Chỉ nhấn "Xác nhận thanh toán thành công" khi đã nhận tiền<br>
                            3. Nếu khách chưa trả tiền, nhấn "Hủy - Thanh toán lúc khác"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Khai báo biến toàn cục
        let countdown = 3;
        let countdownInterval;
        const redirectUrl = '{{ route('admin.bills.index') }}';
        const autoRedirect = {{ isset($autoRedirect) && $autoRedirect ? 'true' : 'false' }};
        const isPaid = {{ $bill->payment_status === 'Paid' ? 'true' : 'false' }};
        const isPreview = {{ isset($isPreview) && $isPreview ? 'true' : 'false' }};
        let hasPrinted = false;

        // Tự động in khi trang load (chỉ khi là preview)
        document.addEventListener('DOMContentLoaded', function() {
            resetReceiptAnimation();

            // Tự động in khi là preview
            if (isPreview && !hasPrinted) {
                setTimeout(() => {
                    printReceipt();
                }, 1000);
            }
        });

        // Hàm in hóa đơn với hiệu ứng
        function printReceipt() {
            if (!hasPrinted) {
                // Hiệu ứng nút in
                const printBtn = document.getElementById('printBtn');
                if (printBtn) {
                    printBtn.classList.add('bg-blue-800');
                    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang in...';
                }

                setTimeout(() => {
                    window.print();
                    hasPrinted = true;

                    // Khôi phục nút in
                    setTimeout(() => {
                        if (printBtn) {
                            printBtn.classList.remove('bg-blue-800');
                            printBtn.innerHTML = '<i class="fas fa-print mr-2"></i> In hóa đơn';
                        }

                        // Hiển thị overlay chuyển hướng
                        if (autoRedirect && isPaid) {
                            showRedirectOverlay();
                        }
                    }, 1000);
                }, 800);
            } else {
                window.print();
            }
        }

        // Hàm chuyển hướng
        function redirectNow() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            window.location.href = redirectUrl;
        }

        // Hàm ở lại trang
        function stayHere() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            const overlay = document.getElementById('redirectOverlay');
            overlay.classList.remove('show');

            setTimeout(() => {
                overlay.style.display = 'none';
            }, 500);
        }

        // Hiển thị overlay chuyển hướng với hiệu ứng
        function showRedirectOverlay() {
            const overlay = document.getElementById('redirectOverlay');
            const content = document.getElementById('overlayContent');

            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.classList.add('show');
                content.style.transform = 'scale(1)';

                // Bắt đầu đếm ngược
                countdownInterval = setInterval(function() {
                    countdown--;
                    document.getElementById('countdown').textContent = countdown;

                    if (countdown <= 0) {
                        redirectNow();
                    }
                }, 1000);
            }, 100);
        }

        // Hiệu ứng cho hóa đơn
        function animateReceipt() {
            const items = document.querySelectorAll('.receipt-item');
            items.forEach(item => {
                item.classList.add('animated');
                item.classList.remove('visible');
            });
            items.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('visible');
                }, 100 + (index * 50));
            });
        }

        function resetReceiptAnimation() {
            const items = document.querySelectorAll('.receipt-item');
            items.forEach(item => {
                item.classList.remove('animated');
                item.classList.remove('visible');
            });
        }

        // Xác nhận thanh toán thành công
        function confirmPaymentSuccess() {
            const finalAmount = {{ $finalAmount }};

            return confirmAction(
                'Xác nhận thanh toán thành công?',
                `Bạn có chắc khách hàng đã thanh toán số tiền: ${formatCurrency(finalAmount)}?`,
                'success',
                'Xác nhận, khách đã trả tiền',
                '#059669'
            );
        }

        // Xác nhận hủy thanh toán
        function confirmCancelPayment() {
            return confirmAction(
                'Hủy thanh toán?',
                'Khách hàng sẽ thanh toán vào lúc khác?',
                'warning',
                'Xác nhận hủy',
                '#f59e0b'
            );
        }

        // Hàm chung xác nhận
        function confirmAction(title, text, icon, confirmText, confirmColor) {
            return new Promise((resolve) => {
                if (typeof Swal === 'undefined') {
                    // Nếu không có SweetAlert, sử dụng confirm thông thường
                    const result = window.confirm(`${title}\n\n${text}`);
                    resolve(result);
                    return;
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Hủy bỏ',
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            });
        }

        // Xử lý quay lại với xác nhận
        async function goBackWithConfirmation() {
            if (isPreview) {
                const confirmed = await confirmAction(
                    'Rời khỏi trang thanh toán?',
                    'Bạn có một thanh toán đang chờ xác nhận. Nếu rời đi mà chưa xác nhận, thanh toán sẽ bị hủy.',
                    'warning',
                    'Hủy thanh toán và quay lại',
                    '#ef4444'
                );

                if (confirmed) {
                    // Gọi API hủy thanh toán
                    fetch('{{ route('admin.bills.cancel-payment', $bill->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.href = '{{ route('admin.tables.detail', $bill->table_id) }}';
                    });
                }
            } else {
                window.location.href = '{{ route('admin.bills.index') }}';
            }
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type} no-print`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Ngăn chặn đóng trang khi đang có thanh toán chờ xác nhận
        window.addEventListener('beforeunload', function(e) {
            if (isPreview) {
                e.preventDefault();
                e.returnValue = 'Bạn có thanh toán đang chờ xác nhận. Bạn có chắc muốn rời đi?';
            }
        });
    </script>
</body>

</html>
