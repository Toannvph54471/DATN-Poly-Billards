<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Máy Chấm Công QR</title>

    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Thư viện QR mới nhất (tự động lấy latest) + SweetAlert2 -->
    <script src="https://unpkg.com/html5-qrcode@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 min-h-screen p-4 md:p-8">

    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-qrcode mr-3"></i>Máy Chấm Công QR</h1>
                <p class="text-gray-600 mt-1">Quét mã để check-in / check-out tự động</p>
            </div>
            <a href="/admin/dashboard"
                class="px-5 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-4">
                <h2 class="text-xl font-bold"><i class="fas fa-camera mr-2"></i>Quét Mã QR Check-in / Check-out</h2>
            </div>

            <div class="p-6">
                <!-- Khu vực camera -->
                <div class="relative bg-black rounded-xl overflow-hidden mb-6">
                    <div id="reader" class="w-full" style="min-height: 400px;"></div>

                    <!-- Overlay trạng thái -->
                    <div id="scan-status"
                        class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden">
                        <div class="text-white text-xl font-bold animate-pulse">
                            <i class="fas fa-spinner fa-spin mr-3"></i>Đang xử lý...
                        </div>
                    </div>
                </div>

                <!-- Nút điều khiển camera -->
                <div class="flex gap-4 mb-6">
                    <button onclick="startCamera()" id="btn-start-cam"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg transition">
                        <i class="fas fa-camera mr-2"></i>Bật Camera
                    </button>
                    <button onclick="stopCamera()" id="btn-stop-cam"
                        class="hidden flex-1 bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg transition">
                        <i class="fas fa-stop mr-2"></i>Tắt Camera
                    </button>
                </div>

                <!-- Nhật ký quét -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-list mr-2"></i>Nhật Ký Quét
                    </h3>
                    <div id="scan-log"
                        class="h-56 overflow-y-auto bg-gray-900 text-green-400 p-4 rounded-xl font-mono text-sm border border-gray-700 shadow-inner">
                        <div class="text-gray-500 italic">Nhấn "Bật Camera" để bắt đầu...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let scanner = null;
        let isProcessing = false;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Hàm ghi log
        function log(message, type = 'info') {
            const logDiv = document.getElementById('scan-log');
            const time = new Date().toLocaleTimeString('vi-VN');
            let color = 'text-green-400';
            if (type === 'error') color = 'text-red-400';
            if (type === 'warning') color = 'text-yellow-400';
            if (type === 'success') color = 'text-cyan-400';

            if (logDiv.querySelector('.text-gray-500')) logDiv.innerHTML = '';

            const entry = document.createElement('div');
            entry.className = `${color} mb-2 border-b border-gray-800 pb-1`;
            entry.innerHTML = `<span class="text-gray-500">[${time}]</span> ${message}`;
            logDiv.insertBefore(entry, logDiv.firstChild);
        }

        // Bắt đầu camera
        function startCamera() {
            if (scanner) {
                scanner.startScanning();
                return;
            }

            scanner = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: (viewWidth, viewHeight) => {
                    const size = Math.min(viewWidth, viewHeight) * 0.7;
                    return {
                        width: size,
                        height: size
                    };
                },
                aspectRatio: 1,
                facingMode: "environment" // camera sau (điện thoại)
            };

            scanner.start({
                    facingMode: "environment"
                }, config, onScanSuccess, onScanError)
                .then(() => {
                    document.getElementById('btn-start-cam').classList.add('hidden');
                    document.getElementById('btn-stop-cam').classList.remove('hidden');
                    log('Camera đã bật thành công! Đưa mã QR vào khung', 'success');
                })
                .catch(err => {
                    log('Lỗi bật camera: ' + err, 'error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Không thể bật camera',
                        text: 'Vui lòng kiểm tra: HTTPS, quyền camera, và dùng Chrome/Firefox',
                        footer: 'Gợi ý: dùng http://127.0.0.1:8000 hoặc ngrok'
                    });
                });
        }

        function onScanError(err) {
            // Không log lỗi liên tục (chỉ log khi cần)
            // console.warn(err);
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            log(`Đã quét: ${decodedText.substring(0, 20)}...`, 'warning');
            document.getElementById('scan-status').classList.remove('hidden');
            scanner.pause();

            processQR(decodedText);
        }

        async function processQR(token) {
            try {
                // Thử Check-in trước
                let response = await fetch('/api/attendance/check-in', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        qr_token: token
                    })
                });

                let data = await response.json();

                // Nếu đã check-in → tự động check-out
                if (response.status === 400 && data.message?.includes('đã check-in')) {
                    log('Đã check-in → Thử check-out...', 'warning');
                    response = await fetch('/api/attendance/check-out', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            qr_token: token
                        })
                    });
                    data = await response.json();
                }

                // Cần lý do đi muộn
                if (response.status === 403 && data.status === 'LATE_REASON_REQUIRED') {
                    await askLateReason(token, data.message);
                    return;
                }

                if (!response.ok) throw new Error(data.message || 'Lỗi không xác định');

                log(data.message, 'success');
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });

            } catch (err) {
                if (err.message !== 'LATE_REASON_REQUIRED') {
                    log('Lỗi: ' + err.message, 'error');
                    Swal.fire('Lỗi', err.message, 'error');
                }
            } finally {
                document.getElementById('scan-status').classList.add('hidden');
                setTimeout(() => {
                    isProcessing = false;
                    scanner?.resume();
                }, 3000);
            }
        }

        async function askLateReason(token, message) {
            const {
                value: reason
            } = await Swal.fire({
                icon: 'warning',
                title: 'Đi muộn',
                text: message,
                input: 'textarea',
                inputPlaceholder: 'Vui lòng nhập lý do đi muộn...',
                showCancelButton: true,
                confirmButtonText: 'Gửi lý do',
                cancelButtonText: 'Hủy',
                inputValidator: (value) => !value ? 'Bạn phải nhập lý do!' : null
            });

            if (reason) {
                log('Đang gửi lý do đi muộn...', 'info');
                try {
                    const res = await fetch('/api/attendance/submit-late-reason', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            qr_token: token,
                            reason
                        })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        log(data.message, 'success');
                        Swal.fire('Đã gửi', data.message, 'success');
                    } else {
                        throw new Error(data.message);
                    }
                } catch (e) {
                    log('Gửi lý do thất bại: ' + e.message, 'error');
                }
            } else {
                log('Đã hủy nhập lý do đi muộn', 'warning');
            }

            // Dù có gửi hay không, vẫn resume sau 3s
            setTimeout(() => {
                isProcessing = false;
                scanner?.resume();
            }, 3000);
        }

        function stopCamera() {
            if (scanner) {
                scanner.stop().then(() => {
                    document.getElementById('btn-start-cam').classList.remove('hidden');
                    document.getElementById('btn-stop-cam').classList.add('hidden');
                    log('Camera đã tắt');
                });
            }
        }

        // Tự động bật camera khi vào trang (tùy chọn)
        // window.onload = startCamera;
    </script>
</body>

</html>
