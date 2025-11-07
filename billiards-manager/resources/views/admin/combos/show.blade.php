@extends('admin.layouts.app')

@section('title', 'Chi tiết Combo')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md flex items-center justify-center">
                    <i class="fas fa-layer-group text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $combo->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-600 font-mono">{{ $combo->combo_code }}</span>
                        @if($combo->is_time_combo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-clock mr-1"></i>Combo bàn
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-shopping-basket mr-1"></i>Combo thường
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $combo->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $combo->status == 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            {{ $combo->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.combos.edit', $combo->id) }}"
                   class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 font-medium transition flex items-center">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
                <a href="{{ route('admin.combos.index') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 font-medium transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    @if($combo->is_time_combo && $activeSession)
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
            <div class="flex items-start">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hourglass-half text-white"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-blue-900 font-semibold">Session đang chạy</h4>
                    <p class="text-blue-700 text-sm mt-0.5">Bàn đang sử dụng combo này. Thời gian còn lại: <strong>{{ $activeSession->remaining_minutes ?? 'N/A' }} phút</strong></p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Products List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list text-gray-600 mr-2"></i>
                        Sản phẩm trong combo
                    </h3>
                    <span class="text-sm text-gray-600">{{ $combo->comboItems->count() }} món</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($combo->comboItems as $item)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3 flex-1">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-green-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 truncate">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->product->product_code }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-semibold text-gray-900">
                                    <span class="text-blue-600">{{ $item->quantity }}</span> × 
                                    {{ number_format($item->unit_price) }}đ
                                </p>
                                <p class="text-xs text-gray-500">= {{ number_format($item->quantity * $item->unit_price) }}đ</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Tổng giá sản phẩm</span>
                    <span class="text-base font-bold text-gray-900">{{ number_format($combo->getProductsTotal()) }}đ</span>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-align-left text-gray-600 mr-2"></i>
                    Mô tả
                </h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    {{ $combo->description ?: 'Không có mô tả' }}
                </p>
            </div>

            <!-- Time Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="text-xs text-gray-600 font-medium uppercase tracking-wider">Ngày tạo</label>
                        <p class="mt-1 text-gray-900 flex items-center">
                            <i class="fas fa-calendar text-gray-400 mr-2 text-xs"></i>{{ $combo->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 font-medium uppercase tracking-wider">Cập nhật</label>
                        <p class="mt-1 text-gray-900 flex items-center">
                            <i class="fas fa-sync text-gray-400 mr-2 text-xs"></i>{{ $combo->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Pricing Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-sm border border-blue-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tag text-blue-600 mr-2"></i>
                    Giá trị combo
                </h3>
                <div class="space-y-3">
                    <!-- Sale Price -->
                    <div class="bg-white rounded-lg p-4 border-2 border-blue-300">
                        <span class="text-xs text-gray-600 block mb-1">Giá bán cho khách</span>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($combo->price) }}đ</span>
                    </div>

                    <!-- Original Price -->
                    <div class="flex items-center justify-between py-2 border-b border-blue-200">
                        <span class="text-sm text-gray-700">Giá trị thực tế</span>
                        <span class="text-base text-gray-500 line-through font-medium">{{ number_format($combo->actual_value) }}đ</span>
                    </div>

                    <!-- Discount -->
                    @if($combo->getDiscountAmount() > 0)
                        <div class="bg-green-100 rounded-lg p-3 border border-green-300">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-green-800">
                                    <i class="fas fa-gift mr-1"></i>Khách tiết kiệm
                                </span>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">{{ number_format($combo->getDiscountAmount()) }}đ</div>
                                    <div class="text-xs text-green-600 font-medium">({{ $combo->getDiscountPercent() }}% OFF)</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Time Combo Details -->
            @if($combo->is_time_combo)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        Thông tin bàn
                    </h3>
                    <div class="space-y-3">
                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                            <label class="text-xs text-purple-700 font-medium uppercase tracking-wider">Loại bàn</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                <i class="fas fa-table text-purple-600 mr-2"></i>{{ $combo->tableCategory?->name ?? 'Tất cả' }}
                            </p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                            <label class="text-xs text-purple-700 font-medium uppercase tracking-wider">Thời gian chơi</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                <i class="fas fa-hourglass-half text-purple-600 mr-2"></i>{{ $combo->play_duration_minutes }} phút
                            </p>
                        </div>
                        @if($combo->is_time_combo && $combo->getTablePrice() > 0)
                            <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                <label class="text-xs text-purple-700 font-medium uppercase tracking-wider">Giá bàn</label>
                                <p class="mt-1 text-base font-bold text-purple-600">
                                    {{ number_format($combo->getTablePrice()) }}đ
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Thao tác</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.combos.edit', $combo->id) }}"
                       class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 font-medium transition flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa combo
                    </a>
                    <form action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST" id="delete-form">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                                class="w-full bg-red-50 text-red-700 px-4 py-2.5 rounded-lg hover:bg-red-100 font-medium transition flex items-center justify-center border border-red-200">
                            <i class="fas fa-trash mr-2"></i>Xóa combo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Xác nhận xóa combo?',
        text: "Combo sẽ được chuyển vào thùng rác.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endsection