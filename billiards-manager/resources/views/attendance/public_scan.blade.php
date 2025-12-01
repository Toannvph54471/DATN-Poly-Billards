<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in Kiosk - Billiards Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white h-screen overflow-hidden flex flex-col">

    <!-- Header -->
    <div class="p-6 text-center bg-gray-800 shadow-lg z-10">
        <h1 class="text-3xl font-bold text-blue-400 tracking-wider uppercase">
            <i class="fas fa-clock mr-3"></i>Chấm Công
        </h1>
        <p class="text-gray-400 mt-2 text-sm">Vui lòng đưa mã QR của bạn vào khung hình</p>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center relative p-4">
        
        <!-- Scanner Container -->
        <div class="relative w-full max-w-lg aspect-square bg-black rounded-3xl overflow-hidden shadow-2xl border-4 border-gray-700">
            <div id="reader" class="w-full h-full object-cover"></div>
            
            <!-- Overlay UI -->
            <div class="absolute inset-0 pointer-events-none border-[50px] border-black/50">
                <div class="w-full h-full border-4 border-blue-500/50 relative">
                    <!-- Corner markers -->
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-blue-400 -mt-1 -ml-1"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-blue-400 -mt-1 -mr-1"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-blue-400 -mb-1 -ml-1"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-blue-400 -mb-1 -mr-1"></div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="scanner-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-20">
                <i class="fas fa-circle-notch fa-spin text-5xl text-blue-500 mb-4"></i>
                <p class="text-lg font-medium">Đang khởi động camera...</p>
            </div>
        </div>

    </div>

    <!-- Status Messages (Toast) -->
    <div id="status-modal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white text-gray-900 rounded-2xl p-8 max-w-sm w-full text-center transform transition-all scale-100 shadow-2xl">
            <div id="status-icon" class="text-6xl mb-4"></div>
            <h3 id="status-title" class="text-2xl font-bold mb-2"></h3>
            <p id="status-message" class="text-gray-600 text-lg"></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-4 text-center text-gray-500 text-xs bg-gray-800">
        &copy; {{ date('Y') }} Billiards Manager System
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        let isProcessing = false;

        function showStatus(type, title, message, duration = 3000) {
            const modal = document.getElementById('status-modal');
            const icon = document.getElementById('status-icon');
            const titleEl = document.getElementById('status-title');
            const msgEl = document.getElementById('status-message');

            modal.classList.remove('hidden');
            
            if (type === 'success') {
                icon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                titleEl.className = 'text-2xl font-bold mb-2 text-green-600';
            } else {
                icon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                titleEl.className = 'text-2xl font-bold mb-2 text-red-600';
            }

            titleEl.textContent = title;
            msgEl.textContent = message;

            // Auto hide
            setTimeout(() => {
                modal.classList.add('hidden');
                isProcessing = false; // Allow scanning again
                html5QrCode.resume();
            }, duration);
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            html5QrCode.pause(); // Pause scanning while processing

            // Play beep sound (optional)
            // const audio = new Audio('/sounds/beep.mp3');
            // audio.play().catch(e => {});

            fetch('/api/attendance/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_token: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatus('success', 
                        data.type === 'checkin' ? 'Xin chào!' : 'Tạm biệt!', 
                        data.message
                    );
                } else {
                    showStatus('error', 'Lỗi Check-in', data.message || 'Mã QR không hợp lệ');
                }
            })
            .catch(err => {
                console.error(err);
                showStatus('error', 'Lỗi Hệ Thống', 'Không thể kết nối đến máy chủ');
            });
        }

        function startScanner() {
            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // Prefer back camera
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                document.getElementById('scanner-loading').classList.add('hidden');
            })
            .catch(err => {
                console.error("Error starting scanner", err);
                document.getElementById('scanner-loading').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                    <p class="text-red-400">Không thể mở camera</p>
                    <p class="text-sm text-gray-500 mt-2">${err.message}</p>
                    <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-blue-600 rounded hover:bg-blue-700">Thử lại</button>
                `;
            });
        }

        // Start immediately
        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
</body>
</html>
