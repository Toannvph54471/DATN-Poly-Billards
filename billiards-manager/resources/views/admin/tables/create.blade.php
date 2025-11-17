@extends('admin.layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Thêm Bàn Mới</h1>
                <p class="text-gray-600">Thêm bàn mới vào hệ thống quản lý</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Form Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-lg font-semibold text-gray-900">Thông tin bàn</h2>
                            <p class="text-sm text-gray-500">Nhập đầy đủ thông tin bên dưới</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.tables.store') }}">
                        @csrf

                        <div class="space-y-6">
                            <!-- Tên bàn -->
                            <div>
                                <label for="table_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên bàn *
                                </label>
                                <input type="text" id="table_name" name="table_name" value="{{ old('table_name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Ví dụ: Bàn số 1" required>
                                @error('table_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mã bàn -->
                            <div>
                                <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mã bàn *
                                </label>
                                <input type="text" id="table_number" name="table_number"
                                    value="{{ old('table_number') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Ví dụ: T01, T02..." required>
                                @error('table_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Loại bàn -->
                            <div>
                                <label for="table_rate_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Loại bàn *
                                </label>
                                <select id="table_rate_id" name="table_rate_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Chọn loại bàn</option>
                                    @foreach ($tableRates as $rate)
                                        <option value="{{ $rate->id }}"
                                            {{ old('table_rate_id') == $rate->id ? 'selected' : '' }}>
                                            {{ $rate->name }} - {{ number_format($rate->hourly_rate, 0, ',', '.') }}đ/giờ
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_rate_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sức chứa -->
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sức chứa *
                                </label>
                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 4) }}"
                                    min="1" max="20"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Trạng thái (Fixed to available) -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Trạng thái
                                </label>
                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-md">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <div>
                                        <span class="text-sm font-medium text-green-800">Available</span>
                                        <p class="text-xs text-green-600 mt-1">Bàn sẽ được thêm với trạng thái trống</p>
                                    </div>
                                </div>
                                <input type="hidden" name="status" value="available">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                            <a href="{{ route('admin.tables.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Quay lại danh sách
                            </a>

                            <div class="flex space-x-3">
                                <button type="reset"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>
                                    Đặt lại
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Thêm bàn mới
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Bàn mới sẽ được thêm với trạng thái "Available" và sẵn sàng cho khách hàng sử dụng
                </p>
            </div>
        </div>
    </div>

    <style>
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .transition-colors {
            transition: all 0.2s ease-in-out;
        }
    </style>
@endsection
