@extends('admin.layouts.app')

@section('title', 'Quản lý Combo - F&B Management')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.combos.trash') }}"
                    class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Thùng rác
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
                                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition shadow-md flex items-center">
                                        <i class="fas fa-plus mr-2"></i>Tạo combo mới
                                    </a>
                                    <a href="{{ route('admin.combos.trash') }}"
                                        class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-50 font-medium transition flex items-center">
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
                                        <h1 class="text-3xl font-bold text-gray-900 mb-1">Quản lý Combo</h1>
                                        <p class="text-gray-600">Tạo và quản lý các combo ưu đãi cho khách hàng</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.combos.create') }}"
                                            class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition shadow-md flex items-center">
                                            <i class="fas fa-plus mr-2"></i>Tạo combo mới
                                        </a>
                                        <a href="{{ route('admin.combos.trash') }}"
                                            class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-50 font-medium transition flex items-center">
                                            <i class="fas fa-trash mr-2"></i>Thùng rác
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                <div
                                    class="bg-white rounded-lg p-5 border-l-4 border-blue-500 shadow-sm hover:shadow transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-gray-600 text-sm mb-1">Tổng combo</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-layer-group text-blue-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white rounded-lg p-5 border-l-4 border-green-500 shadow-sm hover:shadow transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-gray-600 text-sm mb-1">Đang hoạt động</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white rounded-lg p-5 border-l-4 border-red-500 shadow-sm hover:shadow transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-gray-600 text-sm mb-1">Tạm dừng</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-pause-circle text-red-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-white rounded-lg p-5 border-l-4 border-purple-500 shadow-sm hover:shadow transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-gray-600 text-sm mb-1">Combo bàn</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $stats['time_combos'] }}</p>
                                        </div>
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filters -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
                                <form method="GET" action="{{ route('admin.combos.index') }}"
                                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Tên hoặc mã combo..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                                        <select name="status"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Tất cả</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Hoạt động</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Loại combo</label>
                                        <select name="type"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Tất cả</option>
                                            <option value="time" {{ request('type') == 'time' ? 'selected' : '' }}>Combo
                                                bàn</option>
                                            <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>
                                                Combo thường</option>
                                        </select>
                                    </div>

                                    <div class="flex items-end gap-2">
                                        <button type="submit"
                                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium transition">
                                            <i class="fas fa-search mr-2"></i>Lọc
                                        </button>
                                        <a href="{{ route('admin.combos.index') }}"
                                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 font-medium transition">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </form>
                            </div>

                            <!-- Combos Table -->
                            @if ($combos->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Combo</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Loại</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Sản phẩm</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Giá bán</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Tiết kiệm</th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Trạng thái</th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                        Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach ($combos as $combo)
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td class="px-6 py-4">
                                                            <div>
                                                                <a href="{{ route('admin.combos.show', $combo->id) }}"
                                                                    class="font-semibold text-gray-900 hover:text-blue-600 transition">
                                                                    {{ $combo->name }}
                                                                </a>
                                                                <p class="text-xs text-gray-500 font-mono mt-0.5">
                                                                    {{ $combo->combo_code }}</p>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            @if ($combo->is_time_combo)
                                                                <div>
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                                                        <i class="fas fa-clock mr-1"></i>Combo bàn
                                                                    </span>
                                                                    <p class="text-xs text-gray-600 mt-1">
                                                                        {{ $combo->play_duration_minutes }}p
                                                                    </p>
                                                                </div>
                                                            @else
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                                                    <i class="fas fa-shopping-basket mr-1"></i>Thường
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span
                                                                class="text-sm text-gray-900 font-medium">{{ $combo->comboItems->count() }}
                                                                món</span>
                                                        </td>
                                                        <td class="px-6 py-4 text-right">
                                                            <div class="text-base font-bold text-gray-900">
                                                                {{ number_format($combo->price) }}đ
                                                            </div>
                                                            <div class="text-xs text-gray-500 line-through">
                                                                {{ number_format($combo->actual_value) }}đ</div>
                                                        </td>
                                                        <td class="px-6 py-4 text-right">
                                                            @if ($combo->getDiscountAmount() > 0)
                                                                <div class="text-sm font-semibold text-green-600">
                                                                    {{ number_format($combo->getDiscountAmount()) }}đ</div>
                                                                <div class="text-xs text-green-600">
                                                                    ({{ $combo->getDiscountPercent() }}%)
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-400">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <span class="inline-flex items-center">
                                                                <span
                                                                    class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $combo->status == 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                                                <span
                                                                    class="text-sm font-medium {{ $combo->status == 'active' ? 'text-green-700' : 'text-red-700' }}">
                                                                    {{ $combo->status == 'active' ? 'Đang hoạt động' : 'Tạm dừng' }}
                                                                </span>
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center justify-center gap-2">
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
                                </div>

                                @if ($combos->hasPages())
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
                                        {{ $combos->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
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
                                                class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-300 transition font-medium">
                                                <i class="fas fa-redo mr-2"></i>Xóa bộ lọc
                                            </a>
                                        @else
                                            <a href="{{ route('admin.combos.create') }}"
                                                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md font-medium">
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
