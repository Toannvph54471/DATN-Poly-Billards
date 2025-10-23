{{-- resources/views/auth/confirm-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Xác nhận mật khẩu - Billiard Club')

@section('content')
    <h2 class="text-2xl font-bold text-center text-amber-600 mb-6">Xác nhận mật khẩu</h2>

    <p class="text-center text-gray-600 mb-6 text-sm">
        Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu để tiếp tục.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Mật khẩu -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
            <input type="password" name="password"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                   required autocomplete="current-password" autofocus>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-semibold hover:bg-amber-700 transition shadow-md">
            Xác nhận
        </button>

        <p class="text-center text-sm text-gray-600 mt-4">
            <a href="{{ route('dashboard') }}" class="text-amber-600 font-medium hover:underline">
                ← Quay lại trang chủ
            </a>
        </p>
    </form>
@endsection