@extends('admin.layouts.app')

@section('title', 'Quản Lý Combo Đặt Bàn - F&B Management')

@section('content')
    <!-- Page Header -->
     <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Quản Lý Combo Đặt Bàn</h1>
        <p class="text-gray-600 mt-1">Danh sách combo và thông tin liên quan</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.combos.trashed') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 font-medium shadow-sm">
           <i class="fas fa-trash mr-2"></i> Thùng Rác
        </a>
        <a href="{{ route('admin.combos.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium shadow-sm">
           <i class="fas fa-plus mr-2"></i> Thêm Combo
        </a>
    </div>
</div>

   

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Tổng Combo</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalCombos }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Đang Hoạt Động</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeCombos }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Tạm Dừng</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $inactiveCombos }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
        <form action="{{ route('admin.combos.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã Combo</label>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="code" value="{{ request('code') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="Tìm theo mã combo">
                    </div>
                </div>

                <!-- Search Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên Combo</label>
                    <div class="relative">
                        <i class="fas fa-box absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="name" value="{{ request('name') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="Tìm theo tên">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng Thái</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">Tất Cả</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Đang Hoạt Động</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Tạm Dừng</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.combos.index') }}"
                    class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Xóa Bộ Lọc
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-search mr-2"></i> Tìm Kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Combos Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Mã Combo</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tên</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Giá Bán</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Giá Trị Thực</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Trạng Thái</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($combos as $combo)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $combo->combo_code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-800">
                                {{ $combo->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ number_format($combo->price, 0, ',', '.') }} đ
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ number_format($combo->actual_value, 0, ',', '.') }} đ
                            </td>
                            <td class="px-6 py-4">
                                @if ($combo->status === 'Active')
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-green-500 mr-1 text-xs"></i> Hoạt Động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-circle text-red-500 mr-1 text-xs"></i> Tạm Dừng
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.combos.show', $combo->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Chi Tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Chỉnh Sửa">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form id="delete-form-{{ $combo->id }}"
                                        action="{{ route('admin.combos.destroy', $combo->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-combo-btn inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            data-combo-id="{{ $combo->id }}" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-boxes text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-gray-900 font-semibold mb-1">Không Có Combo Nào</h3>
                                    <p class="text-gray-600 text-sm">Không tìm thấy combo phù hợp với tiêu chí tìm kiếm</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($combos->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $combos->links('pagination::tailwind') }}
        </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-combo-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-combo-btn');
                const comboId = button.getAttribute('data-combo-id');

                Swal.fire({
                    title: 'Xác Nhận Xóa',
                    text: 'Bạn có chắc chắn muốn xóa combo này? Hành động này không thể hoàn tác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form-' + comboId);
                        if (form) form.submit();
                    }
                });
            }
        });
    });
</script>
@endsection