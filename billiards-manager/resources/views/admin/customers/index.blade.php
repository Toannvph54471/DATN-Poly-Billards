@extends('admin.layouts.app')

@section('title', 'Quản lý khách hàng - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý khách hàng</h1>
            <p class="text-gray-600">Quản lý thông tin và lịch sử sử dụng dịch vụ của khách hàng</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.customers.trashed') }}" 
               class="bg-yellow-500 text-white rounded-lg px-4 py-2 hover:bg-yellow-600 transition flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Đã xóa ({{ $trashedCount ?? 0 }})
            </a>
            <a href="{{ route('admin.customers.create') }}" 
               class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm khách hàng
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng khách hàng</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalCustomers }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang hoạt động</p>
                    <p class="text-xl font-bold text-gray-800">{{ $activeCount ?? 0 }}</p>
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
                    <p class="text-xl font-bold text-gray-800">{{ $inactiveCount ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Khách VIP</p>
                    <p class="text-xl font-bold text-gray-800">{{ $vipCount }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Khách mới (tháng)</p>
                    <p class="text-xl font-bold text-gray-800">{{ $newThisMonthCount }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.customers.index') }}" method="GET">
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
                            placeholder="Tên, email, số điện thoại...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                </div>

                <!-- Customer Type Filter -->
                <div>
                    <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Loại khách hàng</label>
                    <select name="customer_type" id="customer_type"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả loại</option>
                        <option value="New" {{ request('customer_type') == 'New' ? 'selected' : '' }}>Mới</option>
                        <option value="Regular" {{ request('customer_type') == 'Regular' ? 'selected' : '' }}>Thường xuyên</option>
                        <option value="VIP" {{ request('customer_type') == 'VIP' ? 'selected' : '' }}>VIP</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                    <a href="{{ route('admin.customers.index') }}"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center"
                        title="Làm mới bộ lọc">
                        <i class="fas fa-redo mr-2"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thông tin liên hệ</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Loại khách</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày đăng ký</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium">
                                                {{ substr($customer->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $customer->email ?? 'Chưa có email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">
                                    @if ($customer->phone)
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                            <span>{{ $customer->phone }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Chưa cập nhật</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($customer->customer_type === 'VIP')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-crown mr-1"></i>
                                        VIP
                                    </span>
                                @elseif($customer->customer_type === 'Regular')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-user-check mr-1"></i>
                                        Thường xuyên
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-plus mr-1"></i>
                                        Mới
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if ($customer->status == 'Active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Đang hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Ngừng hoạt động
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $customer->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                       class="text-green-600 hover:text-green-900 transition" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-900 transition" 
                                            title="Xóa" onclick="confirmDelete({{ $customer->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <!-- Delete Form (hidden) -->
                                    <form id="delete-form-{{ $customer->id }}" 
                                          action="{{ route('admin.customers.destroy', $customer->id) }}" 
                                          method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có khách hàng nào</h3>
                                    <p class="text-gray-500 mb-4">Không tìm thấy khách hàng phù hợp với tiêu chí tìm kiếm.</p>
                                    <a href="{{ route('admin.customers.create') }}" 
                                       class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Thêm khách hàng mới
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($customers->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(customerId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa khách hàng này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + customerId).submit();
                }
            });
        }

        // Auto submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const typeSelect = document.getElementById('customer_type');

            // Tự động submit khi thay đổi filter (tùy chọn)
            statusSelect.addEventListener('change', function() {
                this.form.submit();
            });
            
            typeSelect.addEventListener('change', function() {
                this.form.submit();
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

    .table-row {
        transition: background-color 0.2s ease;
    }
</style>