@extends('admin.layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <!-- Real-time Notifications Container -->
    <div id="realtime-notifications" class="fixed top-4 right-4 z-50 space-y-2 max-w-md"></div>

    <div class="space-y-6">
        <!-- Header và Search -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý hóa đơn</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Quản lý và tìm kiếm tất cả hóa đơn trong hệ thống
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <button onclick="toggleAdvancedSearch()"
                        class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fa-solid fa-search-plus mr-2"></i>
                        Tìm kiếm nâng cao
                    </button>

                    <!-- Nút reset bộ lọc nếu có filters -->
                    @if (!empty(array_filter($filters)))
                        <form action="{{ route('admin.bills.reset') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fa-solid fa-times mr-2"></i>
                                Xóa bộ lọc
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.tables.index') }}"
                        class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Tạo hóa đơn mới
                    </a>
                </div>
            </div>

            <!-- Thông báo thanh toán real-time -->
            <div id="payment-success-notification" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75">
                            </div>
                            <div class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <i class="fa-solid fa-credit-card text-green-500 mr-2"></i>
                                <span class="text-green-700 font-medium">
                                    <span id="payment-bill-number">HĐ0001</span> - Thanh toán thành công!
                                </span>
                            </div>
                            <div id="payment-details" class="text-sm text-green-600 mt-1">
                                <!-- Chi tiết sẽ được cập nhật bằng JS -->
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button id="view-paid-bill"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-all">
                            <i class="fa-solid fa-eye mr-1"></i> Xem
                        </button>
                        <button onclick="closePaymentNotification()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded text-sm transition-all">
                            <i class="fa-solid fa-times mr-1"></i> Đóng
                        </button>
                    </div>
                </div>
            </div>

            <!-- Thống kê nhanh -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-blue-600 font-bold text-2xl">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Tổng hóa đơn</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-green-600 font-bold text-2xl">{{ $stats['open'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Đang mở</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="text-purple-600 font-bold text-2xl">{{ $stats['paid'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Đã thanh toán</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-yellow-600 font-bold text-2xl">{{ $stats['today'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Hôm nay</div>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="text-indigo-600 font-bold text-2xl">{{ number_format($stats['total_amount_today']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Doanh thu hôm nay (VNĐ)</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="text-gray-600 font-bold text-2xl">{{ number_format($bills->total()) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Kết quả tìm kiếm</div>
                </div>
            </div>

            <!-- Form tìm kiếm nâng cao -->
            <div id="advancedSearch"
                class="{{ !empty(array_filter($filters)) ? '' : 'hidden' }} bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                <form method="POST" action="{{ route('admin.bills.filter') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Tìm kiếm từ khóa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-search mr-1"></i> Từ khóa
                            </label>
                            <input type="text" name="query" value="{{ $filters['query'] ?? '' }}"
                                placeholder="Mã hóa đơn, tên KH, SĐT, email, tên NV, bàn..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Trạng thái hóa đơn -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-tag mr-1"></i> Trạng thái hóa đơn
                            </label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="Open" {{ ($filters['status'] ?? '') == 'Open' ? 'selected' : '' }}>Đang mở
                                </option>
                                <option value="Closed" {{ ($filters['status'] ?? '') == 'Closed' ? 'selected' : '' }}>Đã
                                    đóng</option>
                                <option value="quick" {{ ($filters['status'] ?? '') == 'quick' ? 'selected' : '' }}>Thanh
                                    toán nhanh</option>
                            </select>
                        </div>

                        <!-- Trạng thái thanh toán -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-credit-card mr-1"></i> Thanh toán
                            </label>
                            <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="all">Tất cả</option>
                                <option value="Pending"
                                    {{ ($filters['payment_status'] ?? '') == 'Pending' ? 'selected' : '' }}>Chờ thanh toán
                                </option>
                                <option value="Paid"
                                    {{ ($filters['payment_status'] ?? '') == 'Paid' ? 'selected' : '' }}>Đã thanh toán
                                </option>
                            </select>
                        </div>

                        <!-- Khoảng thời gian -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-calendar mr-1"></i> Từ ngày
                            </label>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-calendar mr-1"></i> Đến ngày
                            </label>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Khoảng tiền -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Từ số tiền
                            </label>
                            <input type="number" name="min_amount" value="{{ $filters['min_amount'] ?? '' }}"
                                placeholder="Số tiền tối thiểu"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Đến số tiền
                            </label>
                            <input type="number" name="max_amount" value="{{ $filters['max_amount'] ?? '' }}"
                                placeholder="Số tiền tối đa" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Chọn bàn -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-table mr-1"></i> Bàn
                            </label>
                            <select name="table_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="">Tất cả bàn</option>
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}"
                                        {{ ($filters['table_id'] ?? '') == $table->id ? 'selected' : '' }}>
                                        {{ $table->table_name }} ({{ $table->table_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Chọn nhân viên -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-user-tie mr-1"></i> Nhân viên
                            </label>
                            <select name="staff_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="">Tất cả nhân viên</option>
                                @foreach ($staff as $s)
                                    <option value="{{ $s->id }}"
                                        {{ ($filters['staff_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sắp xếp -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fa-solid fa-sort mr-1"></i> Sắp xếp theo
                            </label>
                            <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="created_at_desc"
                                    {{ ($filters['sort_by'] ?? 'created_at_desc') == 'created_at_desc' ? 'selected' : '' }}>
                                    Ngày tạo (mới nhất)</option>
                                <option value="created_at_asc"
                                    {{ ($filters['sort_by'] ?? '') == 'created_at_asc' ? 'selected' : '' }}>Ngày tạo (cũ
                                    nhất)</option>
                                <option value="updated_at_desc"
                                    {{ ($filters['sort_by'] ?? '') == 'updated_at_desc' ? 'selected' : '' }}>Ngày cập nhật
                                    (mới nhất)</option>
                                <option value="updated_at_asc"
                                    {{ ($filters['sort_by'] ?? '') == 'updated_at_asc' ? 'selected' : '' }}>Ngày cập nhật
                                    (cũ nhất)</option>
                                <option value="total_amount_desc"
                                    {{ ($filters['sort_by'] ?? '') == 'total_amount_desc' ? 'selected' : '' }}>
                                    Tổng tiền (cao nhất)</option>
                                <option value="total_amount_asc"
                                    {{ ($filters['sort_by'] ?? '') == 'total_amount_asc' ? 'selected' : '' }}>
                                    Tổng tiền (thấp nhất)</option>
                                <option value="bill_number_desc"
                                    {{ ($filters['sort_by'] ?? '') == 'bill_number_desc' ? 'selected' : '' }}>Mã hóa đơn
                                    (Z-A)</option>
                                <option value="bill_number_asc"
                                    {{ ($filters['sort_by'] ?? '') == 'bill_number_asc' ? 'selected' : '' }}>Mã hóa đơn
                                    (A-Z)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fa-solid fa-search mr-1"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kết quả tìm kiếm -->
            @if (!empty(array_filter($filters)))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-700">
                                <i class="fa-solid fa-filter mr-2"></i>
                                Đang hiển thị kết quả tìm kiếm:
                                @if ($filters['query'] ?? '')
                                    <strong>"{{ $filters['query'] }}"</strong>
                                @endif
                                @if (($filters['status'] ?? '') && ($filters['status'] ?? '') !== 'all')
                                    | Trạng thái: <span class="font-medium">
                                        {{ ($filters['status'] ?? '') == 'Open'
                                            ? 'Đang mở'
                                            : (($filters['status'] ?? '') == 'Closed'
                                                ? 'Đã đóng'
                                                : 'Thanh toán nhanh') }}
                                    </span>
                                @endif
                                @if (($filters['payment_status'] ?? '') && ($filters['payment_status'] ?? '') !== 'all')
                                    | Thanh toán: <span class="font-medium">
                                        {{ ($filters['payment_status'] ?? '') == 'Paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <span class="text-sm text-gray-600">
                                Tìm thấy <strong>{{ $bills->total() }}</strong> kết quả
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Danh sách hóa đơn -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã hóa đơn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khách hàng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bàn
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nhân viên
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tổng tiền
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời gian
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="bills-table-body">
                        @forelse($bills as $bill)
                            <tr class="bill-row group hover:bg-blue-50 transition-all duration-200 cursor-pointer border-l-4 hover:border-l-blue-500 border-l-transparent"
                                data-id="{{ $bill->id }}" data-status="{{ $bill->status }}"
                                data-payment-status="{{ $bill->payment_status }}"
                                data-bill-number="{{ $bill->bill_number }}" data-amount="{{ $bill->final_amount }}"
                                onclick="window.location.href='{{ route('admin.bills.show', $bill->id) }}'"
                                title="Click để xem chi tiết hóa đơn">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if ($bill->payment_status === 'Paid')
                                                <i
                                                    class="fa-solid fa-check-circle text-green-500 text-lg group-hover:text-green-600 transition-colors"></i>
                                            @elseif($bill->status === 'Open')
                                                <i
                                                    class="fa-solid fa-clock text-blue-500 text-lg group-hover:text-blue-600 transition-colors"></i>
                                            @else
                                                <i
                                                    class="fa-solid fa-receipt text-gray-500 text-lg group-hover:text-gray-600 transition-colors"></i>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div
                                                class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $bill->bill_number }}
                                                @if ($bill->payment_status === 'Paid' && \Carbon\Carbon::parse($bill->updated_at)->diffInMinutes() < 5)
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gradient-to-r from-green-500 to-green-600 text-white shadow-sm animate-pulse">
                                                        <i class="fa-solid fa-credit-card mr-1"></i> VỪA TT
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                                ID: {{ $bill->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900 group-hover:text-gray-800 transition-colors">
                                        {{ $bill->user->name ?? 'Khách vãng lai' }}
                                    </div>
                                    <div class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                        <i class="fa-solid fa-phone text-xs mr-1"></i>
                                        {{ $bill->user->phone ?? 'Chưa có SĐT' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900 group-hover:text-gray-800 transition-colors">
                                        {{ $bill->table->table_name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                        <i class="fa-solid fa-hashtag text-xs mr-1"></i>
                                        {{ $bill->table->table_number ?? '' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900 group-hover:text-gray-800 transition-colors">
                                        {{ $bill->staff->name ?? 'Chưa xác định' }}
                                    </div>
                                    <div class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                        <i class="fa-solid fa-id-card text-xs mr-1"></i>
                                        {{ $bill->staff->code ?? '' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-bold text-gray-900 group-hover:text-gray-800 transition-colors">
                                        {{ number_format($bill->final_amount) }} ₫
                                    </div>
                                    @if ($bill->discount_amount > 0)
                                        <div class="text-xs text-red-600 group-hover:text-red-700 transition-colors">
                                            <i class="fa-solid fa-tag mr-1"></i>
                                            -{{ number_format($bill->discount_amount) }} ₫
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-2">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        {{ $bill->status === 'Open'
                            ? 'bg-green-100 text-green-800 group-hover:bg-green-200 group-hover:text-green-900'
                            : ($bill->status === 'Closed'
                                ? 'bg-gray-100 text-gray-800 group-hover:bg-gray-200 group-hover:text-gray-900'
                                : 'bg-blue-100 text-blue-800 group-hover:bg-blue-200 group-hover:text-blue-900') }}">
                                            <i
                                                class="fa-solid 
                            {{ $bill->status === 'Open' ? 'fa-clock' : ($bill->status === 'Closed' ? 'fa-check' : 'fa-bolt') }} 
                            mr-1.5"></i>
                                            {{ $bill->status === 'Open' ? 'Đang mở' : ($bill->status === 'Closed' ? 'Đã đóng' : 'Nhanh') }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        {{ $bill->payment_status === 'Paid'
                            ? 'bg-gradient-to-r from-purple-100 to-purple-50 text-purple-800 group-hover:from-purple-200 group-hover:to-purple-100'
                            : 'bg-gradient-to-r from-yellow-100 to-yellow-50 text-yellow-800 group-hover:from-yellow-200 group-hover:to-yellow-100' }}">
                                            <i
                                                class="fa-solid 
                            {{ $bill->payment_status === 'Paid' ? 'fa-check-circle' : 'fa-hourglass-half' }} 
                            mr-1.5"></i>
                                            {{ $bill->payment_status === 'Paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm text-gray-900 font-medium group-hover:text-gray-800 transition-colors">
                                        <i class="fa-solid fa-calendar-day mr-1.5"></i>
                                        {{ \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-700 group-hover:text-gray-800 transition-colors">
                                        <i class="fa-solid fa-clock mr-1.5"></i>
                                        {{ \Carbon\Carbon::parse($bill->created_at)->format('H:i') }}
                                    </div>
                                    @if ($bill->status === 'Closed' && $bill->end_time)
                                        <div
                                            class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors mt-1">
                                            <i class="fa-solid fa-flag-checkered mr-1"></i>
                                            {{ \Carbon\Carbon::parse($bill->end_time)->format('H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    onclick="event.stopPropagation()">
                                    <div
                                        class="flex space-x-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 -translate-x-2">
                                        <a href="{{ route('admin.bills.show', $bill->id) }}"
                                            class="action-btn bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800"
                                            title="Xem chi tiết">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bills.print', $bill->id) }}" target="_blank"
                                            class="action-btn bg-purple-50 text-purple-600 hover:bg-purple-100 hover:text-purple-800"
                                            title="In hóa đơn">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                        @if ($bill->status === 'Open' || $bill->status === 'quick')
                                            <a href="{{ route('admin.payments.payment-page', $bill->id) }}"
                                                class="action-btn bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-800"
                                                title="Thanh toán">
                                                <i class="fa-solid fa-credit-card"></i>
                                            </a>
                                        @endif
                                        <button onclick="showBillActions({{ $bill->id }}, event)"
                                            class="action-btn bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                                            title="Thao tác khác">
                                            <i class="fa-solid fa-ellipsis-v"></i>
                                        </button>
                                    </div>

                                    <!-- Hiển thị khi không hover -->
                                    <div
                                        class="opacity-100 group-hover:opacity-0 transition-all duration-300 flex space-x-2">
                                        <span class="text-xs text-gray-400 font-normal">
                                            Click để xem chi tiết
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fa-solid fa-receipt text-4xl mb-3"></i>
                                        <p class="text-lg">Không tìm thấy hóa đơn nào</p>
                                        @if (!empty(array_filter($filters)))
                                            <p class="text-sm mt-2">Hãy thử thay đổi tiêu chí tìm kiếm</p>
                                        @else
                                            <p class="text-sm mt-2">Chưa có hóa đơn nào được tạo</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($bills->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bills->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal actions menu -->
    <div id="billActionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="fixed right-4 top-4 bg-white rounded-lg shadow-xl w-64">
            <div class="p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Thao tác hóa đơn</h4>
                <div id="billActionsContent" class="space-y-2">
                    <!-- Nội dung sẽ được cập nhật bằng JavaScript -->
                </div>
                <button onclick="closeBillActions()"
                    class="w-full mt-4 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Modal thông báo thanh toán -->
    <div id="paymentAlertModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <i class="fa-solid fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2" id="paymentAlertTitle">
                            Thanh toán thành công!
                        </h3>
                        <div class="text-sm text-gray-500 mb-4" id="paymentAlertContent">
                            <!-- Nội dung sẽ được cập nhật -->
                        </div>
                        <div class="flex space-x-3 justify-center">
                            <button onclick="viewPaidBill()"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fa-solid fa-eye mr-1"></i> Xem hóa đơn
                            </button>
                            <button onclick="closePaymentAlert()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Animation cho notification */
        .animate-slide-in-right {
            animation: slideInRight 0.5s ease-out forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Hiệu ứng cho hàng vừa thanh toán */
        .new-payment-highlight {
            animation: highlightPayment 10s ease-in-out;
            background: linear-gradient(90deg, #f0fdf4 0%, #fff 100%);
            border-left: 4px solid #10b981 !important;
        }

        @keyframes highlightPayment {
            0% {
                background-color: #d1fae5;
                border-left-color: #059669;
            }

            30% {
                background-color: #ecfdf5;
                border-left-color: #10b981;
            }

            100% {
                background-color: transparent;
                border-left-color: #d1d5db;
            }
        }

        /* Toast notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
        }

        .toast-notification.hiding {
            animation: slideOutRight 0.3s ease-out forwards;
        }

        /* Ping animation */
        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* Pulse animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    <style>
        .bill-row {
            transition: all 0.3s ease;
        }

        .bill-row:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .action-btn {
            @apply w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

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
            animation: pulse 2s infinite;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Biến toàn cục
        let lastPaymentCheck = null;
        let paymentCheckInterval = null;
        let currentPaidBillId = null;
        let paymentNotificationQueue = [];
        let isShowingPaymentAlert = false;

        // Hiển thị/ẩn tìm kiếm nâng cao
        function toggleAdvancedSearch() {
            const searchPanel = document.getElementById('advancedSearch');
            searchPanel.classList.toggle('hidden');

            // Cuộn đến form tìm kiếm
            if (!searchPanel.classList.contains('hidden')) {
                searchPanel.scrollIntoView({
                    behavior: 'smooth'
                });
                const firstInput = searchPanel.querySelector('input, select');
                if (firstInput) firstInput.focus();
            }
        }

        // Reset tìm kiếm
        function resetSearch() {
            window.location.href = "{{ route('admin.bills.reset') }}";
        }

        // Hiển thị menu actions
        function showBillActions(billId) {
            const billRow = document.querySelector(`tr[data-id="${billId}"]`);
            const billNumber = billRow?.getAttribute('data-bill-number') || '';
            const amount = billRow?.getAttribute('data-amount') || 0;
            const status = billRow?.getAttribute('data-status') || '';
            const paymentStatus = billRow?.getAttribute('data-payment-status') || '';

            const formattedAmount = new Intl.NumberFormat('vi-VN').format(amount);

            let actions = `
            <a href="/admin/bills/${billId}" target="_blank"
               class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                <i class="fa-solid fa-eye mr-2 text-blue-500"></i>
                Xem chi tiết
            </a>
            <a href="/admin/bills/${billId}/print" target="_blank"
               class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                <i class="fa-solid fa-print mr-2 text-purple-500"></i>
                In hóa đơn
            </a>
        `;

            if (status === 'Open' || status === 'quick') {
                actions += `
                <a href="/admin/payments/${billId}"
                   class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="fa-solid fa-credit-card mr-2 text-green-500"></i>
                    Thanh toán
                </a>
            `;
            }

            if (paymentStatus === 'Paid') {
                actions += `
                <a href="/admin/payments/bill/${billId}"
                   class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="fa-solid fa-receipt mr-2 text-indigo-500"></i>
                    Chi tiết thanh toán
                </a>
            `;
            }

            actions += `
            <div class="border-t pt-2 mt-2">
                <button onclick="copyBillInfo('${billNumber}', '${formattedAmount}')"
                        class="w-full text-left flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="fa-solid fa-copy mr-2 text-gray-500"></i>
                    Sao chép thông tin
                </button>
                <button onclick="sendBillReceipt(${billId})"
                        class="w-full text-left flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="fa-solid fa-paper-plane mr-2 text-blue-500"></i>
                    Gửi hóa đơn qua email
                </button>
            </div>
        `;

            // Chỉ hiển thị nút xóa cho admin hoặc hóa đơn chưa thanh toán
            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                if (paymentStatus === 'Pending') {
                    actions += `
                    <div class="border-t pt-2 mt-2">
                        <button onclick="confirmDeleteBill(${billId})"
                                class="w-full text-left flex items-center px-3 py-2 text-red-600 hover:bg-red-50 rounded transition">
                            <i class="fa-solid fa-trash mr-2"></i>
                            Xóa hóa đơn
                        </button>
                    </div>
                `;
                }
            @endif

            document.getElementById('billActionsContent').innerHTML = actions;
            document.getElementById('billActionsModal').classList.remove('hidden');
        }

        // Đóng menu actions
        function closeBillActions() {
            document.getElementById('billActionsModal').classList.add('hidden');
        }

        // Sao chép thông tin bill
        function copyBillInfo(billNumber, amount) {
            const text = `Hóa đơn: ${billNumber}\nTổng tiền: ${amount}₫`;
            navigator.clipboard.writeText(text).then(() => {
                showToast('Đã sao chép thông tin hóa đơn', 'success');
            }).catch(() => {
                showToast('Không thể sao chép', 'error');
            });
            closeBillActions();
        }

        // Gửi hóa đơn qua email
        function sendBillReceipt(billId) {
            showToast('Đang gửi hóa đơn...', 'info');
            fetch(`/admin/bills/${billId}/send-receipt`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Đã gửi hóa đơn thành công', 'success');
                    } else {
                        showToast(data.message || 'Gửi thất bại', 'error');
                    }
                })
                .catch(error => {
                    showToast('Có lỗi xảy ra', 'error');
                });
            closeBillActions();
        }

        // Xóa hóa đơn
        function confirmDeleteBill(billId) {
            if (confirm('Bạn có chắc muốn xóa hóa đơn này? Hành động này không thể hoàn tác.')) {
                fetch(`/admin/bills/${billId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            showToast('Đã xóa hóa đơn', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('Không thể xóa hóa đơn', 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Có lỗi xảy ra', 'error');
                    });
            }
            closeBillActions();
        }

        // Hiển thị toast notification
        function showToast(message, type = 'info') {
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800',
                warning: 'bg-yellow-50 border-yellow-200 text-yellow-800'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle',
                warning: 'fa-exclamation-triangle'
            };

            const toastId = 'toast-' + Date.now();
            const toastHtml = `
            <div id="${toastId}" class="toast-notification ${colors[type]} border rounded-lg p-4 mb-2 shadow-lg">
                <div class="flex items-center">
                    <i class="fa-solid ${icons[type]} mr-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="document.getElementById('${toastId}').remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
        `;

            const container = document.getElementById('realtime-notifications');
            container.insertAdjacentHTML('afterbegin', toastHtml);

            // Tự động xóa sau 5 giây
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.classList.add('hiding');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        // Kiểm tra thanh toán mới
        function checkForNewPayments() {
            const now = new Date().toISOString();

            fetch('/admin/bills/check-new-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        last_check: lastPaymentCheck || new Date(Date.now() - 5 * 60000).toISOString()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.new_payments && data.new_payments.length > 0) {
                        lastPaymentCheck = data.current_time;

                        data.new_payments.forEach(payment => {
                            // Thêm vào queue nếu chưa có
                            if (!paymentNotificationQueue.some(p => p.id === payment.id)) {
                                paymentNotificationQueue.push(payment);
                            }
                        });

                        // Xử lý queue
                        processPaymentQueue();

                        // Cập nhật thống kê
                        updateStats(data.new_payments.length, data.new_payments.reduce((sum, p) => sum + p.final_amount,
                            0));
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi kiểm tra thanh toán mới:', error);
                });
        }

        // Xử lý payment queue
        function processPaymentQueue() {
            if (!isShowingPaymentAlert && paymentNotificationQueue.length > 0) {
                isShowingPaymentAlert = true;
                const payment = paymentNotificationQueue.shift();
                showPaymentSuccessAlert(payment);
            }
        }

        // Hiển thị thông báo thanh toán thành công
        function showPaymentSuccessAlert(payment) {
            currentPaidBillId = payment.id;

            // Cập nhật thông báo trên header
            const notificationDiv = document.getElementById('payment-success-notification');
            const billNumberSpan = document.getElementById('payment-bill-number');
            const detailsDiv = document.getElementById('payment-details');

            billNumberSpan.textContent = payment.bill_number;
            detailsDiv.innerHTML = `
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <i class="fa-solid fa-money-bill-wave mr-1"></i>
                    <span class="font-bold">${new Intl.NumberFormat('vi-VN').format(payment.final_amount)}₫</span>
                </div>
                <div>
                    <i class="fa-solid fa-user-tie mr-1"></i>
                    ${payment.staff_name}
                </div>
                <div>
                    <i class="fa-solid fa-table mr-1"></i>
                    ${payment.table_name || 'N/A'}
                </div>
                <div>
                    <i class="fa-solid fa-clock mr-1"></i>
                    ${formatTimeAgo(payment.created_at)}
                </div>
            </div>
        `;

            notificationDiv.classList.remove('hidden');

            // Cập nhật button xem
            document.getElementById('view-paid-bill').onclick = function() {
                viewPaidBill();
            };

            // Thêm hiệu ứng cho hàng trong bảng
            highlightPaidBillInTable(payment);

            // Tự động đóng sau 15 giây
            setTimeout(() => {
                closePaymentNotification();
            }, 15000);
        }

        // Đóng thông báo thanh toán
        function closePaymentNotification() {
            document.getElementById('payment-success-notification').classList.add('hidden');
            isShowingPaymentAlert = false;

            // Xử lý payment tiếp theo trong queue
            processPaymentQueue();
        }

        // Xem hóa đơn vừa thanh toán
        function viewPaidBill() {
            if (currentPaidBillId) {
                window.open(`/admin/bills/${currentPaidBillId}`, '_blank');
                closePaymentNotification();
            }
        }

        // Thêm hiệu ứng cho hàng vừa thanh toán
        function highlightPaidBillInTable(payment) {
            // Tìm hàng trong bảng
            let row = document.querySelector(`tr[data-id="${payment.id}"]`);

            if (row) {
                // Thêm hiệu ứng highlight
                row.classList.add('new-payment-highlight');

                // Cập nhật trạng thái
                const statusCell = row.querySelector('td:nth-child(6)');
                if (statusCell) {
                    statusCell.innerHTML = `
                    <div class="space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Đã đóng
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Đã TT
                        </span>
                    </div>
                `;
                }

                // Cập nhật icon
                const iconCell = row.querySelector('td:first-child .flex-shrink-0 i');
                if (iconCell) {
                    iconCell.className = 'fa-solid fa-check-circle text-green-400';
                }

                // Thêm badge "VỪA TT"
                const firstCell = row.querySelector('td:first-child .ml-3');
                if (firstCell) {
                    // Xóa badge cũ nếu có
                    const oldBadge = firstCell.querySelector('.new-payment-badge');
                    if (oldBadge) oldBadge.remove();

                    // Thêm badge mới
                    const badge = document.createElement('span');
                    badge.className =
                        'ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse new-payment-badge';
                    badge.innerHTML = '<i class="fa-solid fa-credit-card mr-1"></i> VỪA TT';
                    firstCell.appendChild(badge);
                }

                // Tự động xóa highlight sau 10 giây
                setTimeout(() => {
                    row.classList.remove('new-payment-highlight');
                }, 10000);
            } else {
                // Nếu chưa có trong bảng, thêm vào
                addPaidBillToTable(payment);
            }
        }

        // Thêm bill vừa thanh toán vào bảng
        function addPaidBillToTable(payment) {
            const tbody = document.getElementById('bills-table-body');

            const newRow = document.createElement('tr');
            newRow.className = 'new-payment-highlight hover:bg-gray-50 bill-row';
            newRow.setAttribute('data-id', payment.id);
            newRow.setAttribute('data-status', 'Closed');
            newRow.setAttribute('data-payment-status', 'Paid');
            newRow.setAttribute('data-bill-number', payment.bill_number);
            newRow.setAttribute('data-amount', payment.final_amount);

            const createdDate = new Date(payment.created_at);
            const dateStr =
                `${createdDate.getDate().toString().padStart(2, '0')}/${(createdDate.getMonth() + 1).toString().padStart(2, '0')}/${createdDate.getFullYear()}`;
            const timeStr =
                `${createdDate.getHours().toString().padStart(2, '0')}:${createdDate.getMinutes().toString().padStart(2, '0')}`;

            newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                            ${payment.bill_number}
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse new-payment-badge">
                                <i class="fa-solid fa-credit-card mr-1"></i> VỪA TT
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            ID: ${payment.id}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${payment.user_name || 'Khách vãng lai'}</div>
                <div class="text-xs text-gray-500">
                    ${payment.user_phone || 'Chưa có SĐT'}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    ${payment.table_name || 'N/A'}
                </div>
                <div class="text-xs text-gray-500">
                    ${payment.table_number || ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    ${payment.staff_name || 'Chưa xác định'}
                </div>
                <div class="text-xs text-gray-500">
                    ${payment.staff_code || ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">
                    ${new Intl.NumberFormat('vi-VN').format(payment.final_amount)} ₫
                </div>
                ${payment.discount_amount > 0 ? 
                    `<div class="text-xs text-red-600">-${new Intl.NumberFormat('vi-VN').format(payment.discount_amount)} ₫</div>` : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="space-y-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Đã đóng
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Đã TT
                    </span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div>${dateStr}</div>
                <div>${timeStr}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <a href="/admin/bills/${payment.id}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="/admin/bills/${payment.id}/print" target="_blank" class="text-purple-600 hover:text-purple-900" title="In hóa đơn">
                        <i class="fa-solid fa-print"></i>
                    </a>
                    <button onclick="showBillActions(${payment.id})" class="text-gray-600 hover:text-gray-900" title="Thao tác khác">
                        <i class="fa-solid fa-ellipsis-v"></i>
                    </button>
                </div>
            </td>
        `;

            // Thêm vào đầu bảng
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }

            // Tự động xóa highlight sau 10 giây
            setTimeout(() => {
                newRow.classList.remove('new-payment-highlight');
            }, 10000);
        }

        // Cập nhật thống kê
        function updateStats(count, amount) {
            const paidElement = document.querySelector('.bg-purple-50 .text-2xl');
            const revenueElement = document.querySelector('.bg-indigo-50 .text-2xl');
            const resultsElement = document.querySelector('.bg-gray-50 .text-2xl');

            if (paidElement) {
                const currentPaid = parseInt(paidElement.textContent.replace(/,/g, ''));
                paidElement.textContent = (currentPaid + count).toLocaleString();
            }

            if (revenueElement) {
                const currentRevenue = parseInt(revenueElement.textContent.replace(/,/g, ''));
                revenueElement.textContent = (currentRevenue + amount).toLocaleString();
            }

            if (resultsElement) {
                const currentResults = parseInt(resultsElement.textContent.replace(/,/g, ''));
                resultsElement.textContent = (currentResults + count).toLocaleString();
            }
        }

        // Format thời gian đã trôi qua
        function formatTimeAgo(timestamp) {
            const now = new Date();
            const past = new Date(timestamp);
            const diffMs = now - past;
            const diffMins = Math.floor(diffMs / 60000);

            if (diffMins < 1) return 'Vừa xong';
            if (diffMins === 1) return '1 phút trước';
            if (diffMins < 60) return `${diffMins} phút trước`;

            const diffHours = Math.floor(diffMins / 60);
            if (diffHours === 1) return '1 giờ trước';
            if (diffHours < 24) return `${diffHours} giờ trước`;

            const diffDays = Math.floor(diffHours / 24);
            return `${diffDays} ngày trước`;
        }

        // Kiểm tra từ URL parameter
        function checkUrlForPaymentSuccess() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentSuccess = urlParams.get('payment_success');
            const billId = urlParams.get('bill_id');

            if (paymentSuccess === 'true' && billId) {
                // Gửi request để lấy thông tin bill vừa thanh toán
                fetch(`/admin/bills/${billId}/payment-info`)
                    .then(response => response.json())
                    .then(bill => {
                        if (bill.id) {
                            showPaymentSuccessAlert(bill);

                            // Xóa parameter khỏi URL
                            const newUrl = window.location.pathname;
                            window.history.replaceState({}, document.title, newUrl);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy thông tin payment:', error);
                    });
            }
        }

        // Khởi tạo
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra URL cho payment success
            checkUrlForPaymentSuccess();

            // Đóng modal khi click bên ngoài
            document.getElementById('billActionsModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeBillActions();
                }
            });

            document.getElementById('paymentAlertModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closePaymentAlert();
                }
            });

            // Escape để đóng modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeBillActions();
                    closePaymentAlert();
                }
            });

            // Bắt đầu kiểm tra thanh toán mới mỗi 5 giây
            lastPaymentCheck = new Date().toISOString();
            paymentCheckInterval = setInterval(checkForNewPayments, 5000);

            // Kiểm tra ngay khi load trang
            setTimeout(checkForNewPayments, 1000);
        });

        // Dọn dẹp interval khi rời trang
        window.addEventListener('beforeunload', function() {
            if (paymentCheckInterval) {
                clearInterval(paymentCheckInterval);
            }
        });
    </script>


@endsection
