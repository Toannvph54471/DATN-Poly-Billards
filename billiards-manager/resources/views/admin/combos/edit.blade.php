@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Combo - F&B Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-edit text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Combo</h1>
                    <p class="text-gray-600 mt-2">Cập nhật thông tin combo {{ $combo->name }}</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.combos.show', $combo->id) }}"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-xl font-medium transition flex items-center shadow-sm">
                    <i class="fas fa-eye mr-3"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center">
            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
            <div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg mr-2"></i>
                <h4 class="text-red-800 font-semibold">Có lỗi xảy ra</h4>
            </div>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center">
                        <i class="fas fa-circle text-red-400 text-xs mr-2"></i>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Main Form -->
    <form action="{{ route('admin.combos.update', $combo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Left Column - Main Information -->
            <div class="xl:col-span-3 space-y-8">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Thông tin cơ bản
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">BẮT BUỘC</span>
                                    Tên combo
                                </label>
                                <input type="text" name="name" value="{{ old('name', $combo->name) }}"
                                    class="w-full border-2 {{ $errors->has('name') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                    placeholder="Ví dụ: Combo Gia Đình 4 Người" required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Code -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">BẮT BUỘC</span>
                                    Mã combo
                                </label>
                                <input type="text" name="combo_code" value="{{ old('combo_code', $combo->combo_code) }}"
                                    class="w-full border-2 {{ $errors->has('combo_code') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                    placeholder="Ví dụ: COMBO001" required>
                                @error('combo_code')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-3">Trạng thái</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="status" value="active"
                                            {{ old('status', $combo->status) === 'active' ? 'checked' : '' }}
                                            class="peer sr-only">
                                        <div
                                            class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-600 hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all duration-200">
                                            <i class="fas fa-play-circle mr-2"></i>
                                            Hoạt động
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="status" value="inactive"
                                            {{ old('status', $combo->status) === 'inactive' ? 'checked' : '' }}
                                            class="peer sr-only">
                                        <div
                                            class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-600 hover:border-red-300 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200">
                                            <i class="fas fa-pause-circle mr-2"></i>
                                            Tạm dừng
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-semibold text-gray-800 mb-3">
                                    Mô tả combo
                                </label>
                                <textarea name="description" rows="4"
                                    class="w-full border-2 {{ $errors->has('description') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200 resize-none"
                                    placeholder="Mô tả chi tiết về combo...">{{ old('description', $combo->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Pricing -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-semibold text-gray-800 mb-4">Thông tin giá</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-xl border border-green-100">
                                        <label class="block text-sm font-semibold text-green-800 mb-2 flex items-center">
                                            <i class="fas fa-tag mr-2"></i>
                                            Giá bán
                                        </label>
                                        <div class="relative">
                                            <input type="number" name="price" value="{{ old('price', $combo->price) }}"
                                                min="0"
                                                class="w-full bg-white border-2 border-green-200 focus:border-green-500 rounded-xl px-4 py-3.5 pr-12 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-200"
                                                placeholder="0" required>
                                            <span
                                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-green-600 font-semibold">₫</span>
                                        </div>
                                        @error('price')
                                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-cyan-50 p-5 rounded-xl border border-blue-100">
                                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center">
                                            <i class="fas fa-receipt mr-2"></i>
                                            Giá trị thực
                                        </label>
                                        <div class="relative">
                                            <input type="number" name="actual_value"
                                                value="{{ old('actual_value', $combo->actual_value) }}" min="0"
                                                class="w-full bg-white border-2 border-blue-200 focus:border-blue-500 rounded-xl px-4 py-3.5 pr-12 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                                placeholder="0" required>
                                            <span
                                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-600 font-semibold">₫</span>
                                        </div>
                                        @error('actual_value')
                                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Selection Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cubes text-purple-600 mr-3"></i>
                            Sản phẩm trong combo
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Chọn tối đa 10 sản phẩm cho combo</p>
                    </div>
                    <div class="p-6">
                        <div id="combo-products-container" class="space-y-4">
                            @php
                                $comboItems = $combo->comboItems;
                            @endphp

                            <!-- Sản phẩm bắt buộc đầu tiên -->
                            @if ($comboItems->count() > 0)
                                @foreach ($comboItems as $index => $item)
                                    <div
                                        class="product-item border-2 {{ $index === 0 ? 'border-orange-200 bg-orange-50' : 'border-dashed border-gray-200' }} rounded-xl p-5 hover:border-purple-300 transition-all duration-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="font-semibold text-gray-900 flex items-center">
                                                <span
                                                    class="product-number w-6 h-6 bg-{{ $index === 0 ? 'orange' : 'purple' }}-500 text-white rounded-full text-sm flex items-center justify-center mr-3">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="product-title">Sản phẩm {{ $index + 1 }}</span>
                                                @if ($index === 0)
                                                    <span
                                                        class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded">BẮT
                                                        BUỘC</span>
                                                @endif
                                            </h3>
                                            <div class="flex items-center space-x-2">
                                                @if ($index > 0)
                                                    <span class="text-gray-400 text-sm">Tùy chọn</span>
                                                    <button type="button"
                                                        class="remove-product-btn text-red-500 hover:text-red-700 transition-colors duration-200">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn sản
                                                    phẩm</label>
                                                <select name="combo_items[{{ $index }}][product_id]"
                                                    class="product-select w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200 {{ $index === 0 ? 'required' : '' }}"
                                                    {{ $index === 0 ? 'required' : '' }}>
                                                    <option value="">-- Chọn sản phẩm --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            {{ old("combo_items.$index.product_id", $item->product_id) == $product->id ? 'selected' : '' }}
                                                            class="py-2">
                                                            {{ $product->name }} -
                                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Số
                                                    lượng</label>
                                                <div class="relative">
                                                    <input type="number"
                                                        name="combo_items[{{ $index }}][quantity]"
                                                        value="{{ old("combo_items.$index.quantity", $item->quantity) }}"
                                                        min="1"
                                                        class="quantity-input w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                                                        {{ $index === 0 ? 'required' : '' }}>
                                                    <span
                                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                                        <i class="fas fa-cube"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden field for existing item ID -->
                                        <input type="hidden" name="combo_items[{{ $index }}][id]"
                                            value="{{ $item->id }}">
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback nếu không có sản phẩm nào -->
                                <div class="product-item border-2 border-orange-200 rounded-xl p-5 bg-orange-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-900 flex items-center">
                                            <span
                                                class="product-number w-6 h-6 bg-orange-500 text-white rounded-full text-sm flex items-center justify-center mr-3">
                                                1
                                            </span>
                                            <span class="product-title">Sản phẩm 1</span>
                                            <span class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded">BẮT
                                                BUỘC</span>
                                        </h3>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn sản
                                                phẩm</label>
                                            <select name="combo_items[0][product_id]"
                                                class="product-select w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200 required"
                                                required>
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" class="py-2">
                                                        {{ $product->name }} -
                                                        {{ number_format($product->price, 0, ',', '.') }}₫
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                                            <div class="relative">
                                                <input type="number" name="combo_items[0][quantity]" value="1"
                                                    min="1"
                                                    class="quantity-input w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                                                    required>
                                                <span
                                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                                    <i class="fas fa-cube"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Nút thêm sản phẩm -->
                        <div class="mt-6 flex justify-center">
                            <button type="button" id="add-product-btn"
                                class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-plus mr-2"></i> Thêm sản phẩm
                            </button>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 text-lg mt-1 mr-3"></i>
                                <div>
                                    <p class="text-blue-800 font-medium mb-1">Mẹo chỉnh sửa</p>
                                    <p class="text-blue-700 text-sm">
                                        Bạn có thể thêm, xóa hoặc thay đổi sản phẩm. Đảm bảo giữ ít nhất 1 sản phẩm bắt buộc
                                        và giá bán hợp lý.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions & Info -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Actions Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-save text-gray-600 mr-3"></i>
                            Thao tác
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-lg shadow-green-200 hover:shadow-xl hover:shadow-green-300 transform hover:-translate-y-0.5">
                                <i class="fas fa-save mr-3 text-lg"></i>
                                Cập nhật Combo
                            </button>

                            <a href="{{ route('admin.combos.show', $combo->id) }}"
                                class="w-full bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-times mr-3"></i>
                                Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Combo Info Card -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Thông tin Combo
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Ngày tạo</span>
                            <span class="font-semibold text-sm">{{ $combo->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Số sản phẩm</span>
                            <span class="font-semibold">{{ $combo->comboItems->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Trạng thái</span>
                            <span
                                class="font-semibold {{ $combo->status === 'active' ? 'text-green-300' : 'text-red-300' }}">
                                {{ $combo->status === 'active' ? 'Đang hoạt động' : 'Tạm dừng' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Guidelines Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                            Lưu ý khi chỉnh sửa
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-sync-alt text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Cập nhật thông minh</p>
                                    <p class="text-xs text-gray-600 mt-1">Thay đổi sẽ ảnh hưởng đến tất cả đơn hàng sau này
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Kiểm tra kỹ</p>
                                    <p class="text-xs text-gray-600 mt-1">Đảm bảo giá và sản phẩm chính xác trước khi lưu
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-history text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Lịch sử thay đổi</p>
                                    <p class="text-xs text-gray-600 mt-1">Hệ thống sẽ ghi nhận mọi thay đổi của bạn</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔍 Đang khởi tạo JavaScript...');

        const container = document.getElementById('combo-products-container');
        const addButton = document.getElementById('add-product-btn');

        if (!container || !addButton) {
            console.error('❌ Không tìm thấy container hoặc nút');
            return;
        }

        console.log('✅ Đã tìm thấy container và nút');

        let productCount = {{ $combo->comboItems->count() }};
        if (productCount === 0) productCount = 1;

        console.log('Số sản phẩm hiện tại:', productCount);

        // THÊM SỰ KIỆN CLICK CHO NÚT
        addButton.addEventListener('click', function() {
            console.log('🎯 NÚT ĐÃ ĐƯỢC CLICK!');

            if (productCount >= 10) {
                alert('Bạn chỉ có thể thêm tối đa 10 sản phẩm');
                return;
            }

            productCount++;
            const newIndex = productCount - 1;

            const newProduct = document.createElement('div');
            newProduct.className =
                'product-item border-2 border-dashed border-gray-200 rounded-xl p-5 hover:border-purple-300 transition-all duration-200';
            newProduct.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <span class="product-number w-6 h-6 bg-purple-500 text-white rounded-full text-sm flex items-center justify-center mr-3">
                        ${productCount}
                    </span>
                    <span class="product-title">Sản phẩm ${productCount}</span>
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-400 text-sm">Tùy chọn</span>
                    <button type="button" class="remove-product-btn text-red-500 hover:text-red-700 transition-colors duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn sản phẩm</label>
                    <select name="combo_items[${newIndex}][product_id]" 
                            class="product-select w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}₫</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                    <div class="relative">
                        <input type="number" name="combo_items[${newIndex}][quantity]" value="1" min="1" 
                               class="quantity-input w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-cube"></i>
                        </span>
                    </div>
                </div>
            </div>
        `;

            container.appendChild(newProduct);

            // Thêm sự kiện xóa
            newProduct.querySelector('.remove-product-btn').addEventListener('click', function() {
                const products = container.querySelectorAll('.product-item');
                if (products.length <= 1) {
                    alert('Combo phải có ít nhất 1 sản phẩm');
                    return;
                }
                container.removeChild(newProduct);
                productCount--;
                updateProductNumbers();
            });

            updateProductNumbers();
        });

        // Thêm sự kiện xóa cho sản phẩm hiện có
        document.querySelectorAll('.remove-product-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productElement = this.closest('.product-item');
                const products = container.querySelectorAll('.product-item');
                if (products.length <= 1) {
                    alert('Combo phải có ít nhất 1 sản phẩm');
                    return;
                }
                container.removeChild(productElement);
                productCount--;
                updateProductNumbers();
            });
        });

        function updateProductNumbers() {
            const products = container.querySelectorAll('.product-item');
            products.forEach((product, index) => {
                const numberSpan = product.querySelector('.product-number');
                const titleSpan = product.querySelector('.product-title');

                if (numberSpan && titleSpan) {
                    const newNumber = index + 1;
                    numberSpan.textContent = newNumber;
                    titleSpan.textContent = `Sản phẩm ${newNumber}`;

                    // Cập nhật các field name
                    const select = product.querySelector('.product-select');
                    const input = product.querySelector('.quantity-input');
                    const hiddenId = product.querySelector('input[type="hidden"]');

                    if (select) select.name = `combo_items[${index}][product_id]`;
                    if (input) input.name = `combo_items[${index}][quantity]`;
                    if (hiddenId) hiddenId.name = `combo_items[${index}][id]`;
                }
            });
        }

        console.log('✅ JavaScript đã sẵn sàng');
    });
</script>
