@extends('admin.layouts.app')

@section('title', 'Quản lý loại/giá bàn - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Loại/Giá bàn</h1>
            <p class="text-gray-600">Quản lý thông tin và bảng giá cho từng loại bàn</p>
        </div>
        <div>
            <a href="{{ route('admin.table_rates.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm loại bàn
            </a>
            <br>
            <a href="{{ route('admin.table_rates.trashed') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition mt-2 inline-block">
                <i class="fas fa-trash-restore mr-1"></i> Đã xóa
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng loại bàn</p>
                    {{-- <p class="text-xl font-bold text-gray-800">{{ $totalRates }}</p> --}}
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chair text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang áp dụng</p>
                    {{-- <p class="text-xl font-bold text-gray-800">{{ $activeRates }}</p> --}}
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngừng áp dụng</p>
                    {{-- <p class="text-xl font-bold text-gray-800">{{ $inactiveRates }}</p> --}}
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.table_rates.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tên hoặc mã loại bàn...">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Đang áp dụng</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Ngừng áp dụng</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                    <a href="{{ route('admin.table_rates.index') }}"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên loại bàn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá/giờ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ tối đa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rates as $rate)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $rate->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $rate->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ number_format($rate->hourly_rate, 0, ',', '.') }}đ</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $rate->max_hours ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $rate->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $rate->status == 'Active' ? 'Đang áp dụng' : 'Ngừng áp dụng' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.table_rates.edit', $rate->id) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $rate->id }}" action="{{ route('admin.table_rates.destroy', $rate->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $rate->id }})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <p class="mb-4">Chưa có loại bàn nào.</p>
                                <a href="{{ route('admin.table_rates.create') }}"
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                   Thêm loại bàn đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rates->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $rates->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    function confirmDelete(rateId) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc chắn muốn xóa loại bàn này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + rateId).submit();
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
