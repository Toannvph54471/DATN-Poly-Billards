@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa khách hàng - F&B Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa khách hàng</h1>
            <p class="text-gray-600">Cập nhật thông tin khách hàng</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" 
           class="bg-gray-500 text-white rounded-lg px-4 py-2 hover:bg-gray-600 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thông tin cơ bản -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h3>
                    
                    <!-- Tên khách hàng -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Tên khách hàng <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số điện thoại <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Thông tin bổ sung -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin bổ sung</h3>
                    
                    <!-- Loại khách hàng -->
                    <div>
                        <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Loại khách hàng <span class="text-red-500">*</span>
                        </label>
                        <select name="customer_type" id="customer_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="New" {{ old('customer_type', $customer->customer_type) == 'New' ? 'selected' : '' }}>Mới</option>
                            <option value="Regular" {{ old('customer_type', $customer->customer_type) == 'Regular' ? 'selected' : '' }}>Thường xuyên</option>
                            <option value="VIP" {{ old('customer_type', $customer->customer_type) == 'VIP' ? 'selected' : '' }}>VIP</option>
                        </select>
                        @error('customer_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ghi chú -->
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">
                            Ghi chú
                        </label>
                        <textarea name="note" id="note" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('note', $customer->note) }}</textarea>
                        @error('note')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Thống kê (chỉ hiển thị) -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê (Chỉ xem)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tổng lượt chơi</label>
                        <p class="text-sm text-gray-900">{{ $customer->total_visits }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tổng chi tiêu</label>
                        <p class="text-sm text-gray-900">{{ number_format($customer->total_spent, 0, ',', '.') }}₫</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Lần cuối chơi</label>
                        <p class="text-sm text-gray-900">{{ $customer->last_visit_at ? $customer->last_visit_at->format('d/m/Y H:i') : 'Chưa có' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ngày tạo</label>
                        <p class="text-sm text-gray-900">{{ $customer->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.customers.show', $customer->id) }}"
                    class="bg-gray-500 text-white rounded-lg px-6 py-2 hover:bg-gray-600 transition">
                    Hủy
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection