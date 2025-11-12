@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục Mới - F&B Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-tags text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Thêm danh mục mới</h1>
                    <p class="text-gray-600 mt-2">Thêm danh mục mới vào hệ thống quản lý</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.categories.index') }}"
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
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Thông tin cơ bản
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên danh mục <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập tên danh mục">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Danh mục <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Chọn loại</option>
                            <option value="table" {{ old('type') == 'table' ? 'selected' : '' }}>Table</option>
                            <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Product</option>
                        </select>
                    </div>

                    <div>
                        <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá giờ chơi <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate') }}"
                            required="" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="50000">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng thái <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Đang kích hoạt</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Ngừng kích hoạt
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Mô tả chi tiết danh mục...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}"
                        class="bg-gray-200 text-gray-700 rounded-lg px-6 py-2 hover:bg-gray-300 transition">
                        Hủy
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Lưu danh mục
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
