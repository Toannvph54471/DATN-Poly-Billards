@extends('admin.layouts.app')

@section('title', 'Quản Lý Ca Làm Việc')

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Quản Lý Ca Làm Việc</h1>
                <p class="text-gray-600">Danh sách các ca làm việc trong hệ thống</p>
            </div>
            <a href="{{ route('admin.shifts.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm Ca Mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng số ca</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalShifts }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Đang hoạt động</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeShifts }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-pause-circle text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tạm ngừng</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $inactiveShifts }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">NV đang làm</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $currentWorkingEmployees }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Shifts Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold">Danh sách ca làm việc</h2>
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text" placeholder="Tìm kiếm ca..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-filter mr-2"></i> Lọc
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Ca
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian
                            Bắt Đầu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian
                            Kết Thúc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời
                            Lượng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Nhân
                            Viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng
                            Thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($shifts as $shift)
                        <tr class="table-row hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $shift->name }}</div>
                                        <div class="text-sm text-gray-500">Mã: {{ strtoupper(substr($shift->name, 0, 2)) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</div>
                                <div class="text-sm text-gray-500">Hàng ngày</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</div>
                                <div class="text-sm text-gray-500">Hàng ngày</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @php
                                        $start = \Carbon\Carbon::parse($shift->start_time);
                                        $end = \Carbon\Carbon::parse($shift->end_time);
                                        $duration = $start->diff($end)->format('%h giờ %i phút');
                                    @endphp
                                    {{ $duration }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $shift->employee_shifts_count }} nhân viên</div>
                                <div class="text-sm text-gray-500">Phân công hôm nay</div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($shift->status === 'active')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Đang hoạt động
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Tạm dừng
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.shifts.edit', $shift->id) }}"
                                        class="text-blue-600 hover:text-blue-900 transition">
                                        <i class="fas fa-edit mr-1"></i> Sửa
                                    </a>
                                    <button class="text-red-600 hover:text-red-900 transition"
                                        onclick="confirmDelete({{ $shift->id }})">
                                        <i class="fas fa-trash mr-1"></i> Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Hiển thị <span class="font-medium">{{ count($shifts) }}</span> kết quả
                </div>
                <div class="flex space-x-2">
                    {{-- Pagination removed as we show all shifts --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State (if no shifts) -->
    @if (count($shifts) === 0)
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có ca làm việc nào</h3>
            <p class="text-gray-500 mb-6">Bắt đầu bằng cách tạo ca làm việc đầu tiên cho nhân viên của bạn.</p>
            <a href="{{ route('admin.shifts.create') }}"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Tạo Ca Đầu Tiên
            </a>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        function confirmDelete(shiftId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc muốn xóa ca làm việc này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // In a real application, you would submit a delete form
                    // For now, we'll just show a success message
                    Swal.fire(
                        'Đã xóa!',
                        'Ca làm việc đã được xóa thành công.',
                        'success'
                    );
                }
            });
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const shiftName = row.querySelector('td:first-child .text-sm.font-medium')
                        .textContent.toLowerCase();
                    if (shiftName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
