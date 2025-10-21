@extends('admin.layouts.app')

@section('title', 'Chi Tiết Combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi Tiết Combo</h1>
            <p class="text-gray-600 mt-1">Thông tin chi tiết về combo đặt bàn</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.combos.edit', $combo->id) }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <i class="fas fa-pencil-alt mr-2"></i> Chỉnh Sửa
            </a>
            <a href="{{ route('admin.combos.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Quay Lại
            </a>
        </div>
    </div>

    <!-- Main Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mã Combo</p>
                    <h2 class="text-2xl font-bold text-gray-900 mt-1">{{ $combo->combo_code ?? 'N/A' }}</h2>
                </div>
                <div>
                    @if ($combo->isActive())
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i> Hoạt Động
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i> Tạm Dừng
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-8">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Tên Combo -->
                <div class="border-l-4 border-indigo-500 pl-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tên Combo</p>
                    <p class="text-xl font-bold text-gray-900 mt-2">{{ $combo->name ?? 'N/A' }}</p>
                </div>

                <!-- Giá Bán -->
                <div class="border-l-4 border-green-500 pl-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Giá Bán</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        {{ number_format($combo->price, 0, ',', '.') }} <span class="text-base font-semibold text-gray-600">đ</span>
                    </p>
                </div>

                <!-- Giá Trị Thực -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Giá Trị Thực</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        {{ number_format($combo->actual_value, 0, ',', '.') }} <span class="text-base font-semibold text-gray-600">đ</span>
                    </p>
                </div>

                <!-- Tiết Kiệm -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tiết Kiệm</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">
                        {{ number_format($combo->actual_value - $combo->price, 0, ',', '.') }} <span class="text-base font-semibold text-gray-600">đ</span>
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        (~{{ round((($combo->actual_value - $combo->price) / $combo->actual_value) * 100, 1) }}% giảm)
                    </p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-8"></div>

            <!-- Description Section -->
            @if ($combo->description)
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Mô Tả</h3>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $combo->description }}</p>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 my-8"></div>
            @endif

            <!-- Timestamps -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ngày Tạo</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">
                        {{ $combo->created_at->format('d/m/Y') }}
                        <span class="text-gray-500 font-normal">{{ $combo->created_at->format('H:i') }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Ngày Cập Nhật</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">
                        {{ $combo->updated_at->format('d/m/Y') }}
                        <span class="text-gray-500 font-normal">{{ $combo->updated_at->format('H:i') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 mt-8 justify-center md:justify-end">
        <a href="{{ route('admin.combos.edit', $combo->id) }}"
            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold shadow-sm">
            <i class="fas fa-pencil-alt mr-2"></i> Chỉnh Sửa Combo
        </a>
        <a href="{{ route('admin.combos.index') }}"
            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Quay Lại
        </a>
    </div>
@endsection