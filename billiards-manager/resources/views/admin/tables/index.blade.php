@extends('admin.layouts.app')

@section('title', 'Quản lý bàn bi-a - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý bàn bi-a</h1>
            <p class="text-gray-600">Theo dõi trạng thái bàn và thời gian combo theo thời gian thực</p>
        </div>
        <div class="flex flex-col space-y-2">
            <a href="{{ route('admin.tables.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm bàn mới
            </a>
            <a href="{{ route('admin.tables.trashed') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition flex items-center justify-center">
                <i class="fas fa-trash-restore mr-1"></i> Bàn đã xóa
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Bàn trống</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->where('status', 'available')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang sử dụng</p>
                    <p class="text-xl font-bold text-gray-800">
                        {{ $tables->whereIn('status', ['occupied', 'quick'])->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Bảo trì</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->where('status', 'maintenance')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng số bàn</p>
                    <p class="text-xl font-bold text-gray-800">{{ $tables->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-table text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Cảnh báo combo --}}
    @php
        $expiredTables = collect();
        $criticalTables = collect();
        $warningTables = collect();

        foreach ($tables as $table) {
            $info = $table->time_info ?? [];
            $isCombo = ($info['mode'] ?? '') === 'combo';

            if ($isCombo && in_array($table->status, ['occupied', 'paused']) && isset($info['remaining_minutes'])) {
                $remain = $info['remaining_minutes'];

                if ($remain <= 0) {
                    $expiredTables->push(['table' => $table, 'over' => abs($remain)]);
                } elseif ($remain <= 5) {
                    $criticalTables->push(['table' => $table, 'min' => $remain]);
                } elseif ($remain <= 10) {
                    $warningTables->push(['table' => $table, 'min' => $remain]);
                }
            }
        }
    @endphp

    @if ($expiredTables->count() > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 relative group">
            <div class="flex items-center cursor-help">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <div>
                    <p class="font-bold text-red-800">COMBO ĐÃ HẾT GIỜ</p>
                    <p class="text-sm text-red-600">Có {{ $expiredTables->count() }} bàn cần xử lý ngay</p>
                </div>
            </div>

            <!-- Hover Details -->
            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg p-4 mt-2 z-50 w-80">
                <div class="font-bold text-red-700 mb-2">Chi tiết bàn hết giờ combo:</div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach ($expiredTables as $item)
                        <div class="border-b pb-2 last:border-b-0">
                            <div class="font-medium">{{ $item['table']->table_name }} (#{{ $item['table']->table_number }})
                            </div>
                            <div class="text-sm text-gray-600">Đã quá: <span
                                    class="font-bold text-red-600">{{ $item['over'] }} phút</span></div>
                            <a href="{{ route('admin.tables.detail', $item['table']->id) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs inline-block mt-1">
                                Xử lý ngay →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($criticalTables->count() > 0)
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-4 relative group">
            <div class="flex items-center cursor-help">
                <i class="fas fa-clock text-orange-500 mr-3"></i>
                <div>
                    <p class="font-bold text-orange-800">COMBO SẮP HẾT (≤5 phút)</p>
                    <p class="text-sm text-orange-600">Có {{ $criticalTables->count() }} bàn cần theo dõi</p>
                </div>
            </div>

            <!-- Hover Details -->
            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg p-4 mt-2 z-50 w-80">
                <div class="font-bold text-orange-700 mb-2">Chi tiết bàn sắp hết combo:</div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach ($criticalTables as $item)
                        <div class="border-b pb-2 last:border-b-0">
                            <div class="font-medium">{{ $item['table']->table_name }}
                                (#{{ $item['table']->table_number }})
                            </div>
                            <div class="text-sm text-gray-600">Còn lại: <span
                                    class="font-bold text-orange-600">{{ $item['min'] }} phút</span></div>
                            <a href="{{ route('admin.tables.detail', $item['table']->id) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs inline-block mt-1">
                                Xem chi tiết →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($warningTables->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 relative group">
            <div class="flex items-center cursor-help">
                <i class="fas fa-hourglass-half text-yellow-500 mr-3"></i>
                <div>
                    <p class="font-bold text-yellow-800">COMBO SẮP HẾT (≤10 phút)</p>
                    <p class="text-sm text-yellow-600">Có {{ $warningTables->count() }} bàn cần lưu ý</p>
                </div>
            </div>

            <!-- Hover Details -->
            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg p-4 mt-2 z-50 w-80">
                <div class="font-bold text-yellow-700 mb-2">Chi tiết bàn cần lưu ý:</div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach ($warningTables as $item)
                        <div class="border-b pb-2 last:border-b-0">
                            <div class="font-medium">{{ $item['table']->table_name }}
                                (#{{ $item['table']->table_number }})
                            </div>
                            <div class="text-sm text-gray-600">Còn lại: <span
                                    class="font-bold text-yellow-600">{{ $item['min'] }} phút</span></div>
                            <a href="{{ route('admin.tables.detail', $item['table']->id) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs inline-block mt-1">
                                Xem chi tiết →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.tables.index') }}" class="mb-6 bg-white p-4 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc mã bàn..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statuses as $k => $v)
                    <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>
                        {{ $v }}</option>
                @endforeach
            </select>

            <select name="table_rate_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả loại bàn</option>
                @foreach ($tableRates as $rate)
                    <option value="{{ $rate->id }}" {{ request('table_rate_id') == $rate->id ? 'selected' : '' }}>
                        {{ $rate->name }}
                    </option>
                @endforeach
            </select>

            <select name="capacity" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả sức chứa</option>
                <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 người</option>
                <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 người</option>
                <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 người</option>
                <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+ người</option>
            </select>
        </div>
        <div class="mt-3 flex space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Lọc
            </button>
            <a href="{{ route('admin.tables.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Reset
            </a>
        </div>
    </form>

    <!-- Tables List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bàn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sức chứa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại bàn
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá/giờ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Combo
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành
                            động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tables as $table)
                        @php
                            $info = $table->time_info ?? [];
                            $isCombo = ($info['mode'] ?? '') === 'combo';
                            $hasTime = isset($info['remaining_minutes']);
                            $remain = $hasTime ? $info['remaining_minutes'] : null;

                            $isExpired = false;
                            $isCritical = false;
                            $isWarning = false;

                            if ($isCombo && in_array($table->status, ['occupied', 'paused']) && $hasTime) {
                                if ($remain <= 0) {
                                    $isExpired = true;
                                } elseif ($remain <= 5) {
                                    $isCritical = true;
                                } elseif ($remain <= 10) {
                                    $isWarning = true;
                                }
                            }

                            $isProductsOnly = $isCombo && !$hasTime;
                        @endphp
                        {{-- Clickable row với hover effect --}}
                        <tr class="hover:bg-gray-50 transition cursor-pointer 
                            @if ($isExpired) bg-red-50 hover:bg-red-100
                            @elseif($isCritical) bg-orange-50 hover:bg-orange-100
                            @elseif($isWarning) bg-yellow-50 hover:bg-yellow-100 @endif"
                            onclick="window.location.href='{{ route('admin.tables.detail', $table->id) }}'">

                            {{-- Tên bàn với icon và link --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="font-medium text-gray-900 flex items-center">
                                        {{ $table->table_name }}
                                        @if ($isExpired || $isCritical || $isWarning)
                                            <i class="fas fa-external-link-alt text-xs ml-1 opacity-70"></i>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">#{{ $table->table_number }}</div>
                                    @if ($table->currentBill?->user)
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $table->currentBill->user->name }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Sức chứa --}}
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    {{ $table->capacity }} người
                                </span>
                            </td>

                            {{-- Loại bàn --}}
                            <td class="px-6 py-4">
                                @if ($table->tableRate)
                                    <span class="text-sm">{{ $table->tableRate->name }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                        {{ $table->status === 'available'
                                            ? 'bg-green-100 text-green-800'
                                            : ($table->status === 'occupied' || $table->status === 'quick'
                                                ? 'bg-red-100 text-red-800'
                                                : ($table->status === 'paused'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $statuses[$table->status] ?? $table->status }}
                                    </span>

                                    @if ($table->currentBill?->status === 'quick')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                            BÀN LẺ
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Giá --}}
                            <td class="px-6 py-4 text-sm font-medium">{{ number_format($table->getHourlyRate()) }}
                                đ/giờ</td>

                            {{-- Combo info với clickable badge --}}
                            <td class="px-6 py-4">
                                @if ($isCombo && $hasTime && $remain !== null)
                                    <a href="{{ route('admin.tables.detail', $table->id) }}" class="inline-block">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full cursor-pointer
                                            {{ $isExpired
                                                ? 'bg-red-100 text-red-800 hover:bg-red-200'
                                                : ($isCritical
                                                    ? 'bg-red-100 text-red-800 hover:bg-red-200'
                                                    : ($isWarning
                                                        ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                                        : 'bg-green-100 text-green-800 hover:bg-green-200')) }}">
                                            @if ($isExpired)
                                                <i class="fas fa-exclamation-circle mr-1"></i>Hết giờ
                                                (-{{ abs($remain) }}')
                                            @else
                                                <i class="fas fa-clock mr-1"></i>Còn {{ $remain }}'
                                            @endif
                                        </span>
                                    </a>
                                @elseif($isProductsOnly)
                                    <a href="{{ route('admin.tables.detail', $table->id) }}" class="inline-block">
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full cursor-pointer hover:bg-blue-200">
                                            <i class="fas fa-shopping-basket mr-1"></i>Combo sản phẩm
                                        </span>
                                    </a>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Action buttons (không clickable row) --}}
                            <td class="px-6 py-4 text-right space-x-3" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.tables.detail', $table->id) }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if (!in_array($table->status, ['occupied', 'quick']))
                                    <a href="{{ route('admin.tables.edit', $table->id) }}"
                                        class="text-yellow-600 hover:text-yellow-800 hover:underline" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $table->id }}"
                                        action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $table->id }})"
                                            class="text-red-600 hover:text-red-800 hover:underline" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                @if ($isExpired)
                                    <button type="button" onclick="handleExpiredCombo({{ $table->id }})"
                                        class="text-red-600 hover:text-red-800 hover:underline"
                                        title="Xử lý combo hết giờ">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <p class="mb-4">Không tìm thấy bàn phù hợp.</p>
                                <a href="{{ route('admin.tables.create') }}"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    Thêm bàn đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tables->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $tables->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Toast thông báo --}}
    @if (session('success'))
        <div class="fixed bottom-6 right-6 z-50" id="toast-success">
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-check-circle text-xl mr-4"></i>
                <div>
                    <p class="font-bold">Thành công!</p>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="ml-8 text-white hover:text-green-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed bottom-6 right-6 z-50" id="toast-error">
            <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-4"></i>
                <div>
                    <p class="font-bold">Lỗi!</p>
                    <p>{{ session('error') }}</p>
                </div>
                <button class="ml-8 text-white hover:text-red-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Làm cho cả hàng có thể click được
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('tbody tr[onclick]');

            tableRows.forEach(row => {
                // Thêm hiệu ứng hover
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                    this.style.boxShadow =
                        '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });

                // Thêm indicator khi hover
                row.style.cursor = 'pointer';
                row.style.transition = 'all 0.2s ease';
            });
        });

        function confirmDelete(tableId) {
            event.stopPropagation(); // Ngăn click vào hàng
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa bàn này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + tableId).submit();
                }
            });
        }

        function handleExpiredCombo(tableId) {
            event.stopPropagation(); // Ngăn click vào hàng
            Swal.fire({
                title: 'XỬ LÝ COMBO HẾT GIỜ',
                html: `Bàn #${tableId} đã hết giờ combo.<br>Bạn muốn xử lý thế nào?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Gia hạn combo',
                cancelButtonText: 'Kết thúc combo',
                showDenyButton: true,
                denyButtonText: 'Chuyển sang tính giờ',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Thành công!', 'Đã gia hạn combo cho bàn.', 'success');
                } else if (result.isDenied) {
                    Swal.fire('Thành công!', 'Bàn đã chuyển sang chế độ tính giờ.', 'info');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Thành công!', 'Combo đã được kết thúc.', 'info');
                }
            });
        }

        // Ẩn toast sau 5 giây
        setTimeout(() => {
            document.querySelectorAll('#toast-success, #toast-error').forEach(el => {
                if (el) {
                    el.style.opacity = '0';
                    setTimeout(() => el?.remove(), 300);
                }
            });
        }, 5000);
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

    /* Hiệu ứng cho hàng bàn */
    tbody tr {
        position: relative;
    }

    tbody tr:hover {
        background-color: #f9fafb !important;
    }

    tbody tr:hover::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #3b82f6;
    }

    /* Đảm bảo các nút action vẫn có thể click riêng */
    td:last-child {
        position: relative;
        z-index: 10;
    }
</style>
 