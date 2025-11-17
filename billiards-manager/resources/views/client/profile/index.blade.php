@extends('layouts.customer')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-8 border border-gray-100">

    {{-- Tiêu đề --}}
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-yellow-500 pl-4">
        Thông tin người dùng
    </h2>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card Thông Tin --}}
    <div class="flex items-center gap-6">

        {{-- Avatar
        <div class="flex-shrink-0">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                 class="w-24 h-24 rounded-full object-cover border-4 border-yellow-500 shadow-md">
        </div> --}}

        {{-- Thông tin --}}
        <div class="flex-1 text-gray-700 space-y-2">
            <p class="text-lg"><span class="font-semibold text-gray-900">Họ tên:</span> {{ $user->name }}</p>
            <p class="text-lg"><span class="font-semibold text-gray-900">Email:</span> {{ $user->email }}</p>
            <p class="text-lg"><span class="font-semibold text-gray-900">Số điện thoại:</span> {{ $user->phone ?? 'Chưa cập nhật' }}</p>
        </div>

    </div>

    {{-- Nút chỉnh sửa --}}
    <div class="mt-8">
        <a href="{{ route('client.profile.edit') }}"
           class="px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:bg-blue-800 transition">
            Chỉnh sửa thông tin
        </a>
    </div>

</div>
@endsection
