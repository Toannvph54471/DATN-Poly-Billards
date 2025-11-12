@extends('admin.layouts.app')

@section('title', 'Quản lý danh mục - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý danh mục</h1>
            <p class="text-gray-600">Quản lý danh mục</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm danh mục
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng danh mục</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalCategories ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang kích hoạt</p>
                    <p class="text-xl font-bold text-gray-800">{{ $categoryActives ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngừng kích hoạt</p>
                    <p class="text-xl font-bold text-gray-800">{{ $categoriesInactive ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="#" method="GET">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <!-- Search -->
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Tên danh mục...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang kích hoạt</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng kích hoạt
                        </option>
                    </select>
                </div>
                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.index') }}"
                        class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center text-sm whitespace-nowrap">
                        <i class="fas fa-redo mr-2"></i>
                        Làm mới
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center text-sm whitespace-nowrap">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Promotions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Tên danh
                            mục</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Loại
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Giá giờ
                            chơi</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Mô tả
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày tạo
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if ($categories != null && count($categories) > 0)
                        @foreach ($categories as $category)
                            <tr>
                                <td class="py-4 px-6 text-sm text-gray-700">{{ $category->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-700">{{ $category->type }}</td>
                                <td class="py-4 px-6 text-sm text-gray-700">
                                    {{ number_format($category->hourly_rate, 0, ',', '.') }}đ</td>
                                <td class="py-4 px-6 text-sm">
                                    @if ($category->status == 'active')
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Đang
                                            kích hoạt</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ngừng
                                            kích hoạt</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-700">{{ $category->description }}</td>
                                <td class="py-4 px-6 text-sm text-gray-700">{{ $category->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}"
                                            class="text-green-600 hover:text-green-900 transition" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="text-red-600 hover:text-red-900 transition"
                                            title="Xóa" onclick="confirmDelete({{ $category->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <form id="delete-form-{{ $category->id }}"
                                            action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                                Không có dữ liệu
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function confirmDelete(promotionId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa danh mục này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + promotionId).submit();
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
