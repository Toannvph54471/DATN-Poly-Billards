<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - {{ strtoupper($gateway) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gateway-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .payment-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.4);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .processing {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="payment-card w-full max-w-md p-8">
        <!-- Gateway Logo -->
        <div class="text-center mb-6">
            <div class="gateway-logo mx-auto mb-4">
                @if($gateway === 'vnpay')
                    <i class="fas fa-credit-card text-blue-600 text-4xl"></i>
                @elseif($gateway === 'momo')
                    <i class="fas fa-wallet text-pink-600 text-4xl"></i>
                @elseif($gateway === 'zalopay')
                    <i class="fas fa-mobile-alt text-cyan-600 text-4xl"></i>
                @else
                    <i class="fas fa-university text-green-600 text-4xl"></i>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ strtoupper($gateway) }}
            </h1>
            <p class="text-gray-600 text-sm mt-1">Mock Payment Gateway - Test Mode</p>
        </div>

        <!-- Payment Info -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-6 mb-6">
            <div class="info-row">
                <span class="text-gray-600 font-medium">Mã giao dịch:</span>
                <span class="font-bold text-gray-900">{{ $transactionId }}</span>
            </div>
            <div class="info-row">
                <span class="text-gray-600 font-medium">Loại:</span>
                <span class="font-semibold text-blue-600">
                    {{ $type === 'reservation' ? 'Đặt bàn' : 'Thanh toán bill' }}
                </span>
            </div>
            <div class="info-row">
                <span class="text-gray-600 font-medium">Số tiền:</span>
                <span class="text-3xl font-bold text-green-600">
                    {{ number_format($amount) }}đ
                </span>
            </div>
        </div>

        <!-- Warning -->
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="font-semibold text-yellow-900 mb-1">Chế độ thử nghiệm</p>
                    <p class="text-sm text-yellow-800">
                        Đây là giao diện thanh toán giả lập cho môi trường phát triển. 
                        Không có giao dịch thực tế nào được thực hiện.
                    </p>
                </div>
            </div>
        </div>

        <!-- Mock Payment Instructions -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="text-sm font-semibold text-gray-900 mb-2">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>Hướng dẫn test:
            </p>
            <ul class="text-sm text-gray-700 space-y-1 ml-6">
                <li>• Nhấn <strong>"Thanh toán thành công"</strong> để giả lập thanh toán thành công</li>
                <li>• Nhấn <strong>"Thanh toán thất bại"</strong> để giả lập lỗi thanh toán</li>
                <li>• Kết quả sẽ được lưu vào database như thanh toán thực tế</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <form action="{{ route('mock.payment.process') }}" method="POST" id="paymentForm">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="transaction_id" value="{{ $transactionId }}">
            <input type="hidden" name="payment_id" value="{{ $paymentId }}">
            <input type="hidden" name="action" id="action" value="">

            <div class="space-y-3">
                <button type="button" onclick="submitPayment('success')"
                        class="w-full btn-success text-white font-bold py-4 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <span>Thanh toán thành công</span>
                </button>

                <button type="button" onclick="submitPayment('failed')"
                        class="w-full btn-danger text-white font-bold py-4 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle mr-3 text-xl"></i>
                    <span>Thanh toán thất bại</span>
                </button>

                <a href="{{ $type === 'reservation' ? route('reservations.track') : route('admin.dashboard') }}"
                   class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-4 rounded-xl transition">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </form>

        <!-- Processing Indicator -->
        <div id="processing" class="hidden mt-6 text-center">
            <div class="inline-flex items-center px-6 py-3 bg-blue-100 rounded-full">
                <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mr-3"></div>
                <span class="text-blue-800 font-medium">Đang xử lý thanh toán...</span>
            </div>
        </div>
    </div>

    <script>
        function submitPayment(action) {
            // Hiển thị loading
            document.getElementById('processing').classList.remove('hidden');
            document.querySelectorAll('button').forEach(btn => btn.disabled = true);

            // Set action value
            document.getElementById('action').value = action;

            // Submit form sau 1.5s (giả lập xử lý)
            setTimeout(() => {
                document.getElementById('paymentForm').submit();
            }, 1500);
        }

        // Prevent accidental page close
        window.addEventListener('beforeunload', function (e) {
            const processing = document.getElementById('processing');
            if (!processing.classList.contains('hidden')) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>