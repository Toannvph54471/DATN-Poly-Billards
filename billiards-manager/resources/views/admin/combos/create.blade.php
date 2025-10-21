@extends('admin.layouts.app')

@section('title', 'Thêm Combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Thêm Combo</h1>
            <p class="text-gray-600">Tạo combo mới cho hệ thống</p>
        </div>
        <a href="{{ route('admin.combos.index') }}"
            class="bg-gray-600 text-white rounded-lg px-4 py-2 hover:bg-gray-700 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.combos.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Combo Code -->
                <div>
                    <label for="combo_code" class="block text-sm font-medium text-gray-700 mb-1">Mã Combo</label>
                    <input type="text" name="combo_code" id="combo_code" value="{{ old('combo_code') }}"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mã combo" required>
                    @error('combo_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Combo</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập tên combo" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá (VND)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập giá" step="0.01" required>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actual Value -->
                <div>
                    <label for="actual_value" class="block text-sm font-medium text-gray-700 mb-1">Giá trị thực (VND)</label>
                    <input type="number" name="actual_value" id="actual_value" value="{{ old('actual_value') }}"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập giá trị thực" step="0.01" required>
                    @error('actual_value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500" required>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mô tả combo">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-6 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Lưu
                </button>
            </div>
        </form>
    </div>
@endsection