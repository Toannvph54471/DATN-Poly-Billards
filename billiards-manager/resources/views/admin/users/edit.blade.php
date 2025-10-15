@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Thành Viên - F&B Management')

@section('content')
    <!-- Thông báo -->
    @if (session('success'))
        <div class="flex items-center bg-green-500/20 border border-green-500 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span class="font-medium">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center bg-red-500/20 border border-red-500 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
            <span class="font-medium">{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa Thành Viên</h1>
            <p class="text-gray-600">Cập nhật thông tin thành viên</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('POST')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Thông tin cơ bản -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">
                            Thông tin cơ bản
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên *</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại *</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin khác -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">
                            Thông tin khác
                        </h3>

                        <div class="space-y-4">
                            <!-- Vai trò -->
                            <div>
                                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
                                <select name="role_id" id="role_id"
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Chọn vai trò</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Trạng thái -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                                <select name="status"
                                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                                    <option value="Active"
                                        {{ old('status', $user->status) === 'Active' ? 'selected' : '' }}>Đang hoạt động
                                    </option>
                                    <option value="Inactive"
                                        {{ old('status', $user->status) === 'Inactive' ? 'selected' : '' }}>Ngừng hoạt động
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

            <!-- Thông tin hệ thống -->
            <div class="mt-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Thông tin hệ thống</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex flex-col">
                        <span class="text-gray-500 text-xs uppercase mb-1">Ngày tham gia</span>
                        <span class="text-gray-800 font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 text-xs uppercase mb-1">Tổng lượt chơi</span>
                        <span class="text-gray-800 font-medium">{{ $user->total_visits ?? 0 }} lượt</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-500 text-xs uppercase mb-1">ID thành viên</span>
                        <span class="text-gray-800 font-medium">{{ $user->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-medium flex items-center">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
            </div>
        </form>
    </div>

    <!-- Hiển thị lỗi Validation -->
    @if ($errors->any())
        <div class="mt-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <h4 class="text-red-800 font-medium">Vui lòng kiểm tra lại thông tin</h4>
                </div>
                <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection