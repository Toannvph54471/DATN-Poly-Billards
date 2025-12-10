@extends('layouts.customer')

@section('title', 'Mã QR Cá Nhân')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white text-center">Mã QR Chấm Công</h2>
        </div>
        
        <div class="p-6 flex flex-col items-center">
            <div class="mb-6 text-center">
                <p class="text-gray-600 mb-2">Xin chào, <span class="font-bold text-gray-800">{{ $employee->name }}</span></p>
                <p class="text-sm text-gray-500">Sử dụng mã QR này để check-in/check-out tại máy chấm công.</p>
            </div>
            
            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-6 flex justify-center">
                <!-- QR Code Display -->
                <div id="qrcode" class="p-2"></div>
            </div>
            
            <div class="text-center text-sm text-gray-500">
                <p>Mã sẽ hết hạn sau: <span class="font-medium text-red-500" id="countdown">02:00</span></p>
                <p class="mt-2">Mã tự động làm mới sau khi hết hạn.</p>
            </div>

            <button onclick="window.location.reload()" class="mt-6 w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-sync-alt mr-2"></i>Làm mới mã
            </button>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code
    const token = "{{ $employee->qr_token }}";
    const qrcodeContainer = document.getElementById("qrcode");
    
    if (token) {
        new QRCode(qrcodeContainer, {
            text: token,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    } else {
        qrcodeContainer.innerHTML = "<p class='text-red-500'>Không tìm thấy token.</p>";
    }

    // Simple countdown timer
    let duration = 120; // 2 minutes
    const display = document.querySelector('#countdown');
    
    const timer = setInterval(function () {
        let minutes = parseInt(duration / 60, 10);
        let seconds = parseInt(duration % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--duration < 0) {
            clearInterval(timer);
            window.location.reload();
        }
    }, 1000);
</script>
@endsection
@endsection
