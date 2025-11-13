@extends('admin.layouts.app')

@section('title', 'Quản lý Combo - F&B Management')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Quản lý Combo</h1>
                    <p class="text-gray-600">Tạo và quản lý các combo ưu đãi cho khách hàng</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.combos.create') }}"
                        class="bg-gray-900 text-white px-5 py-2.5 hover:bg-gray-800 font-medium transition flex items-center border border-gray-900">
                        <i class="fas fa-plus mr-2"></i>Tạo combo mới
                    </a>
                    <a href="{{ route('admin.combos.trash') }}"
                        class="bg-white border border-gray-400 text-gray-700 px-5 py-2.5 hover:bg-gray-50 font-medium transition flex items-center">
                        <i class="fas fa-trash mr-2"></i>Thùng rác
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-300 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Tổng combo</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 flex items-center justify-center border border-blue-300">
                        <i class="fas fa-layer-group text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Đang hoạt động</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 flex items-center justify-center border border-green-300">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Tạm dừng</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 flex items-center justify-center border border-red-300">
                        <i class="fas fa-pause-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 p-5 hover:shadow transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Combo bàn</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['time_combos'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 flex items-center justify-center border border-purple-300">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white border border-gray-300 p-5 mb-6">
            <form method="GET" action="{{ route('admin.combos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc mã combo..."
                        class="w-full px-4 py-2 border border-gray-400 focus:border-gray-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-400 focus:border-gray-900 focus:outline-none">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            Hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại combo</label>
                    <select name="type"
                        class="w-full px-4 py-2 border border-gray-400 focus:border-gray-900 focus:outline-none">
                        <option value="">Tất cả</option>
                        <option value="time" {{ request('type') == 'time' ? 'selected' : '' }}>Combo
                            bàn</option>
                        <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>
                            Combo thường</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-gray-900 text-white px-4 py-2 hover:bg-gray-800 font-medium transition border border-gray-900">
                        <i class="fas fa-search mr-2"></i>Lọc
                    </button>
                    <a href="{{ route('admin.combos.index') }}"
                        class="bg-white border border-gray-400 text-gray-700 px-4 py-2 hover:bg-gray-50 font-medium transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Combos Table -->
        @if ($combos->count() > 0)
            <div class="bg-white border border-gray-300 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Combo
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Loại
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Sản phẩm
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Giá bán
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Tiết kiệm
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-sm font-bold text-gray-900 uppercase border-r border-gray-300">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-900 uppercase">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300">
                            @foreach ($combos as $combo)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 border-r border-gray-300">
                                        <div>
                                            <a href="{{ route('admin.combos.show', $combo->id) }}"
                                                class="font-semibold text-gray-900 hover:text-blue-600 transition">
                                                {{ $combo->name }}
                                            </a>
                                            <p class="text-xs text-gray-500 font-mono mt-0.5">
                                                {{ $combo->combo_code }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-300">
                                        @if ($combo->is_time_combo)
                                            <div>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 text-xs font-medium bg-purple-100 text-purple-800 border border-purple-300">
                                                    <i class="fas fa-clock mr-1"></i>Combo bàn
                                                </span>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    {{ $combo->play_duration_minutes }}p
                                                </p>
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-100 text-green-800 border border-green-300">
                                                <i class="fas fa-shopping-basket mr-1"></i>Thường
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-300">
                                        <span class="text-sm text-gray-900 font-medium">{{ $combo->comboItems->count() }}
                                            món</span>
                                    </td>
                                    <td class="px-6 py-4 text-right border-r border-gray-300">
                                        <div class="text-base font-bold text-gray-900">
                                            {{ number_format($combo->price) }}₫
                                        </div>
                                        <div class="text-xs text-gray-500 line-through">
                                            {{ number_format($combo->actual_value) }}₫
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right border-r border-gray-300">
                                        @if ($combo->getDiscountAmount() > 0)
                                            <div class="text-sm font-semibold text-green-600">
                                                {{ number_format($combo->getDiscountAmount()) }}₫
                                            </div>
                                            <div class="text-xs text-green-600">
                                                ({{ $combo->getDiscountPercent() }}%)
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center border-r border-gray-300">
                                        <span class="inline-flex items-center">
                                            <span
                                                class="w-2 h-2 mr-2 {{ $combo->status == 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                            <span
                                                class="text-sm font-medium {{ $combo->status == 'active' ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $combo->status == 'active' ? 'Đang hoạt động' : 'Tạm dừng' }}
                                            </span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('admin.combos.show', $combo->id) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 transition border border-transparent hover:border-blue-300"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                                class="p-2 text-yellow-600 hover:bg-yellow-50 transition border border-transparent hover:border-yellow-300"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $combo->id }}"
                                                action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $combo->id }})"
                                                    class="p-2 text-red-600 hover:bg-red-50 transition border border-transparent hover:border-red-300"
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
            </div>

            @if ($combos->hasPages())
                <div class="bg-white border border-gray-300 px-6 py-4">
                    {{ $combos->links() }}
                </div>
            @endif
        @else
            <div class="bg-white border border-gray-300 px-6 py-16 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-gray-100 border border-gray-300 flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có combo nào</h3>
                    <p class="text-gray-600 mb-6 max-w-md">
                        @if (request()->hasAny(['search', 'status', 'type']))
                            Không tìm thấy combo phù hợp với tiêu chí tìm kiếm.
                        @else
                            Bắt đầu tạo combo đầu tiên để cung cấp ưu đãi cho khách hàng.
                        @endif
                    </p>
                    @if (request()->hasAny(['search', 'status', 'type']))
                        <a href="{{ route('admin.combos.index') }}"
                            class="bg-white border border-gray-400 text-gray-700 px-6 py-2.5 hover:bg-gray-50 transition font-medium">
                            <i class="fas fa-redo mr-2"></i>Xóa bộ lọc
                        </a>
                    @else
                        <a href="{{ route('admin.combos.create') }}"
                            class="bg-gray-900 text-white px-8 py-3 hover:bg-gray-800 transition font-medium border border-gray-900">
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
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + comboId).submit();
                }
            });
        }
    </script>
@endsection
