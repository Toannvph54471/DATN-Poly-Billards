@extends('layouts.customer')

@section('title', 'Quét mã Check-in')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col items-center justify-center">

            <h2 class="text-2xl font-bold mb-6 text-elegant-navy">Quét mã Check-in</h2>

            <!-- Thông tin nhân viên -->
            <div class="w-full mb-6 text-center">
                @if(Auth::user()->employee)
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <p class="text-lg font-medium text-gray-900">
                            Xin chào, <span class="text-blue-600">{{ Auth::user()->name }}</span>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Mã NV: <span class="font-mono font-bold text-lg">{{ Auth::user()->employee->employee_code }}</span>
                        </p>
                    </div>
                    <input type="hidden" id="employee_code" value="{{ Auth::user()->employee->employee_code }}">
                @else
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">Lỗi!</span> Tài khoản chưa liên kết hồ sơ nhân viên.
                    </div>
                @endif
            </div>

            <!-- Chọn camera (chỉ hiện khi có nhiều camera) -->
            <div class="w-full mb-4" id="camera-select-container" style="display: none;">
                <label for="camera-select" class="block text-sm font-medium text-gray-700 mb-2">Chọn camera:</label>
                <select id="camera-select" class="block w-full px-4 py-2 text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </select>
            </div>

            <!-- Khu vực quét mã -->
            <div class="w-full relative rounded-xl overflow-hidden shadow-2xl bg-black" style="min-height: 380px;">
                <div id="reader" class="w-full h-full"></div>

                <!-- Loading -->
                <div id="scanner-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-90 text-white z-10">
                    <svg class="animate-spin h-12 w-12 mb-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg font-medium">Đang mở camera...</p>
                    <p class="text-sm text-gray-300 mt-2">Vui lòng cho phép truy cập camera</p>
                </div>

                <!-- Lỗi -->
                <div id="scanner-error" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-90 text-white z-20 hidden p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Không thể mở camera</h3>
                    <p id="error-message" class="text-gray-300 mb-6">Vui lòng kiểm tra quyền truy cập và thử lại</p>
                    <button onclick="location.reload()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition">
                        Thử lại
                    </button>
                </div>
            </div>

            <!-- Kết quả check-in -->
            <div id="result-message" class="mt-6 p-5 rounded-lg hidden w-full text-center text-lg font-medium transition-all duration-300"></div>

            <!-- Nút quay lại -->
            <a href="{{ route('home') }}" class="mt-8 text-sm text-gray-500 hover:text-elegant-gold transition flex items-center">
                Quay lại trang chủ
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let isScanning = false;
    let currentCameraId = null;

    function showLoading() {
        document.getElementById('scanner-loading').classList.remove('hidden');
        document.getElementById('scanner-error').classList.add('hidden');
    }

    function hideLoading() {
        document.getElementById('scanner-loading').classList.add('hidden');
    }

    function showError(msg) {
        hideLoading();
        document.getElementById('scanner-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = msg;
    }

    async function startScanner() {
        showLoading();

        if (!window.isSecureContext) {
            showError("Yêu cầu kết nối HTTPS để sử dụng camera");
            return;
        }

        try {
            const devices = await Html5Qrcode.getCameras();
            if (!devices || devices.length === 0) {
                showError("Không tìm thấy camera trên thiết bị");
                return;
            }

            const cameraSelect = document.getElementById('camera-select');
            const container = document.getElementById('camera-select-container');

            // Ưu tiên camera sau (back)
            let preferredCamera = devices[0];
            const backCamera = devices.find(d => 
                d.label.toLowerCase().includes('back') || 
                d.label.toLowerCase().includes('sau') || 
                d.label.toLowerCase().includes('environment')
            );
            if (backCamera) preferredCamera = backCamera;

            // Hiển thị dropdown nếu có >1 camera
            if (devices.length > 1) {
                devices.forEach(device => {
                    const opt = new Option(device.label || `Camera ${device.id.slice(0,4)}...`, device.id);
                    cameraSelect.appendChild(opt);
                });
                container.style.display = 'block';
                cameraSelect.value = preferredCamera.id;
            }

            currentCameraId = preferredCamera.id;

            // Xử lý đổi camera
            cameraSelect.addEventListener('change', (e) => {
                currentCameraId = e.target.value;
                restartScanner();
            });

            await initCamera(currentCameraId);

        } catch (err) {
            showError("Lỗi truy cập camera: " + err.message);
        }
    }

    async function initCamera(cameraId) {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = {
            fps: 10,
            qrbox: { width: 260, height: 260 },
            aspectRatio: 1,
            showZoomSliderIfSupported: true,
            showTorchButtonIfSupported: true,
            defaultZoomValueIfSupported: 2
        };

        try {
            await html5QrCode.start(
                cameraId,
                config,
                onScanSuccess,
                () => {} // ignore scan errors
            );
            hideLoading();
            isScanning = true;
        } catch (err) {
            let msg = "Không thể mở camera";
            if (err.name === 'NotAllowedError') msg = "Bạn đã chặn quyền truy cập camera";
            else if (err.name === 'NotFoundError') msg = "Không tìm thấy camera";
            else if (err.name === 'NotReadableError') msg = "Camera đang bị ứng dụng khác sử dụng";

            showError(msg);
        }
    }

    function restartScanner() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                initCamera(currentCameraId);
            });
        }
    }

    function onScanSuccess(decodedText) {
        if (!isScanning) return;
        isScanning = false;
        html5QrCode.pause();

        const employeeCode = document.getElementById('employee_code')?.value;
        if (!employeeCode) {
            alert("Không tìm thấy mã nhân viên");
            return;
        }

        fetch('/api/attendance/qr-checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                qr_token: decodedText,
                employee_code: employeeCode
            })
        })
        .then(r => r.json())
        .then(data => {
            const msgDiv = document.getElementById('result-message');
            msgDiv.classList.remove('hidden');

            if (data.status === 'success') {
                msgDiv.className = 'mt-6 p-5 rounded-lg w-full text-center bg-green-100 text-green-800 border-2 border-green-500 text-xl font-bold';
                msgDiv.innerHTML = `${data.message}`;
                
                // Dừng hẳn camera + hiện icon thành công
                html5QrCode.stop();
                document.getElementById('reader').innerHTML = '<div class="flex items-center justify-center h-full"><i class="fas fa-check-circle text-9xl text-green-500"></i></div>';
            } else {
                msgDiv.className = 'mt-6 p-5 rounded-lg w-full text-center bg-red-100 text-red-800 border-2 border-red-500 font-bold';
                msgDiv.innerHTML = `Lỗi: ${data.message}`;

                setTimeout(() => {
                    msgDiv.classList.add('hidden');
                    isScanning = true;
                    html5QrCode.resume();
                }, 3000);
            }
        })
        .catch(() => {
            alert("Lỗi kết nối server");
            isScanning = true;
            html5QrCode.resume();
        });
    }

    // Khởi động khi load trang
    document.addEventListener('DOMContentLoaded', startScanner);
</script>
@endsection