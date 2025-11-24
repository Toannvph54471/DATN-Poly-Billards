@extends('admin.layouts.app')

@section('title', 'Thêm nhân viên mới - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Thêm nhân viên mới</h1>
            <p class="text-gray-600">Thêm thông tin cho nhân viên mới</p>
        </div>

        <a href="{{ route('admin.employees.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại danh sách
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Personal Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">
                            Thông tin cá nhân
                        </h3>

                        <div class="space-y-4">
                            <!-- Mã nhân viên -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Mã nhân viên *
                                </label>
                                <input type="text" name="employee_code"
                                       value="{{ old('employee_code') }}" required
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                              focus:ring-2 focus:ring-blue-500">
                                @error('employee_code')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Họ và tên -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Họ và tên *
                                </label>
                                <input type="text" name="name"
                                       value="{{ old('name') }}" required
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                              focus:ring-2 focus:ring-blue-500">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Số điện thoại *
                                </label>
                                <input type="text" name="phone"
                                       value="{{ old('phone') }}" required
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                              focus:ring-2 focus:ring-blue-500">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email *
                                </label>
                                <input type="email" name="email"
                                       value="{{ old('email') }}"
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                              focus:ring-2 focus:ring-blue-500">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Địa chỉ -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Địa chỉ
                                </label>
                                <textarea name="address"
                                          class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                                 focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">
                            Thông tin công việc
                        </h3>

                        <div class="space-y-4">
                            <!-- Chức vụ -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Chức vụ *
                                </label>
                                <select name="position" required
                                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                               focus:ring-2 focus:ring-blue-500">
                                    <option value="">Chọn chức vụ</option>
                                    <option value="manager" {{ old('position') == 'manager' ? 'selected' : '' }}>
                                        Quản lý
                                    </option>
                                    <option value="staff" {{ old('position') == 'staff' ? 'selected' : '' }}>
                                        Nhân viên
                                    </option>
                                    <option value="cashier" {{ old('position') == 'cashier' ? 'selected' : '' }}>
                                        Thu ngân
                                    </option>
                                    <option value="waiter" {{ old('position') == 'waiter' ? 'selected' : '' }}>
                                        Phục vụ
                                    </option>
                                </select>
                                @error('position')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Loại lương -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Loại lương *
                                </label>
                                <select name="salary_type" required
                                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                               focus:ring-2 focus:ring-blue-500">
                                    <option value="hourly" {{ old('salary_type') == 'hourly' ? 'selected' : '' }}>
                                        Part-time (25.000 VND/giờ)
                                    </option>
                                    <option value="monthly" {{ old('salary_type') == 'monthly' ? 'selected' : '' }}>
                                        Lương cứng (35.000 VND/giờ)
                                    </option>
                                </select>
                                @error('salary_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mức lương -->

                            <!-- Ngày bắt đầu -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ngày bắt đầu *
                                </label>
                                <input type="date" name="start_date"
                                       value="{{ old('start_date') }}" required
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                              focus:ring-2 focus:ring-blue-500">
                                @error('start_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                            <!-- Trạng thái -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Trạng thái *
                                </label>
                                <select name="status" required
                                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800
                                               focus:ring-2 focus:ring-blue-500">
                                    <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>
                                        Đang hoạt động
                                    </option>
                                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>
                                        Ngừng hoạt động
                                    </option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-medium flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Thêm nhân viên
                </button>
            </div>
        </form>

        <!-- Error Box -->
        @if ($errors->any())
            <div class="mt-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <h4 class="text-red-800 font-medium">
                            Vui lòng kiểm tra lại thông tin
                        </h4>
                    </div>
                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endsection