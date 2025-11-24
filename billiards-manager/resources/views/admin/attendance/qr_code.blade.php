@extends('admin.layouts.app')

@section('title', 'Mã QR Check-in')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="flex flex-col items-center justify-center min-h-[60vh]">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Quét mã để Check-in</h2>
            <p class="mb-6 text-gray-500 dark:text-gray-400">Mã sẽ tự động làm mới sau mỗi 30 giây</p>
            
            <div class="bg-white p-4 rounded-lg shadow-lg">
                <!-- Image for QR Code -->
                <img id="qr-image" src="" alt="QR Code" class="w-[300px] h-[300px] mx-auto" style="display: none;">
                <div id="qr-loading" class="w-[300px] h-[300px] mx-auto flex items-center justify-center bg-gray-100 text-gray-400">
                    <i class="fas fa-spinner fa-spin text-3xl"></i>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="mt-4 text-red-500 font-medium hidden"></div>

            <!-- Timer -->
            <div class="mt-6 text-center w-full max-w-xs">
                <p class="text-sm text-gray-500">Thời gian còn lại: <span id="timer" class="font-bold text-blue-600">30</span>s</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-2">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-1000" style="width: 100%"></div>
                </div>
            </div>

            <!-- Debug Toggle -->
            <button onclick="document.getElementById('debug-info').classList.toggle('hidden')" class="mt-8 text-xs text-gray-400 hover:text-gray-600 underline">
                Hiện thông tin Debug
            </button>
            <div id="debug-info" class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-600 hidden max-w-md break-all">
                Waiting for data...
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    let timeLeft = 30;
    let timerInterval;
    const debugDiv = document.getElementById('debug-info');
    const errorDiv = document.getElementById('error-message');
    const qrImage = document.getElementById('qr-image');
    const qrLoading = document.getElementById('qr-loading');

    function fetchQrToken() {
        debugDiv.textContent = "Fetching token...";
        
        fetch('/api/attendance/qr-token')
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP Error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                errorDiv.classList.add('hidden');
                debugDiv.textContent = "Token received: " + data.token;
                
                // Update Image Source using external API
                // Using qrserver.com API which is fast and reliable
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${data.token}&bgcolor=ffffff`;
                
                qrImage.onload = function() {
                    qrImage.style.display = 'block';
                    qrLoading.style.display = 'none';
                };
                qrImage.src = qrUrl;
                
                // Reset timer
                timeLeft = 30;
                updateTimer();
            })
            .catch(err => {
                console.error('Error:', err);
                errorDiv.textContent = "Lỗi kết nối: " + err.message;
                errorDiv.classList.remove('hidden');
                debugDiv.textContent = "Error: " + err.message;
                
                // Retry in 5s
                setTimeout(fetchQrToken, 5000);
            });
    }

    function updateTimer() {
        const timerDisplay = document.getElementById('timer');
        const progressBar = document.getElementById('progress-bar');
        
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = timeLeft;
            progressBar.style.width = (timeLeft / 30 * 100) + '%';

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                fetchQrToken();
            }
        }, 1000);
    }

    // Initial load
    fetchQrToken();
</script>
@endsection
@endsection
