@extends('admin.layouts.app')

@section('title', 'Quản lý combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý combo</h1>
            <p class="text-gray-600">Quản lý thông tin và giá trị combo</p>
        </div>
        <div>
            <a href="{{ route('admin.combos.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm combo
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng combo</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalCombos }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang hoạt động</p>
                    <p class="text-xl font-bold text-gray-800">{{ $activeCombos }}</p>
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
                    <p class="text-xl font-bold text-gray-800">{{ $inactiveCombos }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.combos.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã combo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-gray-400"></i>
                        </div>
                        <input type="text" name="code" id="code" value="{{ request('code') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Mã combo...">
                    </div>
                </div>

                <!-- Search Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên combo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="name" value="{{ request('name') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tên combo...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                    <a href="{{ route('admin.combos.index') }}"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Combos Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Combo</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thông tin giá</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($combos as $combo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($combo->image)
                                            <img class="h-12 w-12 rounded-lg object-cover" src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-gift text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $combo->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">#{{ $combo->combo_code ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm">
                                    <div class="mb-1">
                                        <span class="font-medium text-gray-900">{{ number_format($combo->price, 0, ',', '.') }} đ</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Giá trị thực:</span>
                                        <span class="ml-1 text-gray-600">
                                            {{ number_format($combo->actual_value, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($combo->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Đang hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Tạm dừng
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $combo->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.combos.show', $combo->id) }}"
                                        class="text-blue-600 hover:text-blue-900 transition" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                        class="text-green-600 hover:text-green-900 transition" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-900 transition"
                                        title="Xóa" onclick="confirmDelete({{ $combo->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $combo->id }}" 
                                          action="{{ route('admin.combos.destroy', $combo->id) }}" 
                                          method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-gift text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có combo nào</h3>
                                    <p class="text-gray-500 mb-4">Không tìm thấy combo phù hợp với tiêu chí tìm kiếm.</p>
                                    <a href="{{ route('admin.combos.create') }}" 
                                       class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Thêm combo đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
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
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc chắn muốn xóa combo này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
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

    .table-row {
        transition: background-color 0.2s ease;
    }
</style>