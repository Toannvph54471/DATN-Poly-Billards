@extends('admin.layouts.app')

@section('title', 'Quản lý bàn - Billiards Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý bàn</h1>
            <p class="text-gray-600">Danh sách các bàn billiards trong hệ thống</p>
        </div>
        <div>
            <a href=""
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm bàn mới
            </a>
            <a href="{{ route('admin.tables.trashed') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center ml-2">
                <i class="fas fa-trash-restore mr-2"></i>
                Danh bàn ẩn
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng số bàn</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalTables }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-table text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang sử dụng</p>
                    <p class="text-xl font-bold text-gray-800">{{ $inUseCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Bảo trì</p>
                    <p class="text-xl font-bold text-gray-800">{{ $maintenanceCount }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Trống</p>
                    <p class="text-xl font-bold text-gray-800">{{ $availableCount }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-circle text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ url()->current() }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tên bàn, số bàn...">
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Loại bàn</label>
                    <select name="type" id="type"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả loại</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Trống</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>Đang sử dụng</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì
                        </option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                    <a href="{{ url()->current() }}"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tables List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if ($tables->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bàn
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loại
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Giá/giờ
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vị trí
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($tables as $table)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.tables.detail', $table->id) }}" class="block">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full flex items-center justify-center 
                                                    @if ($table->status == 'available') bg-gray-200 text-gray-700
                                                    @elseif($table->status == 'in_use') bg-green-200 text-green-700
                                                    @else bg-yellow-200 text-yellow-700 @endif">
                                                    <i class="fas fa-table"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $table->table_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $table->table_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                        {{ $table->type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($table->hourly_rate, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $table->position ?? 'Không rõ' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($table->status == 'available')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle mr-1 text-gray-500"></i>
                                            Trống
                                        </span>
                                    @elseif($table->status == 'in_use')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-play-circle mr-1 text-green-500"></i>
                                            Đang sử dụng
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-tools mr-1 text-yellow-500"></i>
                                            Bảo trì
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.tables.edit', $table->id) }}"
                                            class="text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg px-3 py-1.5 text-xs font-medium transition flex items-center">
                                            <i class="fas fa-edit mr-1"></i>
                                            Sửa
                                        </a>

                                        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa bàn này không?');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 bg-red-100 hover:bg-red-200 rounded-lg px-3 py-1.5 text-xs font-medium transition flex items-center">
                                                <i class="fas fa-trash mr-1"></i>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-table text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có bàn nào</h3>
                <p class="text-gray-500 mb-6">Không tìm thấy bàn phù hợp với tiêu chí lọc hiện tại.</p>
                <a href="{{ route('admin.tables.create') }}"
                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm bàn mới
                </a>
            </div>
        @endif

        <!-- Pagination -->
        @if ($tables->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tables->links() }}
            </div>
        @endif
    </div>
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

    /* Hover effect for table rows */
    tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Link styling for table name */
    a.block:hover .text-gray-900 {
        color: #3b82f6;
    }
</style>
