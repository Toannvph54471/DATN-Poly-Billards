@extends('layouts.admin')

@section('title', 'Thêm ca làm việc')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Thêm ca làm việc</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.shifts.store') }}" class="bg-white shadow p-6 rounded">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1">Mã ca *</label>
                <input type="text" name="shift_code" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block mb-1">Tên ca *</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block mb-1">Giờ bắt đầu *</label>
                <input type="time" name="start_time" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block mb-1">Giờ kết thúc *</label>
                <input type="time" name="end_time" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block mb-1">Màu hiển thị</label>
                <input type="text" name="color" class="w-full border rounded p-2" placeholder="#16a34a hoặc tên màu">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-1">Ghi chú</label>
                <textarea name="description" rows="3" class="w-full border rounded p-2"></textarea>
            </div>
        </div>

        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">Lưu ca</button>
        </div>
    </form>
</div>
@endsection
