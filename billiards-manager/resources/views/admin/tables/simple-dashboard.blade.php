@extends('admin.layouts.app')

@section('title', 'Dashboard - F&B Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Tổng quan hệ thống</h1>
                <p class="text-sm text-gray-500">Cập nhật lúc {{ date('H:i, d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download mr-2"></i>Xuất báo cáo
                </button>
                <a href="{{ route('admin.tables.create') }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm bàn
                </a>
            </div>
        </div>

        <!-- Hiển thị lỗi nếu có -->
        @if (isset($error))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Lỗi tải dữ liệu</h3>
                        <p class="text-sm text-red-600 mt-1">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($stats)

            <!-- Grid bàn dạng ô vuông -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Danh sách bàn</h3>
                        <p class="text-sm text-gray-500 mt-1">Quản lý trạng thái các bàn</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Tổng: {{ $tables->count() }} bàn</span>
                    </div>
                </div>

                <!-- Grid 6 cột -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($tables as $table)
                        @php
                            // Xác định màu sắc dựa trên trạng thái
                            $bgColor = 'bg-white';
                            $borderColor = 'border-gray-300';
                            $textColor = 'text-gray-900';

                            switch ($table['status']) {
                                case 'available':
                                    $bgColor = 'bg-white';
                                    $borderColor = 'border-green-500';
                                    $statusText = 'Trống';
                                    $statusColor = 'text-green-600';
                                    break;
                                case 'occupied':
                                    $bgColor = 'bg-gray-900';
                                    $borderColor = 'border-gray-900';
                                    $textColor = 'text-white';
                                    $statusText = 'Đang dùng';
                                    $statusColor = 'text-yellow-400';
                                    break;
                                case 'quick':
                                    $bgColor = 'bg-gray-800';
                                    $borderColor = 'border-gray-800';
                                    $textColor = 'text-white';
                                    $statusText = 'Bàn lẻ';
                                    $statusColor = 'text-purple-400';
                                    break;
                                case 'reserved':
                                    $bgColor = 'bg-white';
                                    $borderColor = 'border-yellow-500';
                                    $statusText = 'Đã đặt';
                                    $statusColor = 'text-yellow-600';
                                    break;
                                case 'maintenance':
                                    $bgColor = 'bg-white';
                                    $borderColor = 'border-red-500';
                                    $statusText = 'Bảo trì';
                                    $statusColor = 'text-red-600';
                                    break;
                                default:
                                    $bgColor = 'bg-white';
                                    $borderColor = 'border-gray-300';
                                    $statusText = $table['status'];
                                    $statusColor = 'text-gray-600';
                            }
                        @endphp
                        <a href="{{ route('admin.tables.detail', $table['id']) }}"
                            class="block relative group no-underline">
                            <div class="relative group">

                                <div
                                    class="{{ $bgColor }} {{ $borderColor }} {{ $textColor }} border-2 rounded-lg p-4 h-32 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:scale-105 cursor-pointer">
                                    <!-- Header: Tên bàn và trạng thái -->
                                    <div class="flex justify-between items-start">

                                        <div>
                                            <h4 class="font-bold text-lg {{ $textColor }}">{{ $table['table_number'] }}
                                            </h4>
                                            <p class="text-xs {{ $textColor }} opacity-80">{{ $table['table_name'] }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block w-2 h-2 rounded-full {{ $statusColor }}"></span>
                                            <p class="text-xs {{ $statusColor }} font-medium mt-1">{{ $statusText }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Thời gian sử dụng -->
                                    <div class="text-center">
                                        @if (
                                            $table['current_bill'] &&
                                                $table['current_bill']['elapsed_time'] &&
                                                in_array($table['status'], ['occupied', 'quick']))
                                            <div class="{{ $textColor }}">
                                                <div class="text-2xl font-mono font-bold tracking-wider">
                                                    {{ $table['current_bill']['elapsed_time'] }}
                                                </div>
                                                <div class="text-xs opacity-80 mt-1">Thời gian sử dụng</div>
                                            </div>
                                        @else
                                            <div class="{{ $textColor }} opacity-70">
                                                <div class="text-lg font-mono">--:--</div>
                                                <div class="text-xs mt-1">Chưa sử dụng</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Footer: Sức chứa và giá -->
                                    <div class="flex justify-between items-center text-xs {{ $textColor }} opacity-80">
                                        <span>{{ $table['capacity'] }} người</span>
                                        <span>{{ number_format($table['hourly_rate'] / 1000, 0) }}k/h</span>
                                    </div>
                                </div>

                                <!-- Overlay actions -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex gap-2">

                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>

                @if ($tables->count() == 0)
                    <div class="text-center py-12">
                        <div class="text-gray-400">
                            <i class="fas fa-chair text-4xl mb-3"></i>
                            <p class="text-lg font-medium">Chưa có bàn nào</p>
                            <p class="text-sm mt-1">Hãy thêm bàn mới để bắt đầu</p>
                            <a href="{{ route('admin.tables.create') }}"
                                class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Thêm bàn mới
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Thống kê nhanh -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Phân bổ trạng thái -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Phân bổ trạng thái</h3>
                    <div class="space-y-3">
                        @php
                            $statusItems = [
                                ['color' => 'green', 'label' => 'Trống', 'value' => $stats['available']],
                                ['color' => 'gray', 'label' => 'Đang dùng', 'value' => $stats['occupied']],
                                ['color' => 'purple', 'label' => 'Bàn lẻ', 'value' => $stats['quick']],
                            ];
                        @endphp

                        @foreach ($statusItems as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-{{ $item['color'] }}-500"></div>
                                    <span class="text-sm text-gray-700">{{ $item['label'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $item['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Tỷ lệ sử dụng</span>
                            <span class="font-semibold text-gray-900">{{ $stats['occupancy_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                                style="width: {{ $stats['occupancy_rate'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Bàn đang hoạt động -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bàn đang hoạt động</h3>
                    <div class="space-y-3">
                        @foreach ($tables->whereIn('status', ['occupied', 'quick'])->take(5) as $table)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $table['table_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $table['table_number'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-mono text-gray-900">
                                        @if ($table['current_bill'] && $table['current_bill']['elapsed_time'])
                                            {{ $table['current_bill']['elapsed_time'] }}
                                        @else
                                            --:--
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($table['hourly_rate'] / 1000, 0) }}k/h
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        @if ($tables->whereIn('status', ['occupied', 'quick'])->count() == 0)
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-users text-2xl mb-2"></i>
                                <p class="text-sm">Không có bàn đang hoạt động</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Hiển thị khi không có dữ liệu -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Không thể tải dữ liệu</h3>
                <p class="text-gray-500 mb-6">Đã xảy ra lỗi khi tải dữ liệu dashboard. Vui lòng thử lại sau.</p>
                <button onclick="window.location.reload()"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Tải lại trang
                </button>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Có thể thêm các tính năng JavaScript tại đây
            console.log('Dashboard loaded successfully');
        });
    </script>
@endsection
