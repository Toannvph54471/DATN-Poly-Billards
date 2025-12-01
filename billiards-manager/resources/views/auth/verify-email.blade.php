@extends('layouts.auth')

@section('title', 'Xác thực email - Poly Billiards')

@section('content')
    <div class="mb-6 text-sm text-gray-600 text-center">
        {{ __('Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email bằng cách nhấp vào liên kết chúng tôi vừa gửi qua email. Nếu bạn không nhận được email, chúng tôi sẽ gửi lại một email khác.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
            {{ __('Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn đã cung cấp khi đăng ký.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md">
                {{ __('Gửi lại email xác thực') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="text-blue-600 hover:text-blue-800 font-medium py-3 px-6 rounded-lg transition duration-200">
                {{ __('Đăng xuất') }}
            </button>
        </form>
    </div>
@endsection