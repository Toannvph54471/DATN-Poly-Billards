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

        .print-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .print-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
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

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }

            100% {
                transform: scale(20, 20);
                opacity: 0;
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
        <a href="{{ route('admin.bills.index') }}">
            <button
                class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-105 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </button>
        </a>
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
                // SỬ DỤNG DỮ LIỆU ĐÃ ĐƯỢC TÍNH TOÁN TỪ BILL
                $finalAmount = $bill->final_amount;
                $totalAmount = $bill->total_amount;
                $discountAmount = $bill->discount_amount;

                // Tính tiền giờ và sản phẩm từ bill details
                $productDetails = $bill->billDetails->where('is_combo_component', false);
                $productTotal = $productDetails->sum('total_price');
                $timeCost = $totalAmount - $productTotal;
            @endphp

            @if ($productDetails->count() > 0)
                <div class="space-y-1 text-xs-print">
                    @foreach ($productDetails as $detail)
                        @php
                            $itemName = $detail->product->name ?? ($detail->combo->name ?? 'Sản phẩm');
                            $itemPrice = $detail->unit_price;
                            $itemTotal = $detail->total_price;

                            // Xác định loại item để hiển thị
                            if ($detail->combo_id && !$detail->is_combo_component) {
                                $itemName = '[COMBO] ' . $itemName;
                            }
                        @endphp
                        <div class="flex justify-between receipt-item">
                            <div class="flex-1">
                                <span>{{ $itemName }}</span>
                                <span class="text-gray-600">x{{ $detail->quantity }}</span>
                            </div>
                            <div class="text-right">
                                {{ number_format($itemTotal, 0, ',', '.') }}₫
                            </div>
                        </div>
                        @if ($detail->combo && $detail->combo->is_time_combo)
                            <div class="text-xs-print receipt-item text-gray-600 ml-2">
                                ↳ {{ $detail->combo->play_duration_minutes }} phút chơi
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

                <!-- Hiển thị thông tin khuyến mãi -->
                @if ($promotionInfo && isset($promotionInfo['name']))
                    <div class="text-xs-print receipt-item text-center text-gray-600">
                        <div>{{ $promotionInfo['name'] }}</div>
                        @if (isset($promotionInfo['code']))
                            <div>Mã: {{ $promotionInfo['code'] }}</div>
                        @endif
                    </div>
                @else
                    <!-- Fallback: Trích xuất từ note -->
                    @php
                        $promotionText = '';
                        if ($bill->note) {
                            // Sử dụng cùng logic với controller
                            if (preg_match('/Mã KM:\s*(\w+)\s*-\s*(.+?)(?:\s*\||$)/', $bill->note, $matches)) {
                                $promoCode = trim($matches[1]);
                                $promoName = trim($matches[2]);
                                $promotionText = "<div>$promoName</div><div>Mã: $promoCode</div>";
                            }
                        }
                    @endphp
                    @if ($promotionText)
                        <div class="text-xs-print receipt-item text-center text-gray-600">
                            {!! $promotionText !!}
                        </div>
                    @endif
                @endif
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

                            @case('bank_transfer')
                                Chuyển khoản
                            @break

                            @case('vnpay')
                                VNPay
                            @break

                            @case('momo')
                                MoMo
                            @break

                            @default
                                {{ $bill->payment_method }}
                        @endswitch
                    </span>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-6 text-center text-xs-print">
            <div class="receipt-line receipt-item"></div>
            <p class="mt-2 receipt-item">Cảm ơn quý khách!</p>
            <p class="receipt-item">Hẹn gặp lại</p>
            <p class="mt-1 receipt-item">In lúc: {{ $printTime }}</p>
        </div>

        <!-- Khoảng trắng cuối bill -->
        <div class="mt-8"></div>
    </div>

    <script>
        let countdown = 3;
        let countdownInterval;
        const redirectUrl = '{{ $redirectUrl ?? route('admin.bills.index') }}';
        const autoRedirect = {{ $autoRedirect ? 'true' : 'false' }};
        const isPaid = {{ $bill->payment_status === 'Paid' ? 'true' : 'false' }};
        let hasPrinted = false;
        let animationEnabled = false;

        // Tự động in khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            // Đảm bảo hóa đơn hiển thị ngay lập tức
            resetReceiptAnimation();

            // Tự động in sau 1 giây nếu là thanh toán mới
            if (isPaid && !hasPrinted) {
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

                        // CHỈ HIỂN THỊ OVERLAY CHUYỂN HƯỚNG NẾU CÓ autoRedirect VÀ ĐÃ THANH TOÁN
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

            // Ẩn overlay với hiệu ứng
            const overlay = document.getElementById('redirectOverlay');
            overlay.classList.remove('show');

            setTimeout(() => {
                overlay.style.display = 'none';
            }, 500);
        }

        // Hiệu ứng confetti
        function createConfetti() {
            const colors = ['#f94144', '#f3722c', '#f8961e', '#f9c74f', '#90be6d', '#43aa8b', '#577590'];
            const confettiCount = 50;

            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                document.body.appendChild(confetti);

                // Animation
                const animation = confetti.animate([{
                        transform: `translate(0, 0) rotate(0deg)`,
                        opacity: 1
                    },
                    {
                        transform: `translate(${Math.random() * 100 - 50}px, ${window.innerHeight}px) rotate(${Math.random() * 360}deg)`,
                        opacity: 0
                    }
                ], {
                    duration: 1000 + Math.random() * 2000,
                    easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
                });

                animation.onfinish = () => {
                    confetti.remove();
                };
            }
        }

        // Hiển thị overlay sau khi in - CHỈ KHI CÓ autoRedirect VÀ ĐÃ THANH TOÁN
        window.onafterprint = function() {
            if (autoRedirect && isPaid) {
                showRedirectOverlay();
            }
        };

        // Hiển thị overlay chuyển hướng với hiệu ứng
        function showRedirectOverlay() {
            const overlay = document.getElementById('redirectOverlay');
            const content = document.getElementById('overlayContent');

            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.classList.add('show');
                content.style.transform = 'scale(1)';

                // Tạo confetti
                createConfetti();

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

        // Fallback: nếu onafterprint không hoạt động, sử dụng setTimeout
        setTimeout(function() {
            // Kiểm tra nếu đang ở chế độ màn hình (không phải print preview) VÀ có autoRedirect VÀ đã thanh toán
            if (!window.matchMedia('print').matches && document.hasFocus() && !hasPrinted && autoRedirect &&
                isPaid) {
                // Chờ thêm 2 giây rồi hiển thị overlay
                setTimeout(function() {
                    const overlay = document.getElementById('redirectOverlay');
                    if (!overlay.classList.contains('show') && overlay.style.display !== 'flex') {
                        showRedirectOverlay();
                    }
                }, 2000);
            }
        }, 3000);

        // Cho phép đóng bằng phím ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                stayHere();
            }
        });

        // Hiệu ứng hover cho các nút
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Hiệu ứng cho hóa đơn
        function animateReceipt() {
            const items = document.querySelectorAll('.receipt-item');
            animationEnabled = true;
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
            animationEnabled = false;
        }

        // Thêm sự kiện cho nút hiệu ứng
        document.getElementById('animateBtn')?.addEventListener('click', animateReceipt);
    </script>
</body>

</html>
