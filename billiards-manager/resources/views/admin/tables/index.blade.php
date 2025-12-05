@extends('admin.layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Quản lý bàn bi-a</h1>
        <p class="text-gray-600">Theo dõi trạng thái bàn và thời gian combo theo thời gian thực</p>
    </div>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bàn trống</p>
                    <p class="text-3xl font-bold text-green-600">{{ $tables->where('status', 'available')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-200"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Đang sử dụng</p>
                    <p class="text-3xl font-bold text-red-600">
                        {{ $tables->whereIn('status', ['occupied', 'quick'])->count() }}</p>
                </div>
                <i class="fas fa-users text-4xl text-red-200"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bảo trì</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $tables->where('status', 'maintenance')->count() }}</p>
                </div>
                <i class="fas fa-tools text-4xl text-yellow-200"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tổng số bàn</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $tables->total() }}</p>
                </div>
                <i class="fas fa-table text-4xl text-blue-200"></i>
            </div>
        </div>
    </div>

    {{-- PHÂN TÍCH VÀ CẢNH BÁO COMBO --}}
    @php
        $activeTables = collect();
        $expiredTables = collect();
        $criticalTables = collect();
        $warningTables = collect();
        $comboProductsOnly = collect();

        foreach ($tables as $table) {
            $info = $table->time_info ?? [];
            $isCombo = ($info['mode'] ?? '') === 'combo';
            
            // Chỉ xử lý cảnh báo nếu bàn đang sử dụng combo
            if ($isCombo && in_array($table->status, ['occupied', 'paused'])) {
                // Kiểm tra xem combo có bao gồm thời gian không (không phải chỉ sản phẩm)
                $hasTime = isset($info['remaining_minutes']);
                
                if ($hasTime) {
                    $remain = $info['remaining_minutes'];
                    
                    // Phân loại cảnh báo
                    if ($remain <= 0) {
                        // Combo đã hết thời gian nhưng chưa tắt
                        $expiredTables->push(['table' => $table, 'over' => abs($remain)]);
                    } elseif ($remain <= 5) {
                        // Còn dưới 5 phút
                        $criticalTables->push(['table' => $table, 'min' => $remain]);
                    } elseif ($remain <= 10) {
                        // Còn dưới 10 phút
                        $warningTables->push(['table' => $table, 'min' => $remain]);
                    }
                } else {
                    // Combo chỉ có sản phẩm, không có thời gian - không cảnh báo
                    $comboProductsOnly->push($table);
                }
            }
        }
    @endphp

    {{-- 1. CẢNH BÁO KHẨN CẤP: COMBO ĐÃ HẾT GIỜ --}}
    @if ($expiredTables->count() > 0)
        <div class="relative bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl p-5 mb-6 shadow-lg border-2 border-red-800"
            id="expired-warning">
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-bold">COMBO ĐÃ HẾT GIỜ</h3>
                        <p class="text-sm opacity-90">Có <strong>{{ $expiredTables->count() }}</strong> bàn cần xử lý ngay
                        </p>
                    </div>
                </div>
                <button onclick="showExpiredAlert()"
                    class="bg-white text-red-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-50 transition-all">
                    XEM CHI TIẾT
                </button>
            </div>
        </div>
    @endif

    {{-- 2. CẢNH BÁO: Combo còn ≤5 phút --}}
    @if ($criticalTables->count() > 0)
        <div class="bg-gradient-to-r from-red-100 to-red-50 border-l-4 border-red-500 rounded-xl p-4 mb-4"
            id="critical-warning">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-clock text-red-600 text-xl mr-3"></i>
                    <div>
                        <h4 class="font-bold text-red-800">Sắp hết combo (≤5 phút)</h4>
                        <p class="text-sm text-red-600">Có {{ $criticalTables->count() }} bàn cần theo dõi</p>
                    </div>
                </div>
                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full font-bold">{{ $criticalTables->count() }}
                    BÀN</span>
            </div>
        </div>
    @endif

    {{-- 3. CẢNH BÁO SỚM: Combo còn ≤10 phút --}}
    @if ($warningTables->count() > 0)
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-500 rounded-xl p-4 mb-6"
            id="warning-alert">
            <div class="flex items-center">
                <i class="fas fa-hourglass-half text-amber-600 text-xl mr-3"></i>
                <div>
                    <h4 class="font-bold text-amber-900">Combo sắp hết (≤10 phút)</h4>
                    <p class="text-sm text-amber-700">Có {{ $warningTables->count() }} bàn cần lưu ý</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Bộ lọc & Tìm kiếm -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form action="{{ route('admin.tables.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <input type="text" name="search" value="{{ request('search') }}" class="border rounded-lg px-4 py-3"
                placeholder="Tìm bàn...">
            <select name="status" class="border rounded-lg px-4 py-3">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statuses as $k => $v)
                    <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>
                        {{ $v }}</option>
                @endforeach
            </select>
            <select name="table_rate_id" class="border rounded-lg px-4 py-3">
                <option value="">Tất cả loại bàn</option>
                @foreach ($tableRates as $rate)
                    <option value="{{ $rate->id }}" {{ request('table_rate_id') == $rate->id ? 'selected' : '' }}>
                        {{ $rate->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium transition-all">Lọc</button>
                <a href="{{ route('admin.tables.index') }}"
                    class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-medium transition-all">Reset</a>
                <a href="{{ route('admin.tables.create') }}"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium ml-auto transition-all">+
                    Thêm
                    bàn</a>
            </div>
        </form>
    </div>

    <!-- Danh sách bàn -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if ($tables->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Bàn</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Sức chứa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Loại bàn</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Giá/giờ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Combo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($tables as $table)
                            @php
                                $info = $table->time_info ?? [];
                                $isCombo = ($info['mode'] ?? '') === 'combo';
                                $hasTime = isset($info['remaining_minutes']);
                                $remain = $hasTime ? $info['remaining_minutes'] : null;
                                
                                // Chỉ hiển thị cảnh báo nếu:
                                // 1. Đang ở chế độ combo
                                // 2. Bàn đang occupied hoặc paused
                                // 3. Combo có thời gian (có remaining_minutes)
                                
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
                                
                                // Combo chỉ có sản phẩm (không có thời gian)
                                $isProductsOnly = $isCombo && !$hasTime;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-all duration-200 cursor-pointer
                    @if ($isExpired) bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-600
                    @elseif($isCritical) bg-red-50 border-l-4 border-red-500
                    @elseif($isWarning) bg-amber-50 border-l-4 border-amber-400 @endif"
                                onclick="window.location='{{ route('admin.tables.detail', $table->id) }}'"
                                data-table-id="{{ $table->id }}">

                                <td class="px-6 py-5">
                                    <div class="flex items-start space-x-4">
                                        @if ($isExpired || $isCritical || $isWarning)
                                            <div class="relative">
                                                <div
                                                    class="w-10 h-10 rounded-full flex items-center justify-center
                                                    @if ($isExpired) bg-red-100 text-red-600
                                                    @elseif($isCritical) bg-red-50 text-red-500
                                                    @else bg-amber-50 text-amber-500 @endif">
                                                    <i
                                                        class="fas @if ($isExpired) fa-exclamation-circle 
                                                        @elseif($isCritical) fa-clock 
                                                        @else fa-hourglass-half @endif"></i>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <!-- TÊN BÀN - TO HƠN -->
                                            <div
                                                class="font-bold text-xl text-gray-900 mb-1 hover:text-blue-600 transition-colors">
                                                {{ $table->table_name }}
                                            </div>
                                            <!-- MÃ BÀN - NHỎ HƠN -->
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <span
                                                    class="bg-gray-100 px-2 py-1 rounded mr-2">#{{ $table->table_number }}</span>
                                                @if ($table->currentBill?->status === 'quick')
                                                    <span
                                                        class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-bold">
                                                        BÀN LẺ
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($table->currentBill?->user)
                                                <div class="text-xs text-gray-400 mt-1 flex items-center">
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $table->currentBill->user->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-700 rounded-full font-bold">
                                        {{ $table->capacity }}
                                        <span class="text-xs ml-1">người</span>
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    @if ($table->tableRate)
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold
                                {{ str_starts_with($table->tableRate->code, 'VIP')
                                    ? 'bg-purple-100 text-purple-800'
                                    : (str_starts_with($table->tableRate->code, 'COMP')
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-gray-100 text-gray-800') }}">
                                            <i class="fas fa-tag mr-1 text-xs"></i>
                                            {{ $table->tableRate->name }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex flex-col space-y-2">
                                        <span
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-full text-xs font-bold
                            {{ $table->status === 'available'
                                ? 'bg-green-100 text-green-800'
                                : ($table->status === 'occupied' || $table->status === 'quick'
                                    ? 'bg-red-100 text-red-800'
                                    : ($table->status === 'paused'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-gray-100 text-gray-800')) }}">
                                            <i
                                                class="fas 
                                                @if ($table->status === 'available') fa-check-circle 
                                                @elseif($table->status === 'occupied' || $table->status === 'quick') fa-users 
                                                @elseif($table->status === 'paused') fa-pause-circle
                                                @else fa-wrench @endif 
                                                mr-1 text-xs"></i>
                                            {{ $statuses[$table->status] ?? $table->status }}
                                        </span>

                                        {{-- THÔNG BÁO COMBO NHỎ --}}
                                        @if ($isCombo && $hasTime)
                                            <div
                                                class="inline-flex items-center bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 px-2 py-1 rounded text-xs font-bold border border-purple-200">
                                                <i class="fas fa-bolt mr-1 text-xs"></i>
                                                COMBO
                                                @if ($isExpired)
                                                    <span class="ml-1 bg-red-100 text-red-700 px-1 rounded text-xs">HẾT
                                                        GIỜ</span>
                                                @elseif($isCritical)
                                                    <span class="ml-1 bg-red-100 text-red-700 px-1 rounded text-xs">CẤP
                                                        BÁO</span>
                                                @elseif($isWarning)
                                                    <span
                                                        class="ml-1 bg-amber-100 text-amber-700 px-1 rounded text-xs">CẢNH
                                                        BÁO</span>
                                                @endif
                                            </div>
                                        @elseif($isProductsOnly)
                                            <div
                                                class="inline-flex items-center bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-200">
                                                <i class="fas fa-shopping-basket mr-1 text-xs"></i>
                                                COMBO SẢN PHẨM
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 font-bold text-gray-800">
                                    {{ number_format($table->getHourlyRate()) }}đ
                                    <div class="text-xs text-gray-500 font-normal">/giờ</div>
                                </td>

                                {{-- CỘT COMBO RIÊNG - NHỎ GỌN --}}
                                <td class="px-6 py-5 text-center">
                                    @if ($isCombo && $hasTime && $remain !== null)
                                        <div class="flex flex-col items-center">
                                            @if ($isExpired)
                                                <div class="relative group">
                                                    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-2 rounded-lg font-bold text-sm cursor-help"
                                                        title="Combo đã hết giờ">
                                                        -{{ abs($remain) }}'
                                                    </div>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block">
                                                        <div
                                                            class="bg-red-600 text-white text-xs px-2 py-1 rounded whitespace-nowrap">
                                                            Đã quá {{ abs($remain) }} phút
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($isCritical)
                                                <div class="bg-gradient-to-r from-red-400 to-red-500 text-white px-3 py-1.5 rounded-full font-bold text-sm shadow-lg transform hover:scale-105 transition-transform cursor-pointer"
                                                    onclick="showComboAlert('critical', {{ $table->id }})">
                                                    {{ $remain }}'
                                                </div>
                                            @elseif($isWarning)
                                                <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-white px-3 py-1.5 rounded-full font-bold text-sm cursor-pointer"
                                                    onclick="showComboAlert('warning', {{ $table->id }})">
                                                    {{ $remain }}'
                                                </div>
                                            @else
                                                <div
                                                    class="bg-gradient-to-r from-green-400 to-green-500 text-white px-3 py-1.5 rounded-full font-bold text-sm">
                                                    {{ $remain }}'
                                                </div>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1">còn lại</div>
                                        </div>
                                    @elseif($isProductsOnly)
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-3 py-1.5 rounded-full font-bold text-sm">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div class="text-xs text-blue-600 mt-1">Sản phẩm</div>
                                        </div>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5" onclick="event.stopPropagation()">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.tables.detail', $table->id) }}"
                                            class="action-btn bg-blue-50 text-blue-600 hover:bg-blue-100"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (!in_array($table->status, ['occupied', 'quick']))
                                            <a href="{{ route('admin.tables.edit', $table->id) }}"
                                                class="action-btn bg-green-50 text-green-600 hover:bg-green-100"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirmDelete(event, {{ $table->id }})">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="action-btn bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Xóa bàn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($isExpired)
                                            <button onclick="handleExpiredCombo({{ $table->id }})"
                                                class="action-btn bg-red-600 text-white hover:bg-red-700"
                                                title="Xử lý combo hết giờ">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $tables->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-table text-8xl text-gray-300 mb-6"></i>
                <p class="text-2xl text-gray-600 mb-4">Chưa có bàn nào</p>
                <a href="{{ route('admin.tables.create') }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:bg-blue-700 transition-all">
                    + Thêm bàn đầu tiên
                </a>
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-end space-x-4">
        <a href="{{ route('admin.tables.create') }}"
            class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl font-bold hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i> Thêm bàn mới
        </a>
        <a href="{{ route('admin.tables.trashed') }}"
            class="bg-gradient-to-r from-gray-600 to-gray-700 text-white px-8 py-4 rounded-xl font-bold hover:from-gray-700 hover:to-gray-800 transition-all">
            <i class="fas fa-trash-restore mr-2"></i> Bàn đã xóa
        </a>
    </div>

    {{-- Toast thông báo đẹp hơn --}}
    @if (session('success'))
        <div class="fixed bottom-6 right-6 z-50" id="toast-success">
            <div
                class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center animate-slide-in">
                <i class="fas fa-check-circle text-2xl mr-4"></i>
                <div>
                    <p class="font-bold">Thành công!</p>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="ml-8 text-white hover:text-green-200" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="fixed bottom-6 right-6 z-50" id="toast-error">
            <div
                class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center animate-slide-in">
                <i class="fas fa-exclamation-circle text-2xl mr-4"></i>
                <div>
                    <p class="font-bold">Lỗi!</p>
                    <p>{{ session('error') }}</p>
                </div>
                <button class="ml-8 text-white hover:text-red-200" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Modal cảnh báo combo hết giờ --}}
    <div id="expiredModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-6 rounded-t-2xl">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">COMBO ĐÃ HẾT GIỜ</h3>
                        <p class="opacity-90">Cần xử lý ngay các bàn sau</p>
                    </div>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div class="space-y-3">
                    @foreach ($expiredTables as $item)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <div class="font-bold text-lg text-gray-900">{{ $item['table']->table_name }}</div>
                                <div class="text-sm text-gray-600">Bàn #{{ $item['table']->table_number }}</div>
                            </div>
                            <div class="text-right">
                                <div class="bg-red-600 text-white px-3 py-1 rounded-full font-bold">
                                    -{{ $item['over'] }}'
                                </div>
                                <a href="{{ route('admin.tables.detail', $item['table']->id) }}"
                                    class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                    Xử lý ngay →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-2xl flex justify-between">
                <button onclick="closeModal()"
                    class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-all">
                    Đóng
                </button>
                <button onclick="reloadPage()"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                    <i class="fas fa-sync-alt mr-2"></i> Tải lại trang
                </button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }

        .action-btn {
            @apply w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            transition: transform 0.2s;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Hiển thị modal combo hết giờ
        function showExpiredAlert() {
            event.stopPropagation();
            const modal = document.getElementById('expiredModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        // Đóng modal
        function closeModal() {
            const modal = document.getElementById('expiredModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        // Đóng modal khi click bên ngoài
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('expiredModal');
            if (modal && !modal.classList.contains('hidden')) {
                if (event.target === modal) {
                    closeModal();
                }
            }
        });

        // Hiển thị cảnh báo combo theo mức độ
        function showComboAlert(type, tableId) {
            event.stopPropagation();

            const messages = {
                critical: {
                    title: "⚠️ COMBO SẮP HẾT!",
                    message: `Bàn #${tableId} chỉ còn dưới 5 phút combo. Cần xử lý ngay!`,
                    icon: "warning"
                },
                warning: {
                    title: "⏰ COMBO SẮP HẾT",
                    message: `Bàn #${tableId} còn dưới 10 phút combo. Vui lòng theo dõi.`,
                    icon: "info"
                }
            };

            const msg = messages[type];
            if (!msg) return;

            Swal.fire({
                title: msg.title,
                text: msg.message,
                icon: msg.icon,
                confirmButtonText: 'Đã hiểu',
                confirmButtonColor: '#3085d6',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }

        // Xử lý combo hết giờ
        function handleExpiredCombo(tableId) {
            event.stopPropagation();

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
                backdrop: 'rgba(0,0,0,0.7)',
                showClass: {
                    popup: 'animate__animated animate__zoomIn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gia hạn combo
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Đã gia hạn combo cho bàn.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        showClass: {
                            popup: 'animate__animated animate__bounceIn'
                        }
                    });
                } else if (result.isDenied) {
                    // Chuyển sang tính giờ
                    Swal.fire({
                        title: 'Đã chuyển!',
                        text: 'Bàn đã chuyển sang chế độ tính giờ.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Kết thúc combo
                    Swal.fire({
                        title: 'Đã kết thúc!',
                        text: 'Combo đã được kết thúc.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Xác nhận xóa bàn với SweetAlert2
        function confirmDelete(event, tableId) {
            event.stopPropagation();
            event.preventDefault();

            Swal.fire({
                title: 'XÓA BÀN BI-A?',
                text: `Bạn có chắc muốn xóa bàn #${tableId}? Hành động này không thể hoàn tác!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                backdrop: 'rgba(0,0,0,0.7)',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        }

        // Tải lại trang
        function reloadPage() {
            window.location.reload();
        }

        // Tự động reload nếu có cảnh báo khẩn cấp (giảm tần suất)
        let autoReloadTimer = null;

        function checkForEmergencyReload() {
            if (document.querySelector('#expired-warning')) {
                // Chỉ reload nếu có combo hết giờ, và tăng thời gian lên 2 phút
                autoReloadTimer = setTimeout(() => {
                    reloadPage();
                }, 120000); // 2 phút thay vì 30 giây
            }
        }

        // Bắt đầu kiểm tra sau khi trang tải xong
        document.addEventListener('DOMContentLoaded', function() {
            checkForEmergencyReload();

            // Hiển thị modal nếu có combo hết giờ sau 2 giây
            @if ($expiredTables->count() > 0)
                setTimeout(() => {
                    showExpiredAlert();
                }, 2000);
            @endif

            // Thêm hiệu ứng hover cho các hàng
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.classList.add('hover-lift');
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.05)';
                });
                row.addEventListener('mouseleave', function() {
                    this.classList.remove('hover-lift');
                    this.style.boxShadow = '';
                });
            });
        });

        // Dọn dẹp timer khi rời trang
        window.addEventListener('beforeunload', function() {
            if (autoReloadTimer) {
                clearTimeout(autoReloadTimer);
            }
        });

        // Ẩn toast sau 5 giây
        setTimeout(() => {
            document.querySelectorAll('#toast-success, #toast-error').forEach(el => {
                if (el) el.style.opacity = '0';
                setTimeout(() => el?.remove(), 300);
            });
        }, 5000);
    </script>

    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Animate.css for SweetAlert animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endpush