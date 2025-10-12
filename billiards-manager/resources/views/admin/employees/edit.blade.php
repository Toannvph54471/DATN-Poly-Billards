@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa nhân viên')

@section('content')
<h1 class="text-2xl font-bold mb-6">Chỉnh sửa nhân viên</h1>

<form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" class="bg-white shadow rounded-lg p-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Chọn người dùng</label>
            <select name="user_id" class="w-full border rounded-lg px-3 py-2">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $employee->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Chức vụ</label>
            <input type="text" name="position" value="{{ $employee->position }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Lương/giờ</label>
            <input type="number" step="0.01" name="salary_rate" value="{{ $employee->salary_rate }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Ngày vào</label>
            <input type="date" name="hire_date" value="{{ $employee->hire_date }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Trạng thái</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2">
                <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $employee->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('admin.employees.index') }}" class="ml-2 text-gray-600">Quay lại</a>
    </div>
</form>
@endsection
