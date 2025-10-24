@extends('admin.layouts.app')

@section('title', 'Chi tiết Combo - F&B Management')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chi tiết Combo</h1>
            <p class="text-gray-600 mt-1">Thông tin đầy đủ về combo {{ $combo->name }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('admin.combos.edit', $combo->id) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.combos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Thông tin chính -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Thông tin combo -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-layer-group text-blue-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $combo->name }}</h2>
                <p class="text-gray-500 text-sm">Mã: {{ $combo->combo_code }}</p>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <p class="text-gray-900 bg-gray-50 rounded-lg p-3">{{ $combo->description ?? 'Không có mô tả' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán</label>
                    <p class="text-lg font-semibold text-green-600">{{ number_format($combo->price, 0, ',', '.') }}₫</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị thực</label>
                    <p class="text-lg font-semibold text-blue-600">{{ number_format($combo->actual_value, 0, ',', '.') }}₫</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiết kiệm</label>
                <p class="text-lg font-semibold text-red-600">
                    {{ number_format($combo->actual_value - $combo->price, 0, ',', '.') }}₫
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $combo->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    <i class="fas {{ $combo->isActive() ? 'fa-check-circle' : 'fa-pause-circle' }} mr-2"></i>
                    {{ $combo->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thống kê</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-cube text-blue-600 mr-3"></i>
                    <span class="text-gray-700">Số sản phẩm</span>
                </div>
                <span class="text-xl font-bold text-blue-600">{{ $combo->comboItems->count() }}</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-percentage text-green-600 mr-3"></i>
                    <span class="text-gray-700">Tỷ lệ giảm giá</span>
                </div>
                <span class="text-xl font-bold text-green-600">
                    {{ $combo->actual_value > 0 ? round((1 - $combo->price / $combo->actual_value) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Hình ảnh (nếu có) -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hình ảnh</h3>
        <div class="flex items-center justify-center h-40 bg-gray-100 rounded-lg">
            @if($combo->image)
                <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" class="max-h-full max-w-full rounded-lg">
            @else
                <div class="text-center text-gray-500">
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <p class="text-sm">Chưa có hình ảnh</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Danh sách sản phẩm trong combo -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">Sản phẩm trong combo</h3>
        <p class="text-gray-600 mt-1">Danh sách các sản phẩm và số lượng trong combo</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($combo->comboItems as $index => $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cube text-gray-500"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Không xác định' }}</div>
                                <div class="text-sm text-gray-500">{{ $item->product->product_code ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $item->quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->product->price, 0, ',', '.') }}₫</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($item->getTotalPrice(), 0, ',', '.') }}₫</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Tổng giá trị combo:</td>
                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-green-600">
                        {{ number_format($combo->price, 0, ',', '.') }}₫
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Action buttons -->
<div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
    <a href="{{ route('admin.combos.edit', $combo->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-edit mr-2"></i>
        Chỉnh sửa Combo
    </a>
    <a href="{{ route('admin.combos.index') }}" 
       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-list mr-2"></i>
        Danh sách Combo
    </a>
</div>
@endsection