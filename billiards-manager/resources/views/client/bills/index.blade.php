@extends('layouts.app')

@section('title', 'Lịch sử hóa đơn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-display font-bold text-elegant-navy mb-4">Lịch sử hóa đơn</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Xem lại chi tiết các lần sử dụng dịch vụ của bạn</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tổng hóa đơn</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bills'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-coins text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tổng chi tiêu</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_spent']) }}₫</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Chưa thanh toán</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unpaid_bills'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tháng này</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month_spent']) }}₫</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-8">
            <form method="GET" action="{{ route('customer.bills.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold">
                        <option value="">Tất cả</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Đang mở</option>
                        <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Tạm dừng</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thanh toán</label>
                    <select name="is_paid" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_paid') == '1' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="0" {{ request('is_paid') == '0' ? 'selected' : '' }}>Chưa thanh toán</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Mã hóa đơn..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold">
                </div>

                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-search mr-2"></i>Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Bills List -->
        <div class="space-y-6">
            @forelse($bills as $bill)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $bill->bill_number }}</h3>
                            <p class="text-sm text-gray-500">{{ $bill->table->table_name ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            @if($bill->status === 'open')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                    Đang mở
                                </span>
                            @elseif($bill->status === 'paused')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">
                                    Tạm dừng
                                </span>
                            @elseif($bill->status === 'closed')
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-3 py-1 rounded-full">
                                    Đã đóng
                                </span>
                            @endif

                            @if($bill->is_paid)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Đã thanh toán
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>Chưa thanh toán
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Thời gian bắt đầu</p>
                            <p class="text-sm font-medium text-gray-900">{{ $bill->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Thời gian kết thúc</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $bill->end_time ? $bill->end_time->format('d/m/Y H:i') : 'Đang chơi' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tổng thời gian</p>
                            <p class="text-sm font-medium text-gray-900">{{ $bill->total_time }} phút</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Nhân viên phục vụ</p>
                            <p class="text-sm font-medium text-gray-900">{{ $bill->staff->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Tổng tiền bàn: <strong>{{ number_format($bill->table_price) }}₫</strong></p>
                            <p class="text-sm text-gray-600">Tổng sản phẩm: <strong>{{ number_format($bill->product_total) }}₫</strong></p>
                            @if($bill->discount_amount > 0)
                                <p class="text-sm text-green-600">Giảm giá: <strong>-{{ number_format($bill->discount_amount) }}₫</strong></p>
                            @endif
                            <p class="text-lg font-bold text-elegant-gold mt-2">
                                Thành tiền: {{ number_format($bill->final_amount) }}₫
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('customer.bills.show', $bill->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                                <i class="fas fa-eye mr-2"></i>Chi tiết
                            </a>

                            @if($bill->isOpen())
                                <a href="{{ route('customer.bills.edit', $bill->id) }}" 
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-edit mr-2"></i>Sửa
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-200">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">Chưa có hóa đơn nào</h3>
                <p class="text-gray-500 mb-8">Bạn chưa có lịch sử sử dụng dịch vụ</p>
                <a href="{{ route('reservation.create') }}" 
                   class="inline-block bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-4 rounded-lg transition duration-200 transform hover:scale-105">
                    <i class="fas fa-calendar-plus mr-3"></i>
                    Đặt bàn ngay
                </a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($bills->hasPages())
        <div class="mt-8">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection