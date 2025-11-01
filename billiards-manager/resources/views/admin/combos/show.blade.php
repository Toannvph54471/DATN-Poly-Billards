@extends('admin.layouts.app')

@section('title', 'Chi tiết Combo')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-layer-group text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $combo->name }}</h1>
                <p class="text-gray-600 mt-1">
                    Mã: <span class="font-mono font-semibold">{{ $combo->combo_code }}</span>
                    @if($combo->is_time_combo)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-clock mr-1"></i>Combo bàn
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.combos.edit', $combo->id) }}"
               class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 font-medium transition flex items-center">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            <a href="{{ route('admin.combos.index') }}"
               class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-300 font-medium transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>
</div>

@if($combo->is_time_combo && $activeSession)
    <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-xl shadow-sm">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-hourglass-half text-white"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-blue-900 font-bold">Session đang chạy</h4>
                <p class="text-blue-700 text-sm">Bàn đang sử dụng combo này. Thời gian còn lại: {{ $activeSession->remaining_minutes ?? 'N/A' }} phút</p>
            </div>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Products -->
    <div class="lg:col-span-3 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-blue-600 mr-3"></i> Sản phẩm trong combo
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($combo->comboItems as $item)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $item->product->product_code }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $item->quantity }} x {{ number_format($item->unit_price) }}đ</p>
                            <p class="text-sm text-gray-500">Tổng: {{ number_format($item->quantity * $item->unit_price) }}đ</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả</h3>
            <p class="text-gray-700">{{ $combo->description ?? 'Không có mô tả' }}</p>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Giá trị combo</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Giá bán</span>
                    <span class="font-bold text-xl text-gray-900">{{ number_format($combo->price) }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Giá trị thực</span>
                    <span class="text-gray-500 line-through">{{ number_format($combo->actual_value) }}đ</span>
                </div>
                <div class="border-t pt-4 flex justify-between">
                    <span class="text-gray-600">Ưu đãi</span>
                    <span class="font-bold text-green-600">
                        {{ number_format($combo->getDiscountAmount()) }}đ ({{ $combo->getDiscountPercent() }}%)
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600">Trạng thái</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $combo->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $combo->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </p>
                </div>
                @if($combo->is_time_combo)
                    <div>
                        <label class="text-sm text-gray-600">Loại bàn</label>
                        <p class="mt-1 font-medium text-gray-900">{{ $combo->tableCategory?->name ?? 'Tất cả' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Thời gian chơi</label>
                        <p class="mt-1 font-medium text-gray-900">{{ $combo->play_duration_minutes }} phút</p>
                    </div>
                @endif
                <div>
                    <label class="text-sm text-gray-600">Ngày tạo</label>
                    <p class="mt-1 text-gray-900">{{ $combo->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Cập nhật</label>
                    <p class="mt-1 text-gray-900">{{ $combo->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection