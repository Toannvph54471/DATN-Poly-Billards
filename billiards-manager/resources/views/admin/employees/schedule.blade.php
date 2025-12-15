@extends('admin.layouts.app')

@section('title', 'Lịch Làm Việc Của Tôi - Poly Billiards')

@section('styles')
    <style>
        .shift-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .shift-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .shift-status {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1e40af;
            border-left-color: #3b82f6;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
            border-left-color: #10b981;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
            border-left-color: #ef4444;
        }

        .status-absent {
            background: #fef3c7;
            color: #92400e;
            border-left-color: #f59e0b;
        }

        .today-shift {
            border: 2px solid #3b82f6;
            background: #eff6ff;
        }

        .shift-time {
            font-weight: 600;
            color: #1f2937;
        }

        .upcoming-shift-item {
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .upcoming-shift-item:hover {
            background: #f9fafb;
        }

        .pagination-link {
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .pagination-link:hover {
            background: #3b82f6;
            color: white;
        }

        .pagination-active {
            background: #3b82f6;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                    Lịch Làm Việc Của Tôi
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $employee->full_name }} • {{ $employee->employee_code }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.my-profile') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <i class="fas fa-user mr-2"></i>Thông tin cá nhân
                </a>
                <a href="{{ route('admin.pos.dashboard') }}"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                    <i class="fas fa-tachometer-alt mr-2"></i>POS Dashboard
                </a>
            </div>
        </div>

        <!-- Thông tin tháng -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">
                        Tháng {{ $month }}/{{ $year }}
                    </h2>
                    <p class="text-gray-600">
                        {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.schedule', ['month' => $month - 1, 'year' => $year]) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <a href="{{ route('admin.schedule') }}"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                        Tháng này
                    </a>

                    <a href="{{ route('admin.schedule', ['month' => $month + 1, 'year' => $year]) }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Tổng ca</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fas fa-calendar text-blue-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Đã hoàn thành</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['completed'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Sắp tới</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['upcoming'] }}</p>
                    </div>
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Hôm nay</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['today'] }}</p>
                    </div>
                    <i class="fas fa-calendar-day text-red-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ca làm HÔM NAY -->
        @if ($todayShifts->count() > 0)
            <div class="bg-white rounded-xl shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-sun text-yellow-500 mr-2"></i>
                        Ca Làm Hôm Nay
                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">
                            {{ $todayShifts->count() }} ca
                        </span>
                    </h2>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @foreach ($todayShifts as $shift)
                            <div class="shift-card bg-white border border-gray-200 rounded-lg p-4 today-shift">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="shift-status">
                                                @php
                                                    $rtStatus = $shift->real_time_status;
                                                    $colorClass = match($rtStatus) {
                                                        'Đang trong ca làm' => 'text-green-800 bg-green-100',
                                                        'Chưa checkin' => 'text-gray-800 bg-gray-100',
                                                        'Đã checkin' => 'text-blue-800 bg-blue-100',
                                                        'Vắng mặt' => 'text-red-800 bg-red-100',
                                                        'Đi muộn' => 'text-orange-800 bg-orange-100',
                                                        'Tan ca nhưng quên checkout' => 'text-yellow-800 bg-yellow-100',
                                                        default => 'text-gray-800 bg-gray-100'
                                                    };
                                                @endphp
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                                    {{ $rtStatus }}
                                                </span>
                                            </span>
                                        </div>

                                        <h3 class="text-lg font-bold text-gray-800 mb-1">
                                            {{ $shift->shift->name ?? 'Ca không xác định' }}
                                        </h3>

                                        @if ($shift->shift)
                                            <div class="flex items-center text-gray-600">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span class="shift-time">
                                                    {{ \Carbon\Carbon::parse($shift->shift->start_time)->format('H:i') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($shift->shift->end_time)->format('H:i') }}
                                                </span>
                                                <span class="mx-2">•</span>
                                                <span>Tổng: {{ $shift->shift->duration ?? 'N/A' }} giờ</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-center py-8">
                    <i class="fas fa-coffee text-4xl text-gray-300 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Không có ca làm hôm nay</h3>
                    <p class="text-gray-500">Hôm nay bạn được nghỉ!</p>
                </div>
            </div>
        @endif

        <!-- Ca làm SẮP TỚI (Hiển thị đơn giản) -->
        @if ($upcomingShifts->count() > 0)
            <div class="bg-white rounded-xl shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-plus text-green-500 mr-2"></i>
                        5 Ca Làm Sắp Tới
                        <span class="ml-2 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                            {{ $upcomingShifts->count() }} ca
                        </span>
                    </h2>
                </div>

                <div class="p-6">
                    <div class="space-y-2">
                        @foreach ($upcomingShifts as $shift)
                            <div class="upcoming-shift-item border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($shift->shift_date)->format('d') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($shift->shift_date)->format('m/Y') }}
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">
                                                {{ $shift->shift->name ?? 'N/A' }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($shift->shift_date)->locale('vi')->dayName }} •
                                                {{ \Carbon\Carbon::parse($shift->shift->start_time ?? now())->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($shift->shift->end_time ?? now())->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="shift-status">
                                        @php
                                            $rtStatus = $shift->real_time_status;
                                            $colorClass = match($rtStatus) {
                                                'Đang trong ca làm' => 'text-green-800 bg-green-100',
                                                'Chưa checkin' => 'text-gray-800 bg-gray-100',
                                                'Đã checkout' => 'text-blue-800 bg-blue-100',
                                                'Vắng mặt' => 'text-red-800 bg-red-100',
                                                'Đi muộn' => 'text-orange-800 bg-orange-100',
                                                'Tan ca nhưng quên checkout' => 'text-yellow-800 bg-yellow-100',
                                                default => 'text-gray-800 bg-gray-100'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                            {{ $rtStatus }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($upcomingShifts->count() >= 5)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">... và
                                {{ $stats['total'] - $stats['completed'] - $upcomingShifts->count() }} ca khác trong tháng
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TẤT CẢ CA LÀM TRONG THÁNG (Có phân trang) -->
        <div class="bg-white rounded-xl shadow">
            <div class="p-6 border-b">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list-alt text-purple-500 mr-2"></i>
                        Tất Cả Ca Làm Trong Tháng
                    </h2>
                    <div class="text-sm text-gray-600">
                        Hiển thị {{ $shifts->firstItem() ?? 0 }}-{{ $shifts->lastItem() ?? 0 }} / {{ $shifts->total() }}
                        ca
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ca làm
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Giờ làm
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ghi chú
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($shifts as $shift)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($shift->shift_date)->format('d/m') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($shift->shift_date)->locale('vi')->dayName }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $shift->shift->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        @if ($shift->shift)
                                            {{ \Carbon\Carbon::parse($shift->shift->start_time)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($shift->shift->end_time)->format('H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $rtStatus = $shift->real_time_status;
                                        $colorClass = match($rtStatus) {
                                            'Đang trong ca làm' => 'text-green-800 bg-green-100',
                                            'Chưa checkin' => 'text-gray-800 bg-gray-100',
                                            'Đã checkout' => 'text-blue-800 bg-blue-100',
                                            'Vắng mặt' => 'text-red-800 bg-red-100',
                                            'Đi muộn' => 'text-orange-800 bg-orange-100',
                                            'Tan ca nhưng quên checkout' => 'text-yellow-800 bg-yellow-100',
                                            default => 'text-gray-800 bg-gray-100'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                        {{ $rtStatus }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 max-w-[200px] truncate"
                                        title="{{ $shift->notes ?? '' }}">
                                        {{ $shift->notes ? Str::limit($shift->notes, 30) : '—' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-3xl mb-3 opacity-50"></i>
                                    <p class="text-lg">Không có ca làm nào trong tháng này</p>
                                    <p class="text-sm text-gray-400 mt-1">Hãy liên hệ quản lý để được phân ca</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            @if ($shifts->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="text-sm text-gray-700">
                            Hiển thị <span class="font-medium">{{ $shifts->firstItem() }}</span>
                            đến <span class="font-medium">{{ $shifts->lastItem() }}</span>
                            của <span class="font-medium">{{ $shifts->total() }}</span> ca làm
                        </div>

                        <div class="flex items-center space-x-1">
                            <!-- Previous Page Link -->
                            @if ($shifts->onFirstPage())
                                <span class="px-3 py-1 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $shifts->previousPageUrl() }}" class="pagination-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            <!-- Pagination Elements -->
                            @foreach ($shifts->getUrlRange(1, $shifts->lastPage()) as $page => $url)
                                @if ($page == $shifts->currentPage())
                                    <span class="pagination-link pagination-active">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="pagination-link">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Next Page Link -->
                            @if ($shifts->hasMorePages())
                                <a href="{{ $shifts->nextPageUrl() }}" class="pagination-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="px-3 py-1 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Chú thích -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Chú Thích
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                    <span class="text-sm">Scheduled - Đã lên lịch</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm">Completed - Đã hoàn thành</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-sm">Absent - Vắng mặt</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm">Cancelled - Đã hủy</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Hiệu ứng cho card
        document.addEventListener('DOMContentLoaded', function() {
            const shiftCards = document.querySelectorAll('.shift-card');

            shiftCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.08)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '';
                });
            });

            // Hiệu ứng hover cho upcoming shifts
            const upcomingItems = document.querySelectorAll('.upcoming-shift-item');
            upcomingItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f9fafb';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
@endsection
