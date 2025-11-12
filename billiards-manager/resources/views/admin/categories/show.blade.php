@extends('admin.layouts.app')

@section('title', 'Chi tiết Khuyến mãi - F&B Management')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chi tiết khuyến mãi</h1>
                <p class="text-gray-600 mt-1">Thông tin chi tiết về khuyến mãi "{{ $promotion->name }}"</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                {{-- <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Chỉnh sửa
                </a> --}}
                <a href="{{ route('admin.promotions.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Thông tin chính -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Thông tin khuyến mãi -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $promotion->name }}</h2>
                    <p class="text-gray-500 text-sm">Mã: {{ $promotion->promotion_code }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <p class="text-gray-900 bg-gray-50 rounded-lg p-3">{{ $promotion->description ?? 'Không có mô tả' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá</label>
                        <p class="text-gray-900">
                            @if ($promotion->discount_type === 'percentage')
                                Giảm {{ $promotion->discount_value }}%
                            @elseif ($promotion->discount_type === 'fixed')
                                Giảm {{ number_format($promotion->discount_value, 0, ',', '.') }}₫
                            @else
                                Combo khuyến mại
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điều kiện</label>
                        <p class="text-gray-900">
                            @if ($promotion->min_play_minutes)
                                Tối thiểu {{ $promotion->min_play_minutes }} phút
                            @else
                                Không có điều kiện
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian áp dụng</label>
                    <p class="text-gray-900">
                        {{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
                    </p>
                </div>

                @php
                    $now = now();
                    $start = \Carbon\Carbon::parse($promotion->start_date);
                    $end = \Carbon\Carbon::parse($promotion->end_date);
                    if ($promotion->status == 0) {
                        $color = 'bg-gray-100 text-gray-800';
                        $text = 'Ngừng kích hoạt';
                        $icon = 'fas fa-pause-circle';
                    } elseif ($start > $now) {
                        $color = 'bg-orange-100 text-orange-800';
                        $text = 'Sắp diễn ra';
                        $icon = 'fas fa-clock';
                    } elseif ($end < $now) {
                        $color = 'bg-red-100 text-red-800';
                        $text = 'Đã kết thúc';
                        $icon = 'fas fa-stop-circle';
                    } else {
                        $color = 'bg-green-100 text-green-800';
                        $text = 'Đang diễn ra';
                        $icon = 'fas fa-play-circle';
                    }
                @endphp
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                        <i class="{{ $icon }} mr-2"></i> {{ $text }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Thông tin áp dụng -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Phạm vi áp dụng</h3>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-2"></i>
                    Áp dụng cho:
                    <span class="ml-2 font-medium text-gray-900">
                        @if ($promotion->applies_to_combo && $promotion->applies_to_time_combo)
                            Combo bàn & Combo giờ
                        @elseif ($promotion->applies_to_combo)
                            Combo bàn
                        @elseif ($promotion->applies_to_time_combo)
                            Combo giờ
                        @else
                            Sản phẩm
                        @endif
                    </span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                    Ngày tạo: <span class="ml-2 text-gray-900">{{ $promotion->created_at->format('d/m/Y H:i') }}</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-user text-gray-500 mr-2"></i>
                    Cập nhật lần cuối: <span class="ml-2 text-gray-900">{{ $promotion->updated_at->format('d/m/Y H:i') }}</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
        {{-- <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center">
            <i class="fas fa-edit mr-2"></i>
            Chỉnh sửa Khuyến mãi
        </a> --}}
        <a href="{{ route('admin.promotions.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center">
            <i class="fas fa-list mr-2"></i>
            Danh sách Khuyến mãi
        </a>
    </div>
@endsection
