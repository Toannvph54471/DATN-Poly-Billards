@extends('admin.layouts.app')

@section('title', 'Chi tiết hóa đơn - ' . $bill->bill_number)

@section('content')
    <div class="p-6 bg-white rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Chi tiết hóa đơn: {{ $bill->bill_number }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.bills.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
                </a>
                @if ($bill->status === 'Open')
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-stop mr-1"></i> Đóng hóa đơn
                    </button>
                @endif
            </div>
        </div>

        {{-- Thông tin cơ bản --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">Thông tin hóa đơn</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mã hóa đơn:</span>
                        <span class="font-medium">{{ $bill->bill_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bàn:</span>
                        <span class="font-medium">{{ $bill->table->table_name }} ({{ $bill->table->table_number }})</span>
                    </div>

                    {{-- HIỂN THỊ MÃ GIẢM GIÁ --}}
                    @if ($bill->promotion_id && $bill->promotion)
                        <div class="flex justify-between items-start">
                            <span class="text-gray-600">Mã giảm giá:</span>
                            <div class="text-right">
                                <span class="font-medium text-green-600">{{ $bill->promotion->promotion_code }}</span>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $bill->promotion->name }}
                                    @if ($bill->promotion->discount_type === 'percent')
                                        ({{ $bill->promotion->discount_value }}%)
                                    @else
                                        ({{ number_format($bill->promotion->discount_value) }} ₫)
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($bill->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Giảm giá:</span>
                            <span class="font-medium text-green-600">-{{ number_format($bill->discount_amount) }} ₫</span>
                        </div>
                    @endif

                    {{-- Thông tin nhân viên tạo --}}
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nhân viên tạo:</span>
                        <span class="font-medium text-blue-600">
                            {{ $bill->staff->name ?? 'Chưa xác định' }}
                        </span>
                    </div>
                    {{-- Thông tin nhân viên thanh toán --}}
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nhân viên thanh toán:</span>
                        <span class="font-medium text-green-600">
                            @if ($bill->processed_by && $bill->processedBy)
                                {{ $bill->processedBy->name }}
                            @elseif($bill->payments->count() > 0 && $bill->payments->first()->processed_by)
                                {{ $bill->payments->first()->processedBy->name ?? 'Chưa xác định' }}
                            @else
                                <span class="text-yellow-600">Chưa thanh toán</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Khách hàng:</span>
                        <span class="font-medium">{{ $bill->user->name ?? 'Khách vãng lai' }}</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">Trạng thái & Thanh toán</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Trạng thái:</span>
                        <span
                            class="px-2 py-1 rounded text-sm
                        {{ $bill->status === 'Open'
                            ? 'bg-green-100 text-green-700'
                            : ($bill->status === 'quick'
                                ? 'bg-yellow-100 text-yellow-700'
                                : 'bg-gray-100 text-gray-700') }}">
                            @if ($bill->status === 'Open')
                                Đang mở
                            @elseif($bill->status === 'quick')
                                Thanh toán nhanh
                            @else
                                Đã đóng
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Thanh toán:</span>
                        <span
                            class="px-2 py-1 rounded text-sm
                        {{ $bill->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                            @if ($bill->payment_status === 'Pending')
                                Chờ thanh toán
                            @else
                                Đã thanh toán
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phương thức:</span>
                        <span class="font-medium">
                            @if ($bill->payment_method === 'cash')
                                Tiền mặt
                            @elseif($bill->payment_method === 'bank')
                                Chuyển khoản
                            @elseif($bill->payment_method === 'vnpay')
                                VNPay
                            @else
                                Chưa chọn
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Thời gian tạo:</span>
                        <span
                            class="font-medium">{{ \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    {{-- Thời gian thanh toán --}}
                    @if ($bill->payment_status === 'Paid')
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian thanh toán:</span>
                            <span class="font-medium text-green-600">
                                @if ($bill->payments->count() > 0 && $bill->payments->first()->paid_at)
                                    {{ \Carbon\Carbon::parse($bill->payments->first()->paid_at)->format('d/m/Y H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($bill->updated_at)->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chi tiết sử dụng thời gian --}}
        @if ($bill->billTimeUsages->count() > 0)
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-3">Chi tiết sử dụng thời gian</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Bắt đầu</th>
                                <th class="px-4 py-2 text-left">Kết thúc</th>
                                <th class="px-4 py-2 text-left">Thời gian (phút)</th>
                                <th class="px-4 py-2 text-left">Giá/giờ</th>
                                <th class="px-4 py-2 text-left">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bill->billTimeUsages as $timeUsage)
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($timeUsage->start_time)->format('H:i d/m/Y') }}</td>
                                    <td class="px-4 py-2">
                                        {{ $timeUsage->end_time ? \Carbon\Carbon::parse($timeUsage->end_time)->format('H:i d/m/Y') : 'Đang sử dụng' }}
                                    </td>
                                    <td class="px-4 py-2">{{ $timeUsage->duration_minutes ?? 'Đang tính' }}</td>
                                    <td class="px-4 py-2">{{ number_format($timeUsage->hourly_rate) }} ₫/h</td>
                                    <td class="px-4 py-2 font-medium">{{ number_format($timeUsage->total_price) }} ₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Chi tiết sản phẩm/combo --}}
        @if ($bill->billDetails->count() > 0)
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-3">Chi tiết đặt hàng</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Sản phẩm/Combo</th>
                                <th class="px-4 py-2 text-left">Số lượng</th>
                                <th class="px-4 py-2 text-left">Đơn giá</th>
                                <th class="px-4 py-2 text-left">Nhân viên đặt</th>
                                <th class="px-4 py-2 text-left">Thời gian thêm</th>
                                <th class="px-4 py-2 text-left">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bill->billDetails->where('is_combo_component', false) as $detail)
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        @if ($detail->product)
                                            {{ $detail->product->name }}
                                            <span
                                                class="text-sm text-gray-500">({{ $detail->product->product_code }})</span>
                                        @elseif($detail->combo)
                                            {{ $detail->combo->name }}
                                            <span class="text-sm text-gray-500">(Combo)</span>
                                            {{-- Hiển thị chi tiết combo --}}
                                            @if ($detail->combo->comboItems->count() > 0)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Bao gồm:
                                                    @foreach ($detail->combo->comboItems as $comboItem)
                                                        {{ $comboItem->product->name }} (x{{ $comboItem->quantity }}),
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-2">{{ number_format($detail->unit_price) }} ₫</td>
                                    <td class="px-4 py-2">
                                        @if ($detail->addedByUser)
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                    @if ($detail->addedByUser->avatar)
                                                        <img src="{{ asset('storage/' . $detail->addedByUser->avatar) }}"
                                                            alt="{{ $detail->addedByUser->name }}"
                                                            class="w-full h-full rounded-full object-cover">
                                                    @else
                                                        <i class="fas fa-user text-blue-600"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $detail->addedByUser->name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $detail->addedByUser->employee->position ?? 'Nhân viên' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        @if ($detail->added_at)
                                            {{ \Carbon\Carbon::parse($detail->added_at)->format('H:i d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 font-medium">{{ number_format($detail->total_price) }} ₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tổng kết số nhân viên tham gia đặt hàng --}}
                @php
                    // Lấy danh sách nhân viên đã đặt hàng (không trùng)
                    $addedByUsers = $bill->billDetails
                        ->where('is_combo_component', false)
                        ->whereNotNull('added_by')
                        ->pluck('addedByUser')
                        ->filter()
                        ->unique('id');
                @endphp

                @if ($addedByUsers->count() > 0)
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <div class="flex items-center">
                            <i class="fas fa-users text-gray-500 mr-2"></i>
                            <div>
                                <span class="font-medium text-gray-700">Nhân viên phục vụ: </span>
                                @foreach ($addedByUsers as $user)
                                    <span
                                        class="inline-flex items-center bg-blue-50 px-3 py-1 rounded-full text-sm text-blue-700 mr-2 mb-1">
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                                class="w-4 h-4 rounded-full mr-1">
                                        @else
                                            <i class="fas fa-user mr-1 text-xs"></i>
                                        @endif
                                        {{ $user->name }}
                                        @if ($user->employee)
                                            <span class="ml-1 text-xs text-blue-500">
                                                ({{ $user->employee->position ?? '' }})
                                            </span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Thông tin thanh toán chi tiết --}}
        @if ($bill->payments->count() > 0)
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-lg mb-3">Thông tin thanh toán</h3>

                {{-- Tổng kết đơn giản --}}
                <div class="mb-4 p-3 bg-gray-50 border rounded">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-sm text-gray-600">Tổng cần thanh toán:</div>
                            <div class="text-lg font-bold">{{ number_format($bill->actual_final_amount) }} ₫</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Đã thanh toán:</div>
                            <div class="text-lg font-bold text-green-600">
                                {{ number_format($bill->payments->sum('amount')) }} ₫
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chi tiết payment --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">STT</th>
                                <th class="px-4 py-2 text-left">Số tiền</th>
                                <th class="px-4 py-2 text-left">Phương thức</th>
                                <th class="px-4 py-2 text-left">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bill->payments as $index => $payment)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 font-bold text-green-700">
                                        {{ number_format($payment->amount) }} ₫
                                    </td>
                                    <td class="px-4 py-2">
                                        @if ($payment->payment_method === 'cash')
                                            Tiền mặt
                                        @elseif($payment->payment_method === 'bank')
                                            Chuyển khoản
                                        @elseif($payment->payment_method === 'vnpay')
                                            VNPay
                                        @else
                                            {{ $payment->payment_method }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        @if ($payment->paid_at)
                                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('H:i d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td class="px-4 py-3 text-right font-bold">Tổng:</td>
                                <td class="px-4 py-3 font-bold text-green-700">
                                    {{ number_format($bill->payments->sum('amount')) }} ₫
                                </td>
                                <td colspan="2" class="px-4 py-3">
                                    @if ($bill->payments->sum('amount') >= $bill->actual_final_amount)
                                        <span class="text-green-600 font-medium">✓ Đã thanh toán đủ</span>
                                    @else
                                        <span class="text-red-600 font-medium">
                                            Còn thiếu:
                                            {{ number_format($bill->actual_final_amount - $bill->payments->sum('amount')) }}
                                            ₫
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Phần chi tiết mã giảm giá (nếu có) --}}
        @if ($bill->promotion_id && $bill->promotion)
            <div class="border border-green-200 rounded-lg p-4 mb-6 bg-green-50">
                <h3 class="font-semibold text-lg mb-4 text-green-800">
                    <i class="fa-solid fa-tag mr-2"></i>Thông tin mã giảm giá đã sử dụng
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Mã giảm giá:</span>
                            <span class="font-bold text-lg text-green-700 bg-green-100 px-3 py-1 rounded">
                                {{ $bill->promotion->promotion_code }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Tên chương trình:</span>
                            <span class="font-semibold text-green-800">{{ $bill->promotion->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Mô tả:</span>
                            <span class="text-right text-gray-600 max-w-xs">
                                {{ $bill->promotion->description ?? 'Không có mô tả' }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Loại giảm giá:</span>
                            <span class="font-semibold">
                                @if ($bill->promotion->discount_type === 'percent')
                                    <span class="text-blue-600">Theo phần trăm</span>
                                @else
                                    <span class="text-purple-600">Số tiền cố định</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Giá trị giảm:</span>
                            <span class="font-bold text-red-600 text-lg">
                                @if ($bill->promotion->discount_type === 'percent')
                                    {{ $bill->promotion->discount_value }}%
                                @else
                                    {{ number_format($bill->promotion->discount_value) }} ₫
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">Thời gian áp dụng:</span>
                            <div class="text-right">
                                <div class="text-sm">
                                    Từ: <span
                                        class="font-medium">{{ \Carbon\Carbon::parse($bill->promotion->start_date)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-sm">
                                    Đến: <span
                                        class="font-medium">{{ \Carbon\Carbon::parse($bill->promotion->end_date)->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hiển thị số tiền giảm cụ thể --}}
                <div class="mt-4 p-3 bg-green-100 rounded-lg border border-green-200">
                    <div class="flex justify-between items-center">
                        <span class="text-green-800 font-semibold">Số tiền đã giảm trong hóa đơn:</span>
                        <span class="font-bold text-xl text-red-600">-{{ number_format($bill->discount_amount) }} ₫</span>
                    </div>
                </div>

                {{-- Hiển thị ghi chú nếu có liên quan đến mã giảm giá --}}
                @if ($bill->note && (str_contains($bill->note, $bill->promotion->promotion_code) || str_contains($bill->note, 'KM')))
                    <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800">
                            <strong><i class="fa-solid fa-note-sticky mr-1"></i>Ghi chú:</strong> {{ $bill->note }}
                        </p>
                    </div>
                @endif
            </div>
        @elseif($bill->discount_amount > 0)
            <div class="border border-gray-200 rounded-lg p-4 mb-6 bg-gray-50">
                <h3 class="font-semibold text-lg mb-3">Thông tin giảm giá</h3>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Số tiền đã giảm:</span>
                    <span class="font-bold text-xl text-red-600">-{{ number_format($bill->discount_amount) }} ₫</span>
                </div>
                @if ($bill->note)
                    <div class="mt-2 p-2 bg-yellow-50 rounded">
                        <p class="text-sm text-yellow-800">
                            <strong>Ghi chú:</strong> {{ $bill->note }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Tổng kết --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">Tổng kết thanh toán</h3>

            {{-- Hiển thị chi tiết tính tiền nếu có chuyển bàn --}}
            @if ($bill->billTimeUsages->count() > 1)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <h4 class="font-medium text-yellow-800 mb-2">
                        <i class="fas fa-exchange-alt mr-2"></i>Chi tiết tính tiền sau chuyển bàn:
                    </h4>
                    <div class="space-y-2 text-sm">
                        @foreach ($bill->billTimeUsages as $index => $timeUsage)
                            @php
                                $start = \Carbon\Carbon::parse($timeUsage->start_time);
                                $end = $timeUsage->end_time ? \Carbon\Carbon::parse($timeUsage->end_time) : now();
                                $minutes = $end->diffInMinutes($start);
                                $hours = $minutes / 60;
                                $cost = $timeUsage->total_price ?? ($timeUsage->hourly_rate / 60) * $minutes;
                            @endphp
                            <div class="flex justify-between">
                                <span>
                                    Session {{ $index + 1 }}:
                                    {{ $start->format('H:i') }} -
                                    {{ $timeUsage->end_time ? $end->format('H:i') : 'Đang chạy' }}
                                    ({{ number_format($minutes) }} phút)
                                </span>
                                <span class="font-medium">{{ number_format($cost) }} ₫</span>
                            </div>
                        @endforeach
                        <div class="pt-2 border-t">
                            <div class="flex justify-between font-medium">
                                <span>Tổng tiền giờ:</span>
                                <span>{{ number_format($bill->time_cost) }} ₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="max-w-md ml-auto space-y-3">
                {{-- Tổng tiền sản phẩm --}}
                <div class="flex justify-between">
                    <span class="text-gray-600">Tổng tiền sản phẩm:</span>
                    <span class="font-medium">{{ number_format($bill->product_total) }} ₫</span>
                </div>

                {{-- Tổng tiền giờ --}}
                <div class="flex justify-between">
                    <span class="text-gray-600">Tổng tiền giờ:</span>
                    <span class="font-medium">{{ number_format($bill->time_cost) }} ₫</span>
                </div>

                {{-- Tổng tiền thực tế --}}
                <div class="flex justify-between border-t pt-3">
                    <span class="text-gray-600 font-medium">Tổng tiền thực tế:</span>
                    <span class="font-medium text-blue-600">{{ number_format($bill->actual_total_amount) }} ₫</span>
                </div>

                {{-- Giảm giá --}}
                @if ($bill->promotion_id && $bill->promotion)
                    <div class="flex justify-between">
                        <span class="text-gray-600">
                            Giảm giá ({{ $bill->promotion->promotion_code }}):
                        </span>
                        <span class="font-medium text-red-600">-{{ number_format($bill->discount_amount) }} ₫</span>
                    </div>
                @elseif($bill->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Giảm giá:</span>
                        <span class="font-medium text-red-600">-{{ number_format($bill->discount_amount) }} ₫</span>
                    </div>
                @endif

                {{-- Thành tiền thực tế --}}
                <div class="flex justify-between border-t pt-3">
                    <span class="text-gray-800 font-semibold text-lg">Thành tiền thực tế:</span>
                    <span class="font-bold text-xl text-blue-600">{{ number_format($bill->actual_final_amount) }} ₫</span>
                </div>

                {{-- Nếu có sai lệch với database --}}
                @if ($bill->actual_total_amount != $bill->total_amount)
                    <div class="text-sm text-gray-500 text-right">
                        Database: {{ number_format($bill->final_amount) }} ₫
                        (Chênh: {{ number_format($bill->final_amount - $bill->actual_final_amount) }} ₫)
                    </div>
                @endif
            </div>
        </div>

        {{-- Phần tóm tắt nhân viên --}}
        <div class="border border-gray-200 rounded-lg p-4 mt-6 bg-gray-50">
            <h3 class="font-semibold text-lg mb-3">Tóm tắt nhân viên xử lý</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center p-3 bg-white rounded-lg border">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-3">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nhân viên tạo hóa đơn</p>
                        <p class="font-semibold text-blue-600">{{ $bill->staff->name ?? 'Chưa xác định' }}</p>
                        <p class="text-xs text-gray-400">Thời gian:
                            {{ \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if ($bill->payment_status === 'Paid')
                    <div class="flex items-center p-3 bg-white rounded-lg border">
                        <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-3">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nhân viên thanh toán</p>
                            <p class="font-semibold text-green-600">
                                @if ($bill->processed_by && $bill->processedBy)
                                    {{ $bill->processedBy->name }}
                                @elseif($bill->payments->count() > 0 && $bill->payments->first()->processed_by)
                                    {{ $bill->payments->first()->processedBy->name ?? 'Chưa xác định' }}
                                @else
                                    Chưa xác định
                                @endif
                            </p>
                            <p class="text-xs text-gray-400">
                                Thời gian:
                                @if ($bill->payments->count() > 0 && $bill->payments->first()->paid_at)
                                    {{ \Carbon\Carbon::parse($bill->payments->first()->paid_at)->format('d/m/Y H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($bill->updated_at)->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center p-3 bg-white rounded-lg border">
                        <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg mr-3">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái thanh toán</p>
                            <p class="font-semibold text-yellow-600">Chờ thanh toán</p>
                            <p class="text-xs text-gray-400">Hóa đơn chưa được thanh toán</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
