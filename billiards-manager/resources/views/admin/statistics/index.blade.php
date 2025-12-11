@extends('admin.layouts.app')

@section('title', 'Thống kê')

@section('content')
<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Thống kê hệ thống</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $label }}</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Tổng doanh thu</p>
            <p class="text-2xl font-bold text-green-600">
                {{ number_format($totalRevenue, 0, ',', '.') }}₫
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Tổng đơn</p>
            <p class="text-2xl font-bold text-blue-600">
                {{ $totalBills }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Khách hàng</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $totalCustomers }}
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-sm text-gray-500">Tăng trưởng</p>
            <p class="text-2xl font-bold {{ $growth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($growth, 1) }}%
            </p>
        </div>
    </div>

    <!-- TABLE -->
   <!-- Revenue by day -->
<div class="bg-white rounded-xl border shadow-sm p-5">
    <h3 class="text-lg font-semibold mb-3">Doanh thu theo ngày</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2 text-left">Ngày</th>
                    <th class="px-4 py-2 text-right">Tổng</th>
                    <th class="px-4 py-2 text-right">Giờ chơi</th>
                    <th class="px-4 py-2 text-right">Dịch vụ</th>
                    <th class="px-4 py-2 text-center">Bàn hoạt động</th>
                    <th class="px-4 py-2 text-center">Lượt chơi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($revenueByDay as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($row->total_revenue ?? 0,0,',','.') }}₫</td>
                        <td class="px-4 py-2 text-right">{{ number_format($row->play_revenue ?? 0,0,',','.') }}₫</td>
                        <td class="px-4 py-2 text-right">{{ number_format($row->service_revenue ?? 0,0,',','.') }}₫</td>
                        <td class="px-4 py-2 text-center">{{ $row->active_tables ?? 0 }}</td>
                        <td class="px-4 py-2 text-center">{{ $row->sessions ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-6 text-gray-400">Không có dữ liệu</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- Per-table -->
<div class="bg-white rounded-xl border shadow-sm p-5 mt-6">
    <h3 class="text-lg font-semibold mb-3">Thống kê theo bàn</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2 text-left">Bàn</th>
                    <th class="px-4 py-2 text-right">Giờ chơi</th>
                    <th class="px-4 py-2 text-right">Lượt chơi</th>
                    <th class="px-4 py-2 text-right">Doanh thu</th>
                    <th class="px-4 py-2 text-center">Hiệu suất %</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($perTable as $t)
                    <tr>
                        <td class="px-4 py-2">{{ $t->table_name ?? 'Bàn '.$t->id }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($t->total_hours ?? 0,1) }}h</td>
                        <td class="px-4 py-2 text-right">{{ $t->total_sessions ?? 0 }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($t->revenue ?? 0,0,',','.') }}₫</td>
                        <td class="px-4 py-2 text-center">
                            {{-- Hiệu suất tính: (giờ chơi / (24 hoặc giờ mở cửa)) *100 - bạn có thể thay công thức --}}
                            {{ isset($t->total_hours) ? number_format(min(100, ($t->total_hours/24)*100),1) : 0 }}%
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-6 text-gray-400">Không có dữ liệu</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white rounded-xl border shadow-sm p-5">
        <h3 class="text-lg font-semibold mb-3">Top dịch vụ bán chạy</h3>
        @forelse($serviceStats['top'] ?? [] as $p)
            <div class="flex justify-between py-2 border-b">
                <div>{{ $p->name }}</div>
                <div class="text-right">{{ $p->qty }} cái • {{ number_format($p->revenue,0,',','.') }}₫</div>
            </div>
        @empty
            <p class="text-center text-gray-400 py-4">Không có dữ liệu</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-5">
        <h3 class="text-lg font-semibold mb-3">Tổng lợi nhuận dịch vụ</h3>
        <!-- <p class="text-2xl font-bold">{{ number_format($serviceStats['profit']->gross_profit ?? 0,0,',','.') }}₫</p> -->
        <p class="text-sm text-gray-500">Tổng doanh thu dịch vụ: {{ number_format($serviceStats['profit']->revenue ?? 0,0,',','.') }}₫</p>
    </div>
</div>

<div class="bg-white rounded-xl border shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        ⏰ Giờ cao điểm
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- TOP GIỜ --}}
        <div>
            <h4 class="text-sm text-gray-500 mb-3">Top giờ theo doanh thu</h4>

            @forelse($peak['byHour'] ?? [] as $h)
                <div class="flex items-center justify-between py-2 border-b last:border-b-0">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded">
                            {{ str_pad($h->hour, 2, '0', STR_PAD_LEFT) }}:00
                        </span>
                        <span class="text-sm text-gray-600">
                            {{ $h->cnt ?? 0 }} lượt
                        </span>
                    </div>

                    <div class="font-semibold text-green-600">
                        {{ number_format($h->revenue ?? 0,0,',','.') }}₫
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-6">Không có dữ liệu</p>
            @endforelse
        </div>

        {{-- KHUNG GIỜ --}}
        <div>
            <h4 class="text-sm text-gray-500 mb-3">Các khung giờ mẫu</h4>

            @forelse($peak['rangeStats'] ?? [] as $k => $v)
                <div class="flex items-center justify-between py-2 border-b last:border-b-0">
                    <div class="text-sm font-medium text-gray-700">
                        {{ $k }}
                    </div>

                    <div class="text-right">
                        <div class="font-semibold text-green-600">
                            {{ number_format($v->revenue ?? 0,0,',','.') }}₫
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $v->cnt ?? 0 }} hóa đơn
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-6">Không có dữ liệu</p>
            @endforelse
        </div>

    </div>
</div>



</div>
@endsection
