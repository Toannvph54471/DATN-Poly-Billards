<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin chuyển khoản</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bank-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .qr-placeholder {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .copy-btn {
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: #3b82f6;
            color: white;
        }

        .copied {
            background: #10b981 !important;
            color: white !important;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bank-card w-full max-w-2xl p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-university text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Thông tin chuyển khoản</h1>
            <p class="text-gray-600">Vui lòng chuyển khoản theo thông tin bên dưới</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- QR Code (Mock) -->
            <div class="text-center">
                <div class="qr-placeholder mx-auto mb-4">
                    <div class="text-center">
                        <i class="fas fa-qrcode text-gray-400 text-6xl mb-3"></i>
                        <p class="text-sm text-gray-500">QR Code thanh toán</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Quét mã để thanh toán nhanh</p>
            </div>

            <!-- Bank Info -->
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-1">Ngân hàng</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-lg text-gray-900">Vietcombank (VCB)</p>
                        <button onclick="copyText('Vietcombank')" class="copy-btn px-3 py-1 bg-gray-200 rounded-lg text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-1">Số tài khoản</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-lg text-gray-900 font-mono">1234567890</p>
                        <button onclick="copyText('1234567890')" class="copy-btn px-3 py-1 bg-gray-200 rounded-lg text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-1">Chủ tài khoản</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-lg text-gray-900">POLY BILLIARDS</p>
                        <button onclick="copyText('POLY BILLIARDS')" class="copy-btn px-3 py-1 bg-gray-200 rounded-lg text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-4 border-2 border-blue-200">
                    <p class="text-sm text-gray-600 mb-1">Nội dung chuyển khoản</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-lg text-blue-900 font-mono">{{ $transactionId }}</p>
                        <button onclick="copyText('{{ $transactionId }}')" class="copy-btn px-3 py-1 bg-blue-200 rounded-lg text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="mt-8 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-yellow-600 text-xl mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="font-bold text-yellow-900 mb-3">Lưu ý quan trọng:</p>
                    <ul class="space-y-2 text-sm text-yellow-800">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                            <span>Vui lòng chuyển khoản <strong>chính xác số tiền</strong> và <strong>nội dung</strong> để đơn hàng được xử lý tự động</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                            <span>Sau khi chuyển khoản thành công, vui lòng chờ <strong>2-5 phút</strong> để hệ thống xác nhận</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-1 text-yellow-600"></i>
                            <span>Nếu sau 10 phút chưa nhận được xác nhận, vui lòng liên hệ hotline: <strong>0901234567</strong></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mock Test Note -->
        <div class="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <div>
                    <p class="font-semibold text-blue-900">Chế độ test (Mock)</p>
                    <p class="text-sm text-blue-800">Trong môi trường phát triển, bạn không cần chuyển khoản thực tế. Thông tin này chỉ để demo.</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('reservations.track') }}" 
               class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-4 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
            <button onclick="simulatePayment()" 
                    class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 rounded-xl transition">
                <i class="fas fa-check-circle mr-2"></i>Xác nhận đã chuyển khoản (Test)
            </button>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="toast" class="hidden fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toastMessage">Đã copy!</span>
    </div>

    <script>
        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Đã copy: ' + text);
                
                // Visual feedback
                event.target.classList.add('copied');
                event.target.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                
                setTimeout(() => {
                    event.target.classList.remove('copied');
                    event.target.innerHTML = '<i class="fas fa-copy mr-1"></i>Copy';
                }, 2000);
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        function simulatePayment() {
            if (confirm('Xác nhận bạn đã chuyển khoản thành công?\n\n(Trong môi trường test, hệ thống sẽ tự động xác nhận thanh toán)')) {
                showToast('Đang xử lý...');
                
                // Giả lập xử lý thanh toán
                setTimeout(() => {
                    window.location.href = '{{ route("reservations.track") }}?payment=success';
                }, 2000);
            }
        }
    </script>
</body>
</html>