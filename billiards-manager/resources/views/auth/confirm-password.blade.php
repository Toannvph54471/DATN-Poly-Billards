@extends('layouts.auth')

@section('title', 'Xác nhận mật khẩu - Poly Billiards')

@section('content')
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Xác nhận mật khẩu</h2>

    <p class="text-center text-gray-600 mb-6 text-sm">
        Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu để tiếp tục.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Mật khẩu -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                       required autocomplete="current-password" autofocus placeholder="••••••••">
                <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="password-eye" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md transform hover:scale-105">
            Xác nhận
        </button>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="{{ route('dashboard') }}" class="text-blue-600 font-medium hover:text-blue-800 transition">
                ← Quay lại trang chủ
            </a>
        </p>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-eye');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endsection