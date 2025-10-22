@extends('admin.layouts.app')

@section('title', 'Chi tiết nhân viên - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết nhân viên</h1>
            <p class="text-gray-600">Thông tin chi tiết về nhân viên {{ $employee->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>
            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            <a href="" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i> Phân công ca làm
            </a>
            <a href="" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-clipboard-list mr-2"></i> Danh sách làm việc
            </a>
        </div>
    </div>

    <!-- Employee Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">Thông tin cá nhân</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h4>
                        <p class="text-gray-600">{{ $employee->position }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên</label>
                        <p class="text-gray-900 font-medium">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <p class="text-gray-900 font-medium">{{ $employee->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900 font-medium">{{ $employee->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                        <p class="text-gray-900 font-medium">{{ $employee->address ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-4">Thông tin công việc</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                    <p class="text-gray-900 font-medium">{{ $employee->position }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại lương</label>
                    <p class="text-gray-900 font-medium">{{ $employee->salary_type == 'hourly' ? 'Part-time' : 'Lương cứng' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mức lương</label>
                    <p class="text-gray-900 font-medium">{{ number_format($employee->salary_rate, 0, ',', '.') }} VND/giờ</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng lương</label>
                    <p class="text-gray-900 font-medium">
                        {{ number_format($employee->employeeShifts()->sum('total_hours') * $employee->salary_rate, 0, ',', '.') }} VND
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                    <p class="text-gray-900 font-medium">{{ $employee->start_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $employee->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $employee->status === 'Active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Shifts -->
    <div class="bg-white rounded-xl shadow-sm mt-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Ca làm gần đây</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ca làm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($employee->employeeShifts()->latest()->limit(5)->get() as $shift)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $shift->shift_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $shift->shift->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $shift->actual_start_time ? $shift->actual_start_time->format('H:i') : $shift->shift->start_time }} - 
                            {{ $shift->actual_end_time ? $shift->actual_end_time->format('H:i') : $shift->shift->end_time }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $shift->status == 'Completed' ? 'bg-green-100 text-green-800' : ($shift->status == 'Absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $shift->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 px-6 text-center text-gray-500">Chưa có ca làm nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection