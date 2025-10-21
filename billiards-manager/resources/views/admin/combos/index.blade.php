@extends('admin.layouts.app')

@section('title', 'Quản lý Combo - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Combo</h1>
            <p class="text-gray-600">Danh sách combo và thông tin liên quan</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.combos.create', request()->query()) }}"
                class="bg-indigo-600 text-white rounded-lg px-4 py-2 hover:bg-indigo-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm Combo
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng Combo</p>
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
                    <p class="text-gray-500 text-sm">Ngừng hoạt động</p>
                    <p class="text-xl font-bold text-gray-800">{{ $inactiveCombos }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.combos.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search by Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã Combo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-gray-400"></i>
                        </div>
                        <input type="text" name="code" id="code" value="{{ request('code') }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Tìm theo mã combo">
                    </div>
                </div>

                <!-- Search by Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Combo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        <input type="text" name="name" id="name" value="{{ request('name') }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Tìm theo tên">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                            class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-6 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Combos Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Combo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị thực</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($combos as $combo)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $combo->combo_code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $combo->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($combo->price, 0, ',', '.') }} VND</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($combo->actual_value, 0, ',', '.') }} VND</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $combo->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $combo->status === 'Active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.combos.show', $combo->id) }}"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                   class="text-green-600 hover:text-green-900 transition"
                                   title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $combo->id }}"
                                      action="{{ route('admin.combos.destroy', $combo->id) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-combo-btn text-red-600 hover:text-red-900 transition"
                                            data-combo-id="{{ $combo->id }}"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 px-6 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-boxes text-gray-400 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Không có combo nào</h3>
                                <p class="text-gray-500 mb-4">Không tìm thấy combo phù hợp với tiêu chí tìm kiếm.</p>
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
            {{ $combos->links('pagination::tailwind') }}
        </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation cho button delete
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-combo-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-combo-btn');
                const comboId = button.getAttribute('data-combo-id');
                if (!comboId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Không thể xóa combo này.',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn có chắc chắn muốn xóa combo này? Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form-' + comboId);
                        if (form) {
                            form.submit();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Form xóa không tìm thấy.',
                            });
                        }
                    }
                });
            }
        });
    });
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

    tbody tr {
        transition: background-color 0.2s ease;
    }

    tbody tr:hover {
        background-color: #f9fafb !important; /* bg-gray-50 */
    }
</style>