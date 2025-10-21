@extends('admin.layouts.app')

@section('title', 'Chi tiết Combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết Combo</h1>
            <p class="text-gray-600">Thông tin chi tiết về combo</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.combos.edit', $combo->id) }}"
                class="bg-green-600 text-white rounded-lg px-4 py-2 hover:bg-green-700 transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.combos.index') }}"
                class="bg-gray-600 text-white rounded-lg px-4 py-2 hover:bg-gray-700 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Mã Combo</p>
                <p class="text-lg font-semibold text-gray-800">{{ $combo->combo_code ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tên Combo</p>
                <p class="text-lg font-semibold text-gray-800">{{ $combo->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Giá (VND)</p>
                <p class="text-lg font-semibold text-gray-800">{{ number_format($combo->price, 0, ',', '.') }} VND</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Giá trị thực (VND)</p>
                <p class="text-lg font-semibold text-gray-800">{{ number_format($combo->actual_value, 0, ',', '.') }} VND</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Trạng thái</p>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $combo->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $combo->isActive() ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                </span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500">Mô tả</p>
                <p class="text-lg text-gray-800">{{ $combo->description ?? 'Không có mô tả' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Ngày tạo</p>
                <p class="text-lg text-gray-800">{{ $combo->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Ngày cập nhật</p>
                <p class="text-lg text-gray-800">{{ $combo->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
@endsection