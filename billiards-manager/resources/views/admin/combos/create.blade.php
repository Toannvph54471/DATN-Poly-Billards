<!-- CREATE.BLADE.PHP -->
@extends('admin.layouts.app')

@section('title', 'Thêm Combo Đặt Bàn - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Thêm Combo Mới</h1>
            <p class="text-gray-600 mt-1">Tạo combo đặt bàn mới cho hệ thống</p>
        </div>
        <a href="{{ route('admin.combos.index') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Quay Lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.combos.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Combo Code -->
                <div>
                    <label for="combo_code" class="block text-sm font-semibold text-gray-900 mb-2">
                        Mã Combo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="combo_code" id="combo_code"
                        value="{{ old('combo_code') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('combo_code') border-red-500 @enderror"
                        placeholder="VD: COMBO001"
                        required>
                    @error('combo_code')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                        Tên Combo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('name') border-red-500 @enderror"
                        placeholder="VD: Combo Nhà Hàng"
                        required>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-900 mb-2">
                        Giá Bán (VND) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" id="price"
                        value="{{ old('price') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('price') border-red-500 @enderror"
                        placeholder="0"
                        step="1000"
                        min="0"
                        required>
                    @error('price')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Actual Value -->
                <div>
                    <label for="actual_value" class="block text-sm font-semibold text-gray-900 mb-2">
                        Giá Trị Thực (VND) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="actual_value" id="actual_value"
                        value="{{ old('actual_value') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('actual_value') border-red-500 @enderror"
                        placeholder="0"
                        step="1000"
                        min="0"
                        required>
                    @error('actual_value')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                        Trạng Thái <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('status') border-red-500 @enderror"
                        required>
                        <option value="">-- Chọn Trạng Thái --</option>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Đang Hoạt Động</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Tạm Dừng</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                    Mô Tả
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none @error('description') border-red-500 @enderror"
                    placeholder="Nhập mô tả chi tiết về combo đặt bàn...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.combos.index') }}"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Hủy
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i> Lưu Combo
                </button>
            </div>
        </form>
    </div>
@endsection