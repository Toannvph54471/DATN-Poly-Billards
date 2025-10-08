@extends('admin.layouts.app')

@section('title', 'Quản Lý Thành Viên - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản Lý Thành Viên</h1>
            <p class="text-gray-600">Danh sách thành viên billiards club</p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-plus mr-2"></i>Thêm thành viên
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-2"></i>Xuất Excel
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tổng thành viên</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalUser }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Đang hoạt động</p>
                    <p class="text-2xl font-bold text-gray-800">128</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-crown text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">VIP Members</p>
                    <p class="text-2xl font-bold text-gray-800">42</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-day text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Hôm nay</p>
                    <p class="text-2xl font-bold text-gray-800">18</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-center">
            <div class="flex space-x-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-3">
                    {{-- Lọc loại --}}
                    <select name="role"
                        class="bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả loại</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Thành viên</option>
                        <option value="employees" {{ request('role') == 'employees' ? 'selected' : '' }}>Nhân viên</option>
                    </select>
                    {{-- Tìm kiếm --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm theo email hoặc số điện thoại"
                        class="bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    {{-- Lọc trạng thái --}}
                    <select name="status"
                        class="bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động
                        </option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Lọc
                    </button>
                    <a href="{{ route('admin.users.index') }}" type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-redo"></i>
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thành viên
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thông tin liên hệ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quyền hạn
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lượt chơi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($listUser as $user)
                        <tr class="table-row hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user['name'] }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $user['id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user['email'] }}</div>
                                <div class="text-sm text-gray-500">{{ $user['phone'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user['role'] == 'admin')
                                    <span
                                        class="px-2 py-1 text-xs bg-purple-100 text-purple-800 border border-purple-200 rounded-full font-medium">
                                        <i class="fas fa-crown mr-1"></i>Admin
                                    </span>
                                @elseif($user['role'] == 'member')
                                    <span
                                        class="px-2 py-1 text-xs bg-blue-100 text-blue-800 border border-blue-200 rounded-full font-medium">
                                        <i class="fas fa-star mr-1"></i>Khách
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs bg-gray-100 text-gray-800 border border-gray-200 rounded-full font-medium">
                                        <i class="fas fa-user mr-1"></i>Nhân viên
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button"
                                    onclick="toggleUserStatus({{ $user['id'] }}, '{{ $user['status'] }}')"
                                    class="px-3 py-1 text-xs rounded-full transition font-medium
                                           @if ($user['status'] == 'active') bg-green-100 text-green-800 border border-green-200 hover:bg-green-200
                                           @else
                                               bg-red-100 text-red-800 border border-red-200 hover:bg-red-200 @endif">
                                    @if ($user['status'] == 'active')
                                        <i class="fas fa-check-circle mr-1"></i>Đang hoạt động
                                    @else
                                        <i class="fas fa-times-circle mr-1"></i>Ngừng hoạt động
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user['total_visits'] }} lượt</div>
                                <div class="text-xs text-gray-500">Tham gia: {{ $user['join_date'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="text-blue-600 hover:text-blue-800 transition" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <button class="text-green-600 hover:text-green-800 transition" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa thành viên này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Hiển thị {{ $listUser->firstItem() ?? 0 }}-{{ $listUser->lastItem() ?? 0 }} của
                    {{ $listUser->total() }} thành viên
                </div>
                <div class="flex space-x-2">
                    {{ $listUser->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

            if (confirm('Bạn có chắc muốn thay đổi trạng thái người dùng?')) {
                fetch(`/admin/users/${userId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra!');
                    });
            }
        }
    </script>
@endsection
