@extends('admin.layouts.app')

@section('title', 'Bàn đã xóa - Billiards Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bàn đã xóa</h1>
            <p class="text-gray-600">Danh sách các bàn billiards đã bị xóa trong hệ thống</p>
        </div>
        <div>
            <a href="{{ route('admin.tables.index') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại danh sách bàn
            </a>
        </div>
    </div>

    <!-- Tables List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Số bàn
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Tên bàn
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày xóa
                        </th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Hành động
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tables as $table)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 text-sm text-gray-800 font-semibold">{{ $table->id }}</td>
                            <td class="py-4 px-6 text-sm text-gray-800 font-medium">{{ $table->table_number }}</td>
                            <td class="py-4 px-6 text-sm text-gray-800">{{ $table->table_name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600 capitalize">{{ $table->type ?? 'N/A' }}</td>
                            <td class="py-4 px-6">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-trash mr-1" style="font-size:6px;"></i> Đã xóa
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $table->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    <form action="{{ route('admin.tables.restore', $table->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Bạn muốn khôi phục không')"
                                            class="bg-green-600 text-white rounded-lg px-3 py-1 text-sm font-medium hover:bg-green-700 transition flex items-center">
                                            <i class="fas fa-undo mr-1"></i>
                                            Khôi phục
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.tables.forceDelete', $table->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmForceDelete({{ $table->id }})"
                                            class="bg-red-600 text-white rounded-lg px-3 py-1 text-sm font-medium hover:bg-red-700 transition flex items-center">
                                            <i class="fas fa-trash-alt mr-1"></i>
                                            Xóa vĩnh viễn
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-trash-restore text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có bàn nào đã xóa</h3>
                                    <p class="text-gray-500">Tất cả các bàn đang được hiển thị trong danh sách chính.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{-- @if ($tables->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $tables->links() }}
            </div>
        @endif --}}
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
</style>
