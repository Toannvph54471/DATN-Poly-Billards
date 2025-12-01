@extends('admin.layouts.app')

@section('title', 'Mã QR Cá Nhân')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="flex flex-col items-center justify-center min-h-[60vh]">
            
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mã QR Check-in Của Bạn</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Sử dụng mã này để quét tại máy chấm công</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 relative">
                <!-- Loading State -->
                <div id="qr-loading" class="absolute inset-0 flex items-center justify-center bg-white rounded-xl z-10">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-2"></i>
                        <span class="text-sm text-gray-500">Đang tạo mã...</span>
                    </div>
                </div>

                <!-- QR Image -->
                <img id="qr-image" src="" alt="QR Code" class="w-[280px] h-[280px] mx-auto block" style="display: none;">
                
                <!-- Status Badge -->
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Đang hoạt động
                    </span>
                </div>
            </div>

            <!-- Timer & Refresh -->
            <div class="mt-8 text-center w-full max-w-xs">
                <div class="flex justify-between text-sm text-gray-500 mb-1">
                    <span>Tự động làm mới sau:</span>
                    <span id="timer" class="font-bold text-blue-600">120s</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-1000 ease-linear" style="width: 100%"></div>
                </div>
                
                <button onclick="fetchQrToken()" class="mt-4 text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-center w-full">
                    <i class="fas fa-sync-alt mr-1"></i> Làm mới ngay
                </button>
            </div>

            <!-- Instructions -->
            <div class="mt-10 max-w-md text-center text-sm text-gray-500 bg-gray-50 p-4 rounded-lg">
                <p><i class="fas fa-info-circle mr-1"></i> <strong>Lưu ý:</strong> Mã QR này chỉ có hiệu lực trong 2 phút và chỉ sử dụng được 1 lần. Sau khi quét thành công, mã sẽ tự động đổi.</p>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let timeLeft = 120;
    let timerInterval;
    const qrImage = document.getElementById('qr-image');
    const qrLoading = document.getElementById('qr-loading');
    const timerDisplay = document.getElementById('timer');
    const progressBar = document.getElementById('progress-bar');

    function fetchQrToken() {
        // Show loading if it's a manual refresh or initial load
        if (qrImage.style.display === 'none') {
            qrLoading.style.display = 'flex';
        }

        fetch('/attendance/my-token')
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    // Update QR Image
                    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${data.token}&bgcolor=ffffff`;
                    
                    // Preload image
                    const img = new Image();
                    img.onload = function() {
                        qrImage.src = qrUrl;
                        qrImage.style.display = 'block';
                        qrLoading.style.display = 'none';
                    };
                    img.src = qrUrl;

                    // Reset Timer
                    timeLeft = parseInt(data.expires_in) || 120;
                    if (timeLeft < 0) timeLeft = 0;
                    startTimer();
                }
            })
            .catch(err => {
                console.error('Error fetching token:', err);
                // Retry shortly
                setTimeout(fetchQrToken, 5000);
            });
    }

    function startTimer() {
        clearInterval(timerInterval);
        const maxTime = 120; // Base for progress bar calculation

        timerInterval = setInterval(() => {
            timeLeft--;
            
            if (timeLeft < 0) timeLeft = 0;
            
            // Update UI
            timerDisplay.textContent = timeLeft + 's';
            const percentage = (timeLeft / maxTime) * 100;
            progressBar.style.width = percentage + '%';
            
            // Color indication
            if (timeLeft < 30) {
                progressBar.classList.remove('bg-blue-600');
                progressBar.classList.add('bg-red-500');
                timerDisplay.classList.remove('text-blue-600');
                timerDisplay.classList.add('text-red-500');
            } else {
                progressBar.classList.add('bg-blue-600');
                progressBar.classList.remove('bg-red-500');
                timerDisplay.classList.add('text-blue-600');
                timerDisplay.classList.remove('text-red-500');
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                fetchQrToken();
            }
        }, 1000);
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', fetchQrToken);
</script>
@endsection
