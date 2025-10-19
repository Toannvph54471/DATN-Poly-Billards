@extends('admin.layouts.app')

@section('title', 'Quản lý vai trò - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý vai trò</h1>
            <p class="text-gray-600">Quản lý phân quyền và vai trò người dùng hệ thống</p>
        </div>
        <div>
            <a href=""
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm vai trò
            </a>
        </div>
    </div>


    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng vai trò</p>
                    <p class="text-xl font-bold text-gray-800"></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tag text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang hoạt động</p>
                    <p class="text-xl font-bold text-gray-800"></p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Quyền hệ thống</p>
                    <p class="text-xl font-bold text-gray-800">25</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Vai trò
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Mô tả
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Số quyền
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Người tạo
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày tạo
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user-tag text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $role->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ $role->description ?? 'Chưa có mô tả' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $permissionsCount = count(json_decode($role->permissions, true) ?? []);
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $permissionsCount }} quyền
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">
                                    {{ $role->creator->name ?? 'System' }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $role->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    <a href="" class="text-blue-600 hover:text-blue-900 transition"
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-900 transition" title="Xóa"
                                        onclick="confirmDelete({{ $role->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $role->id }}" action="" method="POST" class="hidden">
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
                                        <i class="fas fa-user-tag text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có vai trò nào</h3>
                                    <p class="text-gray-500 mb-4">Không tìm thấy vai trò phù hợp với tiêu chí tìm kiếm.</p>
                                    <a href="{{ route('admin.roles.create') }}"
                                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Thêm vai trò đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->

    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(roleId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa vai trò này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + roleId).submit();
                }
            });
        }
    </script>
@endsection
