@extends('layouts.auth')

@section('title', 'Đăng ký - Poly Billiards')

@section('content')
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Tạo tài khoản mới</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Họ tên</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required autofocus placeholder="Nguyễn Văn A">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required placeholder="you@example.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Số điện thoại</label>
            <input type="text" name="phone" value="{{ old('phone') }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required placeholder="0912345678">
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="password-eye" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Nhập lại mật khẩu</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" onclick="togglePassword('password_confirmation')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="password_confirmation-eye" class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md transform hover:scale-105">
            Đăng ký
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-800 transition">Đăng nhập</a>
    </p>

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