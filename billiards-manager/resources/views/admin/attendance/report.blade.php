@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Báo Cáo Chấm Công</h1>
        <form action="{{ route('admin.attendance.report') }}" method="GET" class="flex space-x-2">
            <input type="date" name="date" value="{{ $date }}" class="border rounded px-3 py-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Xem</button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nhân viên
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ca làm việc
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Thời gian thực tế
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Trạng thái
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ghi chú
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex items-center">
                            <div class="ml-3">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $shift->employee->name }}
                                </p>
                                <p class="text-gray-600 whitespace-no-wrap text-xs">
                                    {{ $shift->employee->employee_code }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $shift->shift->name }}</p>
                        <p class="text-gray-600 text-xs">
                            {{ \Carbon\Carbon::parse($shift->shift->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($shift->shift->end_time)->format('H:i') }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($shift->actual_start_time)
                            <p class="text-green-600 whitespace-no-wrap">
                                In: {{ $shift->actual_start_time->format('H:i') }}
                            </p>
                        @else
                            <p class="text-gray-400">--:--</p>
                        @endif

                        @if($shift->actual_end_time)
                            <p class="text-red-600 whitespace-no-wrap">
                                Out: {{ $shift->actual_end_time->format('H:i') }}
                            </p>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @php
                            $statusClass = 'bg-gray-200 text-gray-800';
                            $statusText = $shift->status;

                            if ($shift->status === 'Scheduled') {
                                $statusClass = 'bg-yellow-200 text-yellow-800';
                                $statusText = 'Chưa vào ca';
                            } elseif ($shift->status === 'active') {
                                $statusClass = 'bg-green-200 text-green-800';
                                $statusText = 'Đang làm việc';
                            } elseif ($shift->status === 'completed') {
                                $statusClass = 'bg-blue-200 text-blue-800';
                                $statusText = 'Hoàn thành';
                            }

                            // Check Late
                            if ($shift->actual_start_time) {
                                $scheduledStart = \Carbon\Carbon::parse($shift->shift_date->format('Y-m-d') . ' ' . $shift->shift->start_time);
                                if ($shift->actual_start_time->gt($scheduledStart->addMinutes(15))) {
                                    $statusClass = 'bg-red-200 text-red-800';
                                    $statusText = 'Đi muộn';
                                }
                            }
                        @endphp
                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $statusClass }} rounded-full">
                            <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full"></span>
                            <span class="relative">{{ $statusText }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $shift->note ?? '-' }}</p>
                        @if($shift->confirmed_by)
                            <p class="text-xs text-gray-500">Duyệt bởi ID: {{ $shift->confirmed_by }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        Không có dữ liệu ca làm việc cho ngày này.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
