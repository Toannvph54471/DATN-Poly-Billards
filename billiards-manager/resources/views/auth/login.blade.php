@extends('layouts.auth')

@section('title', 'Đăng nhập - Poly Billiards')

@section('content')
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Đăng nhập</h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 text-center bg-green-50 p-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                       required autocomplete="current-password" placeholder="••••••••">
                <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i id="password-eye" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="rounded text-blue-600 focus:ring-blue-500">
                <label for="remember" class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md transform hover:scale-105">
            Đăng nhập
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:text-blue-800 transition">Đăng ký ngay</a>
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