@extends('admin.layouts.app')

@section('title', 'Quản lý Combo - F&B Management')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Combo</h1>
            <p class="text-gray-600 mt-1">Tạo và quản lý các combo ưu đãi cho khách hàng</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.combos.create') }}" 
               class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2.5 rounded-xl hover:from-blue-700 hover:to-blue-800 font-medium transition shadow-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>Tạo combo mới
            </a>
            <a href="{{ route('admin.combos.trash') }}" 
               class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-50 font-medium transition flex items-center">
                <i class="fas fa-trash mr-2"></i>Thùng rác
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">Tổng combo</p>
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-layer-group text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1">Đang hoạt động</p>
                <p class="text-3xl font-bold">{{ $stats['active'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium mb-1">Tạm dừng</p>
                <p class="text-3xl font-bold">{{ $stats['inactive'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-pause-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-1">Combo bàn</p>
                <p class="text-3xl font-bold">{{ $stats['time_combos'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('admin.combos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tên hoặc mã combo..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loại combo</label>
            <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="time" {{ request('type') == 'time' ? 'selected' : '' }}>Combo bàn</option>
                <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Combo thường</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium transition">
                <i class="fas fa-search mr-2"></i>Lọc
            </button>
            <a href="{{ route('admin.combos.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 font-medium transition">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Combos Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if($combos->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Combo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mã</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Giá bán</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ưu đãi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($combos as $combo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box-open text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.combos.show', $combo->id) }}" 
                                           class="font-semibold text-gray-900 hover:text-blue-600 transition">
                                            {{ $combo->name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if ($combo->is_time_combo)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-clock mr-1 text-xs"></i>{{ $combo->play_duration_minutes }}p
                                                </span>
                                            @endif
                                            @if ($combo->tableCategory)
                                                <span class="text-xs text-gray-500">
                                                    <i class="fas fa-table mr-1"></i>{{ $combo->tableCategory->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-gray-600">{{ $combo->combo_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $combo->comboItems->count() }} sản phẩm</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-bold text-gray-900">{{ number_format($combo->price) }}đ</div>
                                    <div class="text-gray-500 line-through text-xs">{{ number_format($combo->actual_value) }}đ</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($combo->getDiscountAmount() > 0)
                                    <div class="text-sm">
                                        <div class="font-semibold text-green-600">-{{ number_format($combo->getDiscountAmount()) }}đ</div>
                                        <div class="text-green-600 text-xs">({{ $combo->getDiscountPercent() }}%)</div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $combo->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $combo->status == 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ $combo->status == 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.combos.show', $combo->id) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.combos.edit', $combo->id) }}" 
                                       class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $combo->id }}" 
                                          action="{{ route('admin.combos.destroy', $combo->id) }}" 
                                          method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete({{ $combo->id }})" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" 
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($combos->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $combos->links() }}
            </div>
        @endif
    @else
        <div class="px-6 py-20 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-box-open text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Chưa có combo nào</h3>
                <p class="text-gray-600 mb-8 max-w-md">
                    @if(request()->hasAny(['search', 'status', 'type']))
                        Không tìm thấy combo phù hợp với tiêu chí tìm kiếm.
                    @else
                        Bắt đầu tạo combo đầu tiên để cung cấp ưu đãi cho khách hàng.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'status', 'type']))
                    <a href="{{ route('admin.combos.index') }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-300 transition">
                        <i class="fas fa-redo mr-2"></i>Xóa bộ lọc
                    </a>
                @else
                    <a href="{{ route('admin.combos.create') }}" 
                       class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Tạo combo đầu tiên
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(comboId) {
    Swal.fire({
        title: 'Xác nhận xóa combo?',
        text: "Combo sẽ được chuyển vào thùng rác và có thể khôi phục lại.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa combo',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy bỏ',
        customClass: {
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + comboId).submit();
        }
    });
}
</script>
@endsection