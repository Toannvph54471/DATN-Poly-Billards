<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In hóa đơn - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        }

        @media screen {
            .print-section {
                width: 80mm;
                margin: 20px auto;
                border: 1px solid #ccc;
                padding: 10px;
                background: white;
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
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            z-index: 1000;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Redirect Overlay -->
    <div id="redirectOverlay" class="redirect-overlay no-print" style="display: none;">
        <div class="text-center">
            <div class="text-4xl mb-4">✅</div>
            <h2 class="text-xl font-bold mb-2">In hóa đơn thành công!</h2>
            <p class="text-lg mb-4">Tự động chuyển về danh sách bàn sau <span id="countdown" class="font-bold">3</span>
                giây...</p>
            <div class="flex space-x-2">
                <button onclick="redirectNow()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Chuyển ngay
                </button>
                <button onclick="stayHere()"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Ở lại
                </button>
            </div>
        </div>
    </div>

    <!-- Nút điều khiển - chỉ hiển thị trên màn hình -->
    <div class="no-print fixed top-4 left-4 z-50">
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors">
            🖨️ In hóa đơn
        </button>
        <button onclick="redirectNow()"
            class="bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-700 transition-colors ml-2">
            📋 Về danh sách bàn
        </button>
    </div>

    <!-- Nội dung hóa đơn -->
    <div class="print-section bg-white p-4 font-mono">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="font-bold text-lg uppercase">BILLIARDS CLUB</h1>
            <p class="text-sm-print">HÓA ĐƠN THANH TOÁN</p>
            <div class="receipt-line"></div>
        </div>

        <!-- Thông tin hóa đơn -->
        <div class="space-y-1 text-sm-print">
            <div class="flex justify-between">
                <span>Mã HĐ:</span>
                <span class="font-bold">{{ $bill->bill_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bàn:</span>
                <span>{{ $bill->table->table_number }} - {{ $bill->table->table_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Khách hàng:</span>
                <span>{{ $bill->user->name ?? 'Khách vãng lai' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nhân viên:</span>
                <span>{{ $staff }}</span>
            </div>
            <div class="flex justify-between">
                <span>Giờ vào:</span>
                <span>{{ \Carbon\Carbon::parse($bill->start_time)->format('H:i d/m/Y') }}</span>
            </div>
            @if ($bill->end_time)
                <div class="flex justify-between">
                    <span>Giờ ra:</span>
                    <span>{{ \Carbon\Carbon::parse($bill->end_time)->format('H:i d/m/Y') }}</span>
                </div>
            @endif
            <div class="receipt-line"></div>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="mt-3">
            <div class="text-center font-bold text-sm-print mb-2">CHI TIẾT HÓA ĐƠN</div>

            <!-- Sản phẩm -->
            @php
                $productDetails = $bill->billDetails->where('is_combo_component', false);
            @endphp

            @if ($productDetails->count() > 0)
                <div class="space-y-1 text-xs-print">
                    @foreach ($productDetails as $detail)
                        <div class="flex justify-between">
                            <div class="flex-1">
                                <span>{{ $detail->product->name ?? ($detail->combo->name ?? 'Sản phẩm') }}</span>
                                <span class="text-gray-600">x{{ $detail->quantity }}</span>
                            </div>
                            <div class="text-right">
                                {{ number_format($detail->total_price, 0, ',', '.') }}₫
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="receipt-line"></div>
            @endif

            <!-- Thời gian chơi -->
            @if ($timeCost > 0)
                <div class="flex justify-between text-sm-print">
                    <span>Tiền giờ:</span>
                    <span>{{ number_format($timeCost, 0, ',', '.') }}₫</span>
                </div>
            @endif

            {{-- Hiển thị thông tin chuyển bàn --}}
            @foreach ($timeDetails['sessions'] as $session)
                @if (isset($session['table_note']))
                    <div class="transfer-info">
                        <small class="text-muted">{{ $session['table_note'] }}</small>
                    </div>
                @endif
            @endforeach

            <!-- Tổng tiền hàng -->
            <div class="flex justify-between text-sm-print">
                <span>Tổng tiền hàng:</span>
                <span>{{ number_format($productTotal, 0, ',', '.') }}₫</span>
            </div>

            <!-- Giảm giá -->
            @if ($bill->discount_amount > 0)
                <div class="flex justify-between text-sm-print">
                    <span>Giảm giá:</span>
                    <span>-{{ number_format($bill->discount_amount, 0, ',', '.') }}₫</span>
                </div>
            @endif

            <!-- Tổng cộng -->
            <div class="flex justify-between font-bold text-sm-print mt-2">
                <span>TỔNG CỘNG:</span>
                <span>{{ number_format($finalAmount, 0, ',', '.') }}₫</span>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        @if ($bill->payment_method)
            <div class="mt-3 text-sm-print">
                <div class="flex justify-between">
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
            <div class="receipt-line"></div>
            <p class="mt-2">Cảm ơn quý khách!</p>
            <p>Hẹn gặp lại</p>
            <p class="mt-1">In lúc: {{ $printTime }}</p>
        </div>

        <!-- Khoảng trắng cuối bill -->
        <div class="mt-8"></div>
    </div>

    <script>
        let countdown = 3;
        let countdownInterval;
        const redirectUrl = '{{ route('admin.tables.index') }}';

        // Tự động in khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.matchMedia('print').matches) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });

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
            document.getElementById('redirectOverlay').style.display = 'none';
        }

        // Hiển thị overlay sau khi in
        window.onafterprint = function() {
            // Hiển thị overlay chuyển hướng
            document.getElementById('redirectOverlay').style.display = 'flex';

            // Bắt đầu đếm ngược
            countdownInterval = setInterval(function() {
                countdown--;
                document.getElementById('countdown').textContent = countdown;

                if (countdown <= 0) {
                    redirectNow();
                }
            }, 1000);
        };

        // Fallback: nếu onafterprint không hoạt động, sử dụng setTimeout
        setTimeout(function() {
            // Kiểm tra nếu đang ở chế độ màn hình (không phải print preview)
            if (!window.matchMedia('print').matches && document.hasFocus()) {
                // Chờ thêm 2 giây rồi hiển thị overlay
                setTimeout(function() {
                    if (!document.getElementById('redirectOverlay').style.display ||
                        document.getElementById('redirectOverlay').style.display === 'none') {
                        document.getElementById('redirectOverlay').style.display = 'flex';

                        countdownInterval = setInterval(function() {
                            countdown--;
                            document.getElementById('countdown').textContent = countdown;

                            if (countdown <= 0) {
                                redirectNow();
                            }
                        }, 1000);
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
    </script>
</body>

</html>
