<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner Chấm Công - Billiards Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-900 h-screen flex flex-col items-center justify-center text-white">

    <div class="w-full max-w-md p-4">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-blue-400 mb-2">
                <i class="fas fa-qrcode mr-2"></i>Scanner Chấm Công
            </h1>
            <p class="text-gray-400 text-sm">Đưa mã QR của bạn vào khung hình để check-in/check-out</p>
        </div>

        <div class="bg-black rounded-xl overflow-hidden shadow-2xl border-2 border-gray-700 relative">
            <div id="reader" class="w-full" style="min-height: 350px;"></div>
            
            <!-- Overlay guide -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div class="w-64 h-64 border-2 border-blue-500 opacity-50 rounded-lg"></div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <div id="status-msg" class="text-gray-500 italic">Đang chờ quét...</div>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="/" class="text-gray-500 hover:text-white text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Trở về trang chủ
            </a>
        </div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            
            isProcessing = true;
            html5QrCode.pause(); // Pause scanning
            
            document.getElementById('status-msg').innerText = "Đang xử lý...";
            document.getElementById('status-msg').className = "text-yellow-400 font-bold animate-pulse";

            // Play beep sound
            const audio = new Audio('https://www.soundjay.com/button/beep-07.mp3');
            audio.play().catch(e => console.log('Audio play failed', e));

            fetch('{{ url("/api/attendance/scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_token: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: data.type === 'checkin' ? 'Check-in Thành Công!' : 'Check-out Thành Công!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#1f2937',
                        color: '#fff'
                    }).then(() => {
                        resumeScanning();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#1f2937',
                        color: '#fff'
                    }).then(() => {
                        resumeScanning();
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi Hệ Thống',
                    text: 'Không thể kết nối đến server.',
                    background: '#1f2937',
                    color: '#fff'
                }).then(() => {
                    resumeScanning();
                });
            });
        }

        function resumeScanning() {
            isProcessing = false;
            document.getElementById('status-msg').innerText = "Đang chờ quét...";
            document.getElementById('status-msg').className = "text-gray-500 italic";
            html5QrCode.resume();
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // console.warn(`Code scan error = ${error}`);
        }

        // Start scanning
        const cameraConfig = { facingMode: "environment" };
        // Fallback for laptops or devices where environment camera might fail or not exist
        // We catch the specific error below, or just remove facingMode to let browser decide if it fails.
        // For now, let's just make it simpler or retry.
        
        // Let's try default config first (user facing for laptops usually, environment for phones)
        // If we really want to force back camera but fallback, we need more logic.
        // For this "fix", removing strict requirement is safest for "emulator" on laptop.
        
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            console.log("Environment camera failed, trying user camera...", err);
            return html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanFailure);
        })
        .catch(err => {
             console.log("User camera also failed, trying without constraint...", err);
             return html5QrCode.start({ }, config, onScanSuccess, onScanFailure);
        })
        .catch(err => {
            console.error("Error starting scanner", err);
            document.getElementById('status-msg').innerText = "Không thể khởi động camera. Vui lòng cấp quyền.";
            document.getElementById('status-msg').className = "text-red-500 font-bold";
        });
    </script>
</body>
</html>
