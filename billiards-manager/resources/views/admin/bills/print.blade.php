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
            }

            .no-print {
                display: none !important;
            }

            .print-section {
                display: block !important;
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
            /* Mặc định hiển thị bình thường */
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
    </style>
</head>

<body class="bg-gray-100">
    <!-- Redirect Overlay - CHỈ HIỂN THỊ KHI ĐÃ THANH TOÁN -->
    <div id="redirectOverlay" class="redirect-overlay no-print">
        <div class="text-center transform transition-all duration-500 scale-90" id="overlayContent">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-2xl font-bold mb-4">In hóa đơn thành công!</h2>
            <p class="text-lg mb-6">Tự động chuyển về danh sách bàn sau <span id="countdown"
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
         <button onclick="goBack()"
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
            <h1 class="font-bold text-lg uppercase receipt-item">BILLIARDS CLUB</h1>
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

            <!-- Sản phẩm -->
            @php
                $productDetails = $bill->billDetails->where('is_combo_component', false);

                // Sử dụng CHÍNH XÁC cùng logic làm tròn như trang detail và payment
                $roundedFinalAmount = ceil($bill->final_amount / 1000) * 1000;
                $roundedTimeCost = ceil(($timeCost ?? 0) / 1000) * 1000;
                $roundedDiscountAmount = ceil(($bill->discount_amount ?? 0) / 1000) * 1000;

                // Tính tổng sản phẩm đã làm tròn theo từng item
                $roundedProductTotal = 0;
                foreach ($productDetails as $detail) {
                    $roundedProductTotal += ceil($detail->total_price / 1000) * 1000;
                }
            @endphp

            @if ($productDetails->count() > 0)
                <div class="space-y-1 text-xs-print">
                    @foreach ($productDetails as $detail)
                        @php
                            $roundedDetailPrice = ceil($detail->total_price / 1000) * 1000;
                        @endphp
                        <div class="flex justify-between receipt-item">
                            <div class="flex-1">
                                <span>{{ $detail->product->name ?? ($detail->combo->name ?? 'Sản phẩm') }}</span>
                                <span class="text-gray-600">x{{ $detail->quantity }}</span>
                            </div>
                            <div class="text-right">
                                {{ number_format($roundedDetailPrice, 0, ',', '.') }}₫
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="receipt-line receipt-item"></div>
            @endif

            <!-- Thời gian chơi -->
            @if ($timeCost > 0)
                <div class="flex justify-between text-sm-print receipt-item">
                    <span>Tiền giờ:</span>
                    <span>{{ number_format($roundedTimeCost, 0, ',', '.') }}₫</span>
                </div>
            @endif

            {{-- Hiển thị thông tin chuyển bàn --}}
            @foreach ($timeDetails['sessions'] as $session)
                @if (isset($session['table_note']))
                    <div class="transfer-info receipt-item">
                        <small class="text-muted">{{ $session['table_note'] }}</small>
                    </div>
                @endif
            @endforeach

            <!-- Tổng tiền hàng -->
            <div class="flex justify-between text-sm-print receipt-item">
                <span>Tổng tiền hàng:</span>
                <span>{{ number_format($roundedProductTotal, 0, ',', '.') }}₫</span>
            </div>

            <!-- Giảm giá -->
            @if ($bill->discount_amount > 0)
                <div class="flex justify-between text-sm-print receipt-item">
                    <span>Giảm giá:</span>
                    <span>-{{ number_format($roundedDiscountAmount, 0, ',', '.') }}₫</span>
                </div>
            @endif

            <!-- Tổng cộng -->
            <div class="flex justify-between font-bold text-sm-print mt-2 receipt-item">
                <span>TỔNG CỘNG:</span>
                <span>{{ number_format($roundedFinalAmount, 0, ',', '.') }}₫</span>
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

                            @case('bank_transfer')
                                Chuyển khoản
                            @break

                            @case('card')
                                Thẻ
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

            if (!window.matchMedia('print').matches) {
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
                printBtn.classList.add('bg-blue-800');
                printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang in...';

                setTimeout(() => {
                    window.print();
                    hasPrinted = true;

                    // Khôi phục nút in
                    setTimeout(() => {
                        printBtn.classList.remove('bg-blue-800');
                        printBtn.innerHTML = '<i class="fas fa-print mr-2"></i> In hóa đơn';

                        // CHỈ HIỂN THỊ OVERLAY CHUYỂN HƯỚNG NẾU CÓ autoRedirect
                        if (autoRedirect) {
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

        // Hàm quay lại trang trước
        function goBack() {
            window.history.back();
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

        // Hiển thị overlay sau khi in - CHỈ KHI CÓ autoRedirect
        window.onafterprint = function() {
            if (autoRedirect) {
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

        // Fallback: nếu onafterprint không hoạt động, sử dụng setTimeout - CHỈ KHI CÓ autoRedirect
        setTimeout(function() {
            // Kiểm tra nếu đang ở chế độ màn hình (không phải print preview) VÀ có autoRedirect
            if (!window.matchMedia('print').matches && document.hasFocus() && !hasPrinted && autoRedirect) {
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

        // Các hàm khác giữ nguyên...
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
    </script>
</body>

</html>
