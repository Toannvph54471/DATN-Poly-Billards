@extends('layouts.customer')

@section('title', 'Theo dõi đặt bàn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-elegant-navy mb-4">Theo dõi đặt bàn</h1>
            <p class="text-lg text-gray-600">Nhập số điện thoại và mã đặt bàn để xem trạng thái</p>
        </div>

        <!-- Search Form -->
        <form action="{{ route('api.reservations.search') }}" method="POST" class="bg-white p-8 rounded-2xl shadow-lg mb-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại *</label>
                    <input type="text" name="phone" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold"
                           placeholder="0901234567"
                           value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã đặt bàn (tùy chọn)</label>
                    <input type="text" name="code" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold"
                           placeholder="RSV20251116-0001"
                           value="{{ old('code') }}">
                    <p class="text-xs text-gray-500 mt-1">Để trống để xem tất cả đặt bàn</p>
                </div>
            </div>
            <button type="submit" class="mt-6 w-full bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold py-3 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Tìm kiếm
            </button>
        </form>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3 text-xl"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">Hướng dẫn tra cứu</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Nhập số điện thoại đã dùng khi đặt bàn</li>
                        <li>• Có thể nhập thêm mã đặt bàn để tìm kiếm chính xác hơn</li>
                        <li>• Mã đặt bàn được gửi qua SMS/Email sau khi đặt thành công</li>
                        <li>• Bạn có thể thanh toán online ngay sau khi tìm thấy đặt bàn</li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection