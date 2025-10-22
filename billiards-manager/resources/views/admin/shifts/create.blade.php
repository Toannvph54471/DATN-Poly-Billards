@extends('admin.layouts.app')

@section('title', 'Thêm ca làm')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Thêm ca làm mới</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium mb-1">Tên ca</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full border border-gray-300 rounded px-3 py-2"
                   placeholder="Nhập tên ca (VD: Ca sáng)">
        </div>

        <div>
            <label class="block font-medium mb-1">Giờ bắt đầu</label>
            <input 
                type="text" 
                name="start_time" 
                id="start_time"
                value="{{ old('start_time') }}" 
                class="w-full border border-gray-300 rounded px-3 py-2"
                placeholder="Chọn giờ bắt đầu (HH:mm)"
            >
        </div>

        <div>
            <label class="block font-medium mb-1">Giờ kết thúc</label>
            <input 
                type="text" 
                name="end_time" 
                id="end_time"
                value="{{ old('end_time') }}" 
                class="w-full border border-gray-300 rounded px-3 py-2"
                placeholder="Chọn giờ kết thúc (HH:mm)"
            >
        </div>

        <div class="flex justify-between mt-4">
            <a href="{{ route('admin.shifts.index') }}" class="text-gray-600 hover:underline">← Quay lại</a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Thêm ca làm
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
flatpickr("#start_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i", // 24h format
    time_24hr: true,
});

flatpickr("#end_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});
</script>
@endsection
