@extends('layouts.auth')

@section('title', 'Quên mật khẩu - Poly Billiards')

@section('content')
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Quên mật khẩu?</h2>
    
    <p class="text-center text-gray-600 mb-6 text-sm">
        Đừng lo! Chỉ cần nhập email, chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   required autofocus placeholder="you@example.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md transform hover:scale-105">
            Gửi link đặt lại
        </button>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-800 transition">
                ← Quay lại đăng nhập
            </a>
        </p>
    </form>
@endsection