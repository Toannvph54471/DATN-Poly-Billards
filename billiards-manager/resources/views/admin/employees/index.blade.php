@extends('admin.layouts.app')

@section('title', 'Danh sách nhân viên')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Danh sách nhân viên</h1>
    <a href="{{ route('admin.employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Thêm nhân viên
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="bg-white shadow rounded-lg p-4">
    <table class="min-w-full border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Tên</th>
                <th class="px-4 py-2 border">Chức vụ</th>
                <th class="px-4 py-2 border">Lương/giờ</th>
                <th class="px-4 py-2 border">Ngày vào</th>
                <th class="px-4 py-2 border">Trạng thái</th>
                <th class="px-4 py-2 border">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $key => $employee)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2">{{ $key + 1 }}</td>
                <td class="px-4 py-2">{{ $employee->user->name ?? 'N/A' }}</td>
                <td class="px-4 py-2">{{ $employee->position }}</td>
                <td class="px-4 py-2">{{ number_format($employee->salary_rate, 0) }} đ</td>
                <td class="px-4 py-2">{{ $employee->hire_date }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded-full {{ $employee->status == 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $employee->status }}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-600 hover:text-blue-800 mr-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
