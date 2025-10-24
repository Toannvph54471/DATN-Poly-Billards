{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu - Billiard Club')

@section('content')
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-amber-600">Đặt lại mật khẩu</h2>
        <p class="text-gray-600 mt-2">Nhập mật khẩu mới cho tài khoản của bạn</p>
    </div>

    <!-- Thông báo thành công -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Token ẩn -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                   required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mật khẩu mới -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu mới</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition pr-12"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" onclick="togglePassword('password', 'eye-icon-1')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="eye-icon-1" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Xác nhận mật khẩu -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nhập lại mật khẩu</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition pr-12"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="eye-icon-2" class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Nút gửi -->
        <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md transform hover:scale-105">
            Cập nhật mật khẩu
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        <a href="{{ route('login') }}" class="text-amber-600 font-medium hover:underline">
            ← Quay lại đăng nhập
        </a>
    </p>
@endsection

@section('scripts')
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
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