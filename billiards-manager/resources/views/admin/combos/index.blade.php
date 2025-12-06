@extends('admin.layouts.app')

@section('title', 'Quản lý Combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Combo</h1>
            <p class="text-gray-600">Tạo và quản lý các combo ưu đãi cho khách hàng</p>
        </div>
        <div>
            <a href="{{ route('admin.combos.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tạo combo mới
            </a>
            <br>
            <a href="{{ route('admin.combos.trash') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition mt-2 inline-block">
                <i class="fas fa-trash-restore mr-1"></i> Thùng rác
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng combo</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang hoạt động</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tạm dừng</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['inactive'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Combo bàn</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['time_combos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.combos.index') }}" class="mb-6 bg-white p-4 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc mã combo..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
            </select>

            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả loại</option>
                <option value="time" {{ request('type') == 'time' ? 'selected' : '' }}>Combo bàn</option>
                <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Combo thường</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Lọc
            </button>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.combos.index') }}"
                class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                <i class="fas fa-redo mr-1"></i> Xóa bộ lọc
            </a>
        </div>
    </form>

    <!-- Combos Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Combo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số món
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá bán
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiết kiệm
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành
                            động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($combos as $combo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $combo->name }}</div>
                                @if ($combo->is_time_combo)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-clock mr-1"></i>{{ $combo->play_duration_minutes }} phút
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">#{{ $combo->combo_code }}</td>
                            <td class="px-6 py-4">
                                @if ($combo->is_time_combo)
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-clock mr-1"></i>Combo bàn
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-shopping-basket mr-1"></i>Thường
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $combo->comboItems->count() }} món</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ number_format($combo->price) }}đ</div>
                                <div class="text-xs text-gray-500 line-through">{{ number_format($combo->actual_value) }}đ
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($combo->getDiscountAmount() > 0)
                                    <div class="text-sm font-medium text-green-600">
                                        {{ number_format($combo->getDiscountAmount()) }}đ</div>
                                    <div class="text-xs text-green-600">({{ $combo->getDiscountPercent() }}%)</div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($combo->status == 'active')
                                    <span class="inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                        <span class="text-sm text-green-700">Hoạt động</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                        <span class="text-sm text-red-700">Tạm dừng</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.combos.show', $combo->id) }}"
                                    class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $combo->id }}"
                                    action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $combo->id }})"
                                        class="text-red-600 hover:text-red-800" title="Xóa combo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="mb-4">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                    <p class="mb-2">
                                        @if (request()->hasAny(['search', 'status', 'type']))
                                            Không tìm thấy combo phù hợp
                                        @else
                                            Chưa có combo nào
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-400 mb-4">
                                        @if (request()->hasAny(['search', 'status', 'type']))
                                            Không tìm thấy combo phù hợp với tiêu chí tìm kiếm.
                                        @else
                                            Bắt đầu tạo combo đầu tiên để cung cấp ưu đãi cho khách hàng.
                                        @endif
                                    </p>
                                    @if (request()->hasAny(['search', 'status', 'type']))
                                        <a href="{{ route('admin.combos.index') }}"
                                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                                            <i class="fas fa-redo mr-1"></i>Xóa bộ lọc
                                        </a>
                                    @else
                                        <a href="{{ route('admin.combos.create') }}"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-plus mr-1"></i>Tạo combo đầu tiên
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($combos->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $combos->links() }}
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

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
</style>
