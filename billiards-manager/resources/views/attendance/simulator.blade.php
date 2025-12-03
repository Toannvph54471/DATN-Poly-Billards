<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- QR Code Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <!-- QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-7xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-tools mr-3"></i>Giả Lập Máy Chấm Công QR</h1>
                <p class="text-gray-600 mt-2">Công cụ test luồng Check-in / Check-out trên Localhost</p>
            </div>
            <a href="/" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">Quay lại Dashboard</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- LEFT COLUMN: GENERATOR -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-blue-600 border-b pb-2">1. Tạo Mã QR (Nhân viên)</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Nhân Viên</label>
                        <select id="employee-select" class="w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn nhân viên để test --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->employee_code }} - {{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button onclick="generateToken()" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Tạo Token Mới
                    </button>

                    <!-- Result Area -->
                    <div id="token-result" class="hidden mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                        <p class="text-sm text-gray-500 mb-2">Mã QR này có hiệu lực trong 2 phút</p>
                        
                        <div class="flex justify-center mb-4">
                            <canvas id="qr-canvas"></canvas>
                        </div>

                        <div class="bg-white p-2 rounded border font-mono text-xs break-all text-gray-600 mb-2" id="token-text">
                            <!-- Token string will appear here -->
                        </div>
                        
                        <button onclick="copyToken()" class="text-blue-600 text-sm hover:underline">
                            <i class="far fa-copy mr-1"></i>Copy Token
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: SCANNER SIMULATOR -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-green-600 border-b pb-2">2. Giả Lập Máy Quét (Scanner)</h2>

                <!-- Tabs -->
                <div class="flex space-x-4 mb-4 border-b">
                    <button onclick="switchTab('manual')" id="tab-manual" class="pb-2 border-b-2 border-green-500 font-medium text-green-600">Nhập Thủ Công</button>
                    <button onclick="switchTab('camera')" id="tab-camera" class="pb-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Camera Laptop</button>
                </div>

                <!-- Manual Mode -->
                <div id="mode-manual" class="block">
                    <p class="text-sm text-gray-600 mb-3">Dán token vừa tạo hoặc copy từ nơi khác vào đây để giả lập việc quét.</p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-token-input" placeholder="Dán chuỗi token vào đây..." class="flex-1 border p-2 rounded focus:ring-green-500 focus:border-green-500">
                        <button onclick="simulateScan()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i>Check
                        </button>
                    </div>
                </div>

                <!-- Camera Mode -->
                <div id="mode-camera" class="hidden">
                    <p class="text-sm text-gray-600 mb-3">Sử dụng webcam để quét mã QR trên màn hình điện thoại hoặc in ra.</p>
                    <div id="reader" class="w-full bg-black rounded-lg overflow-hidden" style="min-height: 300px;"></div>
                    <button onclick="startCamera()" id="btn-start-cam" class="mt-3 w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700">
                        <i class="fas fa-camera mr-2"></i>Bật Camera
                    </button>
                    <button onclick="stopCamera()" id="btn-stop-cam" class="hidden mt-3 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                        <i class="fas fa-stop mr-2"></i>Tắt Camera
                    </button>
                </div>

                <!-- Logs / Output -->
                <div class="mt-6">
                    <h3 class="font-bold text-gray-700 mb-2">Kết Quả:</h3>
                    <div id="scan-log" class="h-48 overflow-y-auto bg-gray-900 text-green-400 p-4 rounded font-mono text-sm">
                        <div class="text-gray-500">Waiting for scan...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- GENERATOR LOGIC ---
        function generateToken() {
            const empId = document.getElementById('employee-select').value;
            if (!empId) {
                alert('Vui lòng chọn nhân viên!');
                return;
            }

            fetch(`/attendance/test-token/${empId}`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('token-result').classList.remove('hidden');
                        document.getElementById('token-text').innerText = data.token;
                        
                        // Render QR
                        new QRious({
                            element: document.getElementById('qr-canvas'),
                            value: data.token,
                            size: 200
                        });

                        // Auto copy to clipboard for convenience? No, let user choose.
                        log(`Generated token for ${data.employee}`);
                    }
                })
                .catch(err => log('Error generating token: ' + err, 'error'));
        }

        function copyToken() {
            const token = document.getElementById('token-text').innerText;
            navigator.clipboard.writeText(token);
            alert('Đã copy token!');
        }

        // --- SCANNER LOGIC ---
        function switchTab(mode) {
            if (mode === 'manual') {
                document.getElementById('mode-manual').classList.remove('hidden');
                document.getElementById('mode-camera').classList.add('hidden');
                document.getElementById('tab-manual').classList.add('border-green-500', 'text-green-600');
                document.getElementById('tab-manual').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-camera').classList.remove('border-green-500', 'text-green-600');
                document.getElementById('tab-camera').classList.add('border-transparent', 'text-gray-500');
                stopCamera();
            } else {
                document.getElementById('mode-manual').classList.add('hidden');
                document.getElementById('mode-camera').classList.remove('hidden');
                document.getElementById('tab-camera').classList.add('border-green-500', 'text-green-600');
                document.getElementById('tab-camera').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-manual').classList.remove('border-green-500', 'text-green-600');
                document.getElementById('tab-manual').classList.add('border-transparent', 'text-gray-500');
            }
        }

        function simulateScan() {
            const token = document.getElementById('manual-token-input').value.trim();
            if (!token) {
                alert('Vui lòng nhập token!');
                return;
            }
            sendScanRequest(token);
        }

        function sendScanRequest(token) {
            log('Sending scan request...');
            
            fetch('/api/attendance/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // API might not need CSRF if stateless, but usually Laravel needs it for web routes. API routes usually don't unless configured.
                },
                body: JSON.stringify({ qr_token: token })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    log(`SUCCESS: ${data.message} (${data.type})`, 'success');
                } else {
                    log(`FAILED: ${data.message}`, 'error');
                }
            })
            .catch(err => {
                log(`ERROR: ${err.message}`, 'error');
            });
        }

        function log(msg, type = 'info') {
            const logDiv = document.getElementById('scan-log');
            const time = new Date().toLocaleTimeString();
            let color = 'text-green-400';
            if (type === 'error') color = 'text-red-400';
            if (type === 'success') color = 'text-blue-400';
            
            const entry = document.createElement('div');
            entry.className = `${color} mb-1 border-b border-gray-800 pb-1`;
            entry.innerHTML = `<span class="text-gray-600">[${time}]</span> ${msg}`;
            
            logDiv.insertBefore(entry, logDiv.firstChild);
        }

        // --- CAMERA LOGIC ---
        let html5QrCode;
        
        function startCamera() {
            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                document.getElementById('btn-start-cam').classList.add('hidden');
                document.getElementById('btn-stop-cam').classList.remove('hidden');
                log('Camera started.');
            })
            .catch(err => {
                log('Error starting camera: ' + err, 'error');
            });
        }

        function stopCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('btn-start-cam').classList.remove('hidden');
                    document.getElementById('btn-stop-cam').classList.add('hidden');
                    log('Camera stopped.');
                }).catch(err => {
                    console.log(err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Handle on success condition with the decoded message.
            log(`QR Code Detected! Processing...`);
            
            // Optional: Pause scanning to avoid double scans
            html5QrCode.pause();
            
            sendScanRequest(decodedText);

            // Resume after delay
            setTimeout(() => {
                html5QrCode.resume();
            }, 3000);
        }
    </script>
</body>
</html>
