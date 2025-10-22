@extends('admin.layouts.app')

@section('title', 'Quản lý ca làm')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Danh sách ca làm</h2>
        <a href="{{ route('admin.shifts.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            <i class="fa fa-plus mr-1"></i> Thêm ca làm
        </a>
    </div>

    <table class="w-full border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Tên ca</th>
                <th class="p-3 text-left">Giờ bắt đầu</th>
                <th class="p-3 text-left">Giờ kết thúc</th>
                <th class="p-3 text-left">Nhân viên trong ca</th>
                <th class="p-3 text-left">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shifts as $shift)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">{{ $shift->name }}</td>
                  <td class="p-3">
    {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
</td>
<td class="p-3">
    {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
</td>

                    <td class="p-3">
                        @php
                            $employeeNames = $shift->employees->map(function($emp){
                                return optional($emp->user)->name ?? ("#".$emp->id);
                            });
                        @endphp
                        @if($employeeNames->isEmpty())
                            <span class="text-gray-500">Chưa phân công</span>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($employeeNames as $name)
                                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-sm">{{ $name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="p-3">
                        <a href="{{ route('admin.shifts.show', $shift) }}" class="text-indigo-600 hover:underline mr-2">Chi tiết</a> |
                        <a href="{{ route('admin.shifts.edit', $shift) }}" class="text-blue-600 hover:underline">Sửa</a> |
                        <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Xóa ca này?')" class="text-red-600 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-3 text-center text-gray-500">Chưa có ca làm nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
