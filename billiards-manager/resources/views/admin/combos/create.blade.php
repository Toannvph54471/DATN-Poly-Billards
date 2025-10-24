@extends('admin.layouts.app')

@section('title', 'Thêm Combo mới - F&B Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-layer-group text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Thêm Combo Mới</h1>
                    <p class="text-gray-600 mt-2">Tạo combo ưu đãi cho khách hàng</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.combos.index') }}"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-xl font-medium transition flex items-center shadow-sm">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Quay lại danh sách
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
    <form action="{{ route('admin.combos.store') }}" method="POST">
        @csrf

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
                                <input type="text" name="name" value="{{ old('name') }}"
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
                                <input type="text" name="combo_code" value="{{ old('combo_code') }}"
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
                                            {{ old('status', 'active') === 'active' ? 'checked' : '' }}
                                            class="peer sr-only">
                                        <div
                                            class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-600 hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all duration-200">
                                            <i class="fas fa-play-circle mr-2"></i>
                                            Hoạt động
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="status" value="inactive"
                                            {{ old('status') === 'inactive' ? 'checked' : '' }} class="peer sr-only">
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
                                    placeholder="Mô tả chi tiết về combo...">{{ old('description') }}</textarea>
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
                                            <input type="number" name="price" value="{{ old('price') }}" min="0"
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
                                            <input type="number" name="actual_value" value="{{ old('actual_value') }}"
                                                min="0"
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
                        <p class="text-gray-600 text-sm mt-1">Chọn tối đa 5 sản phẩm cho combo</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @for ($i = 0; $i < 5; $i++)
                                <div
                                    class="border-2 border-dashed border-gray-200 rounded-xl p-5 hover:border-purple-300 transition-all duration-200 {{ $i === 0 ? 'bg-orange-50 border-orange-200' : '' }}">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-semibold text-gray-900 flex items-center">
                                            <span
                                                class="w-6 h-6 bg-{{ $i === 0 ? 'orange' : 'purple' }}-500 text-white rounded-full text-sm flex items-center justify-center mr-3">
                                                {{ $i + 1 }}
                                            </span>
                                            Sản phẩm {{ $i + 1 }}
                                            @if ($i === 0)
                                                <span
                                                    class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded">BẮT
                                                    BUỘC</span>
                                            @endif
                                        </h3>
                                        @if ($i > 0)
                                            <span class="text-gray-400 text-sm">Tùy chọn</span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn sản
                                                phẩm</label>
                                            <select name="combo_items[{{ $i }}][product_id]"
                                                class="w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200 {{ $i === 0 ? 'required' : '' }}"
                                                {{ $i === 0 ? 'required' : '' }}>
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ old("combo_items.$i.product_id") == $product->id ? 'selected' : '' }}
                                                        class="py-2">
                                                        {{ $product->name }} -
                                                        {{ number_format($product->price, 0, ',', '.') }}₫
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                                            <div class="relative">
                                                <input type="number" name="combo_items[{{ $i }}][quantity]"
                                                    value="{{ old("combo_items.$i.quantity", 1) }}" min="1"
                                                    class="w-full border-2 border-gray-200 focus:border-purple-500 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-100 transition-all duration-200"
                                                    {{ $i === 0 ? 'required' : '' }}>
                                                <span
                                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                                    <i class="fas fa-cube"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 text-lg mt-1 mr-3"></i>
                                <div>
                                    <p class="text-blue-800 font-medium mb-1">Mẹo nhỏ</p>
                                    <p class="text-blue-700 text-sm">
                                        Chọn ít nhất 1 sản phẩm bắt buộc. Đảm bảo giá bán thấp hơn tổng giá trị thực để tạo
                                        ưu đãi hấp dẫn.
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
                            <i class="fas fa-play-circle text-gray-600 mr-3"></i>
                            Thao tác
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 transform hover:-translate-y-0.5">
                                <i class="fas fa-plus-circle mr-3 text-lg"></i>
                                Tạo Combo Mới
                            </button>

                            <a href="{{ route('admin.combos.index') }}"
                                class="w-full bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-times mr-3"></i>
                                Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>
                        Thống kê nhanh
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Tổng combo</span>
                            <span class="font-semibold">15</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Đang hoạt động</span>
                            <span class="font-semibold">12</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">Combo mới</span>
                            <span class="font-semibold">3</span>
                        </div>
                    </div>
                </div>

                <!-- Guidelines Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                            Hướng dẫn
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Combo chất lượng</p>
                                    <p class="text-xs text-gray-600 mt-1">Chọn sản phẩm phù hợp với nhu cầu khách hàng</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-percentage text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Giá cả hợp lý</p>
                                    <p class="text-xs text-gray-600 mt-1">Đảm bảo giá bán thấp hơn giá trị thực</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                    <i class="fas fa-unique text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mã độc nhất</p>
                                    <p class="text-xs text-gray-600 mt-1">Mã combo phải không trùng lặp</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
