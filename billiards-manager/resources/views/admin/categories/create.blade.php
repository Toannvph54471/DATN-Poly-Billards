@extends('admin.layouts.app')

@section('title', 'Thêm Khuyến Mãi Mới - F&B Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-gift text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Thêm Khuyến Mãi Mới</h1>
                    <p class="text-gray-600 mt-2">Tạo chương trình khuyến mãi hấp dẫn cho khách hàng</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.promotions.index') }}"
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

    <!-- Main Form -->
    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Left Column -->
            <div class="xl:col-span-3 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Thông tin khuyến mãi
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">BẮT BUỘC</span>
                                Tên khuyến mãi
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border-2 {{ $errors->has('name') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Ví dụ: Giảm giá Noel 2025" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">BẮT BUỘC</span>
                                Mã khuyến mãi
                            </label>
                            <input type="text" name="promotion_code" value="{{ old('promotion_code') }}"
                                class="w-full border-2 {{ $errors->has('promotion_code') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Ví dụ: NOEL25" required>
                            @error('promotion_code')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Discount Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Loại giảm giá</label>
                            <select name="discount_type"
                                class="w-full border-2 {{ $errors->has('discount_type') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200">
                                <option value="">-- Chọn loại giảm giá --</option>
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                            </select>
                            @error('discount_type')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Discount Value -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Giá trị giảm</label>
                            <input type="number" name="discount_value" value="{{ old('discount_value') }}" min="0"
                                class="w-full border-2 {{ $errors->has('discount_value') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Ví dụ: 20 hoặc 50000" required>
                            @error('discount_value')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Minimum total -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Giá trị đơn hàng tối thiểu</label>
                            <input type="number" name="min_total_amount" value="{{ old('min_total_amount') }}" min="0"
                                class="w-full border-2 {{ $errors->has('min_total_amount') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                                placeholder="Ví dụ: 200000">
                            @error('min_total_amount')
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
                                        {{ old('status', 'active') === 'active' ? 'checked' : '' }} class="peer sr-only">
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
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Mô tả</label>
                            <textarea name="description" rows="4"
                                class="w-full border-2 {{ $errors->has('description') ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-blue-500' }} rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200 resize-none"
                                placeholder="Mô tả chi tiết chương trình...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Ngày bắt đầu</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="w-full border-2 border-gray-200 focus:border-blue-500 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Ngày kết thúc</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="w-full border-2 border-gray-200 focus:border-blue-500 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Action Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-play-circle text-gray-600 mr-3"></i>
                            Thao tác
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-plus-circle mr-3 text-lg"></i>
                            Tạo Khuyến Mãi Mới
                        </button>

                        <a href="{{ route('admin.promotions.index') }}"
                            class="w-full bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-times mr-3"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </div>

                <!-- Guide Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                            Hướng dẫn
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Mã duy nhất</p>
                                <p class="text-xs text-gray-600 mt-1">Mỗi mã khuyến mãi không được trùng lặp</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                <i class="fas fa-percentage text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Loại giảm giá</p>
                                <p class="text-xs text-gray-600 mt-1">Chọn phần trăm hoặc số tiền cố định</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                                <i class="fas fa-calendar text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Thời gian áp dụng</p>
                                <p class="text-xs text-gray-600 mt-1">Đảm bảo ngày bắt đầu < ngày kết thúc</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
