@extends('layouts.auth')

@section('title', 'Đăng nhập - Billiard Club')

@section('content')
    <h2 class="text-2xl font-bold text-center text-amber-600 mb-6">Đăng nhập</h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
            <input type="password" name="password" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                   required autocomplete="current-password">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mb-4">
            <input type="checkbox" name="remember" id="remember" class="rounded text-amber-600">
            <label for="remember" class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</label>
        </div>

        <div class="flex items-center justify-between mb-6">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-amber-600 hover:underline">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-semibold hover:bg-amber-700 transition shadow-md">
            Đăng nhập
        </button>

    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-amber-600 font-medium hover:underline">Đăng ký ngay</a>
    </p>
@endsection