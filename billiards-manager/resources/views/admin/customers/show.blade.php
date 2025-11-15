@extends('admin.layouts.app')

@section('title', 'Chi tiết khách hàng - F&B Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết khách hàng</h1>
            <p class="text-gray-600">Thông tin chi tiết và lịch sử khách hàng</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" 
               class="bg-yellow-500 text-white rounded-lg px-4 py-2 hover:bg-yellow-600 transition flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Sửa
            </a>
            <a href="{{ route('admin.customers.index') }}" 
               class="bg-gray-500 text-white rounded-lg px-4 py-2 hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thông tin chính -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tên khách hàng</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Số điện thoại</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?? 'Chưa có' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Loại khách hàng</label>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $customer->customer_type === 'VIP' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $customer->customer_type === 'Regular' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $customer->customer_type === 'New' ? 'bg-blue-100 text-blue-800' : '' }}">
                            @if($customer->customer_type === 'VIP')
                                <i class="fas fa-crown mr-1"></i>
                            @elseif($customer->customer_type === 'Regular')
                                <i class="fas fa-user-check mr-1"></i>
                            @else
                                <i class="fas fa-user-plus mr-1"></i>
                            @endif
                            {{ $customer->customer_type }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $customer->total_visits }}</div>
                        <div class="text-sm text-gray-500">Lượt chơi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($customer->total_spent, 0, ',', '.') }}₫</div>
                        <div class="text-sm text-gray-500">Tổng chi tiêu</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            @if($customer->total_visits > 0)
                                {{ number_format($customer->total_spent / $customer->total_visits, 0, ',', '.') }}₫
                            @else
                                0₫
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">Trung bình/lượt</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ $customer->last_visit_at ? $customer->last_visit_at->format('d/m/Y') : 'Chưa có' }}
                        </div>
                        <div class="text-sm text-gray-500">Lần cuối</div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            @if($customer->note)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ghi chú</h3>
                <p class="text-gray-700">{{ $customer->note }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Thông tin hệ thống -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin hệ thống</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ngày tạo</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cập nhật cuối</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $customer->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thao tác</h3>
                <div class="space-y-2">
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" 
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-600 text-white rounded-lg px-4 py-2 hover:bg-red-700 transition flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Xóa khách hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection