@extends('layouts.admin')

@section('title', 'Quản lý nhân viên - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý nhân viên</h1>
            <p class="text-gray-600">Danh sách nhân viên và thông tin liên quan</p>
        </div>
        <div class="flex space-x-3">
            <a href="#" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-calendar mr-2"></i> Phân công ca làm
            </a>
            <a href="#" class="bg-green-600 text-white rounded-lg px-4 py-2 hover:bg-green-700 transition flex items-center">
                <i class="fas fa-clock mr-2"></i> Danh sách làm việc
            </a>
            <a href="{{ route('admin.employees.create') }}"
                class="bg-indigo-600 text-white rounded-lg px-4 py-2 hover:bg-indigo-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm nhân viên
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng nhân viên</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalEmployees }}</p>
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
                    <p class="text-xl font-bold text-gray-800">{{ $activeEmployees }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Nhân viên mới</p>
                    <p class="text-xl font-bold text-gray-800">{{ $newEmployees }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngừng hoạt động</p>
                    <p class="text-xl font-bold text-gray-800">{{ $inactiveEmployees }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.employees.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search by Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-gray-400"></i>
                        </div>
                        <input type="text" name="code" value="{{ request('code') }}" placeholder="Tìm theo mã..."
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 pl-10 text-gray-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Search by Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên nhân viên</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" value="{{ request('name') }}" placeholder="Tìm theo tên..."
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 pl-10 text-gray-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-6 py-3 hover:bg-blue-700 transition w-full">
                        <i class="fas fa-search mr-2"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chức vụ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SĐT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($employees as $employee)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->employee_code ?? 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->name ?? 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position ?? 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->phone ?? 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $employee->status == 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $employee->status == 0 ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.employees.show', $employee->id) }}"
   class="text-blue-600 hover:text-blue-900 transition"
   title="Chi tiết">
    <i class="fas fa-eye"></i>
</a>
                    <a href="{{ route('admin.employees.edit', $employee->id) }}"
   class="text-green-600 hover:text-green-900 transition"
   title="Chỉnh sửa">
    <i class="fas fa-edit"></i>
</a>
                    <form id="delete-form-{{ $employee->id }}"
      action="{{ route('admin.employees.destroy', $employee->id) }}"
      method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="button" class="text-red-600 hover:text-red-900 transition"
            title="Xóa" onclick="confirmDelete({{ $employee->id }})">
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
                        <i class="fas fa-users text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có nhân viên nào</h3>
                    <p class="text-gray-500 mb-4">Không tìm thấy nhân viên phù hợp với tiêu chí tìm kiếm.</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($employees->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        function confirmDelete(employeeId) {
    if (!employeeId) {
        alert('Không thể xóa nhân viên này.');
        return;
    }
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa nhân viên này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + employeeId).submit();
        }
    });
        }
    </script>
@endsection
