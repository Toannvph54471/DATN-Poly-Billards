<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-clock mr-3"></i>Máy Chấm Công QR</h1>
                <p class="text-gray-600 mt-2">Hệ thống chấm công tự động</p>
            </div>
            <a href="/admin/dashboard" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">Quay lại Dashboard</a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-green-600 border-b pb-2">Quét Mã QR Check-in / Check-out</h2>

            <!-- Camera Area -->
            <div class="relative">
                <div id="reader" class="w-full bg-black rounded-lg overflow-hidden" style="min-height: 400px;"></div>
                
                <!-- Status Overlay -->
                <div id="scan-status" class="absolute top-4 left-4 right-4 hidden">
                    <div class="bg-blue-500 text-white px-4 py-2 rounded shadow flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...
                    </div>
                </div>
            </div>

            <div class="mt-4 flex gap-4">
                <button onclick="startCamera()" id="btn-start-cam" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold text-lg shadow">
                    <i class="fas fa-camera mr-2"></i>Bật Camera
                </button>
                <button onclick="stopCamera()" id="btn-stop-cam" class="hidden flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-bold text-lg shadow">
                    <i class="fas fa-stop mr-2"></i>Tắt Camera
                </button>
            </div>

            <!-- Logs / Output -->
            <div class="mt-6">
                <h3 class="font-bold text-gray-700 mb-2">Nhật Ký Quét:</h3>
                <div id="scan-log" class="h-48 overflow-y-auto bg-gray-900 text-green-400 p-4 rounded font-mono text-sm border border-gray-700 shadow-inner">
                    <div class="text-gray-500 italic">Chưa có lượt quét nào...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrCode;
        let isProcessing = false;

        function log(msg, type = 'info') {
            const logDiv = document.getElementById('scan-log');
            const time = new Date().toLocaleTimeString();
            let color = 'text-green-400';
            if (type === 'error') color = 'text-red-400';
            if (type === 'warning') color = 'text-yellow-400';
            if (type === 'success') color = 'text-blue-400';
            
            // Remove initial placeholder
            if (logDiv.querySelector('.text-gray-500')) {
                logDiv.innerHTML = '';
            }

            const entry = document.createElement('div');
            entry.className = `${color} mb-1 border-b border-gray-800 pb-1`;
            entry.innerHTML = `<span class="text-gray-600">[${time}]</span> ${msg}`;
            
            logDiv.insertBefore(entry, logDiv.firstChild);
        }

        function startCamera() {
            if(html5QrCode) {
                // If already instance, just start
                startScanning();
                return;
            }
            
            html5QrCode = new Html5Qrcode("reader");
            startScanning();
        }

        function startScanning() {
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                document.getElementById('btn-start-cam').classList.add('hidden');
                document.getElementById('btn-stop-cam').classList.remove('hidden');
                log('Camera đã bật. Vui lòng đưa mã QR vào khung hình.');
            })
            .catch(err => {
                log('Không thể bật camera: ' + err, 'error');
                Swal.fire('Lỗi Camera', 'Không thể truy cập camera. Vui lòng kiểm tra quyền truy cập.', 'error');
            });
        }

        function stopCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('btn-start-cam').classList.remove('hidden');
                    document.getElementById('btn-stop-cam').classList.add('hidden');
                    log('Camera đã tắt.');
                }).catch(err => {
                    console.log(err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            
            log(`Phát hiện mã QR: ${decodedText.substring(0, 10)}...`, 'info');
            
            // Pause scanning visually
            html5QrCode.pause();
            document.getElementById('scan-status').classList.remove('hidden');

            processScan(decodedText);
        }

        function processScan(token) {
            // Try Check-in first
            fetch('/api/attendance/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_token: token })
            })
            .then(res => {
                if (res.status === 403) {
                    return res.json().then(data => {
                        if (data.status === 'LATE_REASON_REQUIRED') {
                            handleLateReason(token, data.message);
                            throw new Error('LATE_REASON_REQUIRED');
                        }
                        // Other 403 errors (e.g. no shift)
                        throw new Error(data.message || 'Forbidden');
                    });
                }
                // If 400 (already checked in), try Check-out
                if (res.status === 400) {
                    return res.json().then(data => {
                        if (data.message && data.message.includes('đã check-in')) {
                            log('Đã check-in, chuyển sang check-out...', 'warning');
                            return fetch('/api/attendance/check-out', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ qr_token: token })
                            }).then(r => {
                                if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'Lỗi Check-out') });
                                return r.json();
                            });
                        }
                        throw new Error(data.message || 'Lỗi Check-in');
                    });
                }
                
                if (!res.ok) {
                    return res.json().then(d => { throw new Error(d.message || 'Lỗi hệ thống') });
                }

                return res.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    log(`THÀNH CÔNG: ${data.message}`, 'success');
                    Swal.fire({
                        title: 'Thành công!',
                        text: data.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                if (err.message !== 'LATE_REASON_REQUIRED') {
                    log(`LỖI: ${err.message}`, 'error');
                    Swal.fire('Lỗi', err.message, 'error');
                }
            })
            .finally(() => {
                // Resume scanning after delay
                document.getElementById('scan-status').classList.add('hidden');
                setTimeout(() => {
                    isProcessing = false;
                    html5QrCode.resume();
                }, 3000);
            });
        }

        function handleLateReason(token, message) {
            Swal.fire({
                title: 'Đi Muộn',
                text: message,
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Nhập lý do đi muộn...',
                showCancelButton: true,
                confirmButtonText: 'Gửi Lý Do',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#d33',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Bạn cần nhập lý do để được duyệt!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitLateReason(token, result.value);
                } else {
                    // Cancelled
                    log('Đã hủy nhập lý do đi muộn.', 'warning');
                }
            });
        }

        function submitLateReason(token, reason) {
            log('Đang gửi lý do đi muộn...', 'info');
            fetch('/api/attendance/submit-late-reason', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_token: token, reason: reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    log(`ĐÃ GỬI: ${data.message}`, 'success');
                    Swal.fire('Đã Gửi', data.message, 'success');
                } else {
                    log(`LỖI GỬI LÝ DO: ${data.message}`, 'error');
                    Swal.fire('Lỗi', data.message, 'error');
                }
            })
            .catch(err => log(`Lỗi kết nối: ${err.message}`, 'error'));
        }
    </script>
</body>
</html>
