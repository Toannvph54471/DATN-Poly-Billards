@extends('admin.layouts.app')

@section('title', 'Sửa ca làm')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Sửa ca làm</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-medium mb-1">Tên ca</label>
            <input type="text" name="name" value="{{ old('name', $shift->name) }}" 
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

       <div>
    <label class="block font-medium mb-1">Giờ bắt đầu</label>
    <input id="start_time" type="text" name="start_time"
           value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>

<div>
    <label class="block font-medium mb-1">Giờ kết thúc</label>
    <input id="end_time" type="text" name="end_time"
           value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
           class="w-full border border-gray-300 rounded px-3 py-2">
</div>


        <div class="flex justify-between mt-4">
            <a href="{{ route('admin.shifts.index') }}" class="text-gray-600 hover:underline">← Quay lại</a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#start_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    flatpickr("#end_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
</script>
@endsection
