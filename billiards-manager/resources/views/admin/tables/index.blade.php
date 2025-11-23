@extends('admin.layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Quản lý bàn</h1>
        <p class="text-gray-600">Quản lý bàn bi-a và trạng thái</p>
    </div>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bàn trống</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $tables->where('status', 'available')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-users text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Đang sử dụng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $tables->where('status', 'occupied')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-tools text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bảo trì</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $tables->where('status', 'maintenance')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-table text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tổng số bàn</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $tables->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CẢNH BÁO COMBO SẮP HẾT - HIỂN THỊ TRÊN CÙNG -->
    @php
        $criticalComboTables = collect();
        $warningComboTables = collect();

        foreach ($tables as $table) {
            $timeInfo = $table->time_info;
            if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && isset($timeInfo['remaining_minutes'])) {
                $remainingMinutes = $timeInfo['remaining_minutes'];

                if ($remainingMinutes <= 5 && $remainingMinutes > 0) {
                    $criticalComboTables->push([
                        'table' => $table,
                        'remaining_minutes' => $remainingMinutes,
                        'time_info' => $timeInfo,
                    ]);
                } elseif ($remainingMinutes <= 10 && $remainingMinutes > 5) {
                    $warningComboTables->push([
                        'table' => $table,
                        'remaining_minutes' => $remainingMinutes,
                        'time_info' => $timeInfo,
                    ]);
                }
            }
        }
    @endphp

    @if ($criticalComboTables->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 animate-pulse">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">CẢNH BÁO KHẨN CẤP: COMBO SẮP HẾT!</h3>
                        <p class="text-red-600">
                            Có <strong>{{ $criticalComboTables->count() }} bàn</strong> sắp hết thời gian combo
                        </p>
                    </div>
                </div>
                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                    <i class="fas fa-clock mr-1"></i>
                    CẦN XỬ LÝ NGAY
                </span>
            </div>

            <!-- Hiển thị danh sách bàn cần xử lý -->
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($criticalComboTables as $comboTable)
                    <div class="bg-red-100 border border-red-300 rounded p-2 flex justify-between items-center">
                        <span class="font-semibold text-red-800">
                            {{ $comboTable['table']->table_number }} - {{ $comboTable['table']->table_name }}
                        </span>
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                            {{ $comboTable['remaining_minutes'] }} phút
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($warningComboTables->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-amber-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-amber-800">CẢNH BÁO: COMBO SẮP HẾT</h3>
                        <p class="text-amber-600">
                            Có <strong>{{ $warningComboTables->count() }} bàn</strong> sắp hết thời gian combo
                        </p>
                    </div>
                </div>
                <span class="bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                    <i class="fas fa-clock mr-1"></i>
                    CHUẨN BỊ XỬ LÝ
                </span>
            </div>

            <!-- Hiển thị danh sách bàn cần chú ý -->
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($warningComboTables as $comboTable)
                    <div class="bg-amber-100 border border-amber-300 rounded p-2 flex justify-between items-center">
                        <span class="font-semibold text-amber-800">
                            {{ $comboTable['table']->table_number }} - {{ $comboTable['table']->table_name }}
                        </span>
                        <span class="bg-amber-500 text-white px-2 py-1 rounded text-sm font-bold">
                            {{ $comboTable['remaining_minutes'] }} phút
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters và Search -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.tables.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Tìm kiếm bàn...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại bàn</label>
                <select name="table_rate_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tất cả</option>
                    @foreach ($tableRates as $rate)
                        <option value="{{ $rate->id }}" {{ request('table_rate_id') == $rate->id ? 'selected' : '' }}>
                            {{ $rate->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Lọc
                </button>
                <a href="{{ route('admin.tables.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    <i class="fas fa-refresh mr-2"></i>Đặt lại
                </a>
                <a href="{{ route('admin.tables.create') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 ml-auto">
                    <i class="fas fa-plus mr-2"></i>Thêm bàn mới
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách bàn -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if ($tables->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông
                                tin bàn</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sức chứa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Giá theo giờ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời gian còn lại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($tables as $table)
                            @php
                                $timeInfo = $table->time_info;
                                $hasComboWarning = false;
                                $hasCriticalWarning = false;
                                $remainingMinutes = null;

                                if (
                                    isset($timeInfo['mode']) &&
                                    $timeInfo['mode'] === 'combo' &&
                                    isset($timeInfo['remaining_minutes'])
                                ) {
                                    $remainingMinutes = $timeInfo['remaining_minutes'];
                                    if ($remainingMinutes <= 10) {
                                        $hasComboWarning = true;
                                        if ($remainingMinutes <= 5) {
                                            $hasCriticalWarning = true;
                                        }
                                    }
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 group relative cursor-pointer 
                                @if ($hasCriticalWarning) bg-red-50 border-l-4 border-l-red-500 animate-pulse @endif
                                @if ($hasComboWarning && !$hasCriticalWarning) bg-amber-50 border-l-4 border-l-amber-500 @endif"
                                onclick="window.location='{{ route('admin.tables.detail', $table->id) }}'">

                                <!-- Cột Thông tin bàn -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($hasCriticalWarning)
                                            <div class="mr-3">
                                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"
                                                    title="Combo sắp hết!"></i>
                                            </div>
                                        @elseif($hasComboWarning)
                                            <div class="mr-3">
                                                <i class="fas fa-clock text-amber-500 text-lg" title="Combo sắp hết"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                #{{ $table->table_number }}
                                                @if ($table->currentBill && $table->currentBill->status === 'quick')
                                                    <span
                                                        class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">
                                                        <i class="fas fa-bolt mr-1"></i>Bàn lẻ
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $table->table_name }}</div>
                                            @if ($table->currentBill)
                                                <div class="text-xs text-red-500 mt-1 font-semibold">
                                                    <i class="fas fa-clock mr-1"></i>Đang sử dụng
                                                    @if ($table->currentBill->user)
                                                        - {{ $table->currentBill->user->name }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>{{ $table->capacity }} người
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $rate = $table->tableRate;

                                        // Màu sắc theo loại bàn
                                        $typeColors = [
                                            'Regular' => 'bg-gray-100 text-gray-800',
                                            'VIP' => 'bg-purple-100 text-purple-800',
                                            'Competition' => 'bg-yellow-100 text-yellow-800',
                                        ];

                                        // Mặc định
                                        $typeKey = 'Regular';
                                        $rateLabel = 'Chưa phân loại';
                                        $rateValue = '';

                                        if ($rate) {
                                            // Xác định loại bàn theo code
                                            if (str_starts_with($rate->code, 'VIP')) {
                                                $typeKey = 'VIP';
                                            } elseif (str_starts_with($rate->code, 'COMP')) {
                                                $typeKey = 'Competition';
                                            } elseif (str_starts_with($rate->code, 'REG')) {
                                                $typeKey = 'Regular';
                                            }

                                            $rateLabel = $rate->name;
                                            $rateValue = number_format($rate->hourly_rate, 0, ',', '.') . ' đ/giờ';
                                        } else {
                                            $rateValue = number_format(50000, 0, ',', '.') . ' đ/giờ';
                                        }
                                    @endphp

                                    <div class="flex flex-col">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$typeKey] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $rateLabel }}
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1">{{ $rateValue }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800',
                                            'occupied' => 'bg-red-100 text-red-800',
                                            'paused' => 'bg-yellow-100 text-yellow-800',
                                            'maintenance' => 'bg-gray-100 text-gray-800',
                                            'reserved' => 'bg-blue-100 text-blue-800',
                                            'quick' => 'bg-orange-100 text-orange-800',
                                        ];

                                        $statusLabels = [
                                            'available' => 'Trống',
                                            'occupied' => 'Đang sử dụng',
                                            'paused' => 'Tạm dừng',
                                            'maintenance' => 'Bảo trì',
                                            'reserved' => 'Đã đặt',
                                            'quick' => 'Bàn lẻ',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$table->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if ($table->status === 'occupied' && $table->currentBill)
                                            @if ($table->currentBill->status === 'Open')
                                                <i class="fas fa-play-circle mr-1"></i>
                                            @elseif($table->currentBill->status === 'quick')
                                                <i class="fas fa-bolt mr-1"></i>
                                            @endif
                                        @endif
                                        {{ $statusLabels[$table->status] ?? $table->status }}
                                    </span>

                                    <!-- Hiển thị trạng thái combo -->
                                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                                        <div class="mt-1">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-gift mr-1"></i>Combo Time
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $hourlyRate = $table->tableRate ? $table->tableRate->hourly_rate : 50000;
                                    @endphp
                                    {{ number_format($hourlyRate, 0, ',', '.') }} đ/giờ
                                </td>

                                <!-- Cột Thời gian còn lại -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($hasComboWarning && $remainingMinutes !== null)
                                        <div class="flex items-center">
                                            @if ($hasCriticalWarning)
                                                <span class="text-red-600 font-bold text-lg mr-2">
                                                    {{ $remainingMinutes }}'
                                                </span>
                                                <span class="text-red-500 text-xs">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Sắp hết!
                                                </span>
                                            @else
                                                <span class="text-amber-600 font-semibold mr-2">
                                                    {{ $remainingMinutes }}'
                                                </span>
                                                <span class="text-amber-500 text-xs">
                                                    <i class="fas fa-clock mr-1"></i>Sắp hết
                                                </span>
                                            @endif
                                        </div>
                                    @elseif(isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && isset($timeInfo['remaining_minutes']))
                                        <span class="text-green-600 font-medium">
                                            {{ $timeInfo['remaining_minutes'] }}' còn lại
                                        </span>
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </td>

                                <!-- Cột Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2" onclick="event.stopPropagation()">
                                        <!-- Xem chi tiết -->
                                        <a href="{{ route('admin.tables.detail', $table->id) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Chỉnh sửa - Chỉ cho bàn không ở trạng thái occupied -->
                                        @if ($table->status !== 'occupied')
                                            <a href="{{ route('admin.tables.edit', $table->id) }}"
                                                class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed"
                                                title="Không thể chỉnh sửa bàn đang sử dụng">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        @endif

                                        <!-- Xóa - Chỉ cho bàn không ở trạng thái occupied -->
                                        @if ($table->status !== 'occupied')
                                            <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa bàn này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    onclick="event.stopPropagation()" title="Xóa bàn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed"
                                                title="Không thể xóa bàn đang sử dụng">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $tables->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-table fa-3x text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Không tìm thấy bàn nào</h3>
                <p class="text-gray-500 mt-2">Hãy bắt đầu bằng cách tạo bàn mới.</p>
                <a href="{{ route('admin.tables.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4">
                    <i class="fas fa-plus mr-2"></i>Thêm bàn mới
                </a>
            </div>
        @endif
    </div>

    <!-- Nút thêm bàn mới ở dưới cùng -->
    <div class="mt-6 flex justify-end">
        <a href="{{ route('admin.tables.create') }}"
            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fas fa-plus mr-2"></i>Thêm bàn mới
        </a>
        <a href="{{ route('admin.tables.trashed') }}"
            class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-4">
            <i class="fas fa-trash-restore mr-2"></i>Bàn đã xóa
        </a>
    </div>

    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" id="success-message">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('success-message').remove();
            }, 3000);
        </script>
    @endif

    @if (session('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg" id="error-message">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('error-message').remove();
            }, 5000);
        </script>
    @endif

    @push('styles')
        <style>
            /* Hiệu ứng hover cho toàn bộ hàng */
            tr[onclick]:hover {
                background-color: #f3f4f6 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.2s ease-in-out;
            }

            /* Con trỏ chuột cho toàn bộ hàng */
            tr[onclick] {
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }

            /* Đảm bảo các link trong actions không bị ảnh hưởng */
            tr[onclick] .flex.space-x-2 a,
            tr[onclick] .flex.space-x-2 button,
            tr[onclick] .flex.space-x-2 form {
                position: relative;
                z-index: 10;
            }

            /* Style cho icon bị disabled */
            .cursor-not-allowed {
                opacity: 0.5;
            }

            /* Animation cho cảnh báo khẩn cấp */
            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.7;
                }
            }

            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Auto-refresh trang mỗi 30 giây để cập nhật trạng thái combo
            setInterval(() => {
                // Chỉ refresh nếu có bàn đang sử dụng combo sắp hết
                const hasCriticalCombo = document.querySelector('.bg-red-50.animate-pulse');
                const hasWarningCombo = document.querySelector('.bg-amber-50');

                if (hasCriticalCombo || hasWarningCombo) {
                    window.location.reload();
                }
            }, 30000); // 30 giây

            // Hoặc có thể refresh mỗi 60 giây nếu muốn
            setInterval(() => {
                window.location.reload();
            }, 60000); // 60 giây
        </script>
    @endpush
@endsection
