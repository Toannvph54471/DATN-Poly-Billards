@extends('admin.layouts.app')

@section('title', 'Chi tiết ca làm')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Chi tiết ca: {{ $shift->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.shifts.index') }}" class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">Quay lại</a>
            <a href="{{ route('admin.shifts.edit', $shift) }}" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Sửa ca</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="p-4 rounded border">
            <div class="text-gray-600 text-sm">Thời gian</div>
            <div class="text-gray-900 font-medium">{{ $shift->start_time }} - {{ $shift->end_time }}</div>
        </div>
        <div class="p-4 rounded border">
            <div class="text-gray-600 text-sm">Tổng nhân viên</div>
            <div class="text-gray-900 font-medium">{{ $shift->employees->count() }}</div>
        </div>
    </div>

    <h3 class="text-lg font-semibold mb-3">Nhân viên trong ca</h3>
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Tên</th>
                    <th class="p-3 text-left">Chức vụ</th>
                    <th class="p-3 text-left">Trạng thái</th>
                    <th class="p-3 text-left">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shift->employees as $employee)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ optional($employee->user)->name ?? ('#'.$employee->id) }}</td>
                        <td class="p-3">{{ $employee->position ?? '—' }}</td>
                        <td class="p-3">{{ $employee->pivot->status ?? '—' }}</td>
                        <td class="p-3">{{ $employee->pivot->note ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-gray-500">Chưa phân công nhân viên cho ca này</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection