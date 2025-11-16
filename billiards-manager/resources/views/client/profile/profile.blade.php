@extends('layouts.customer')

@section('content')
<div class="max-w-2xl mx-auto p-6 mt-8 bg-white shadow rounded-lg">

    <h2 class="text-2xl font-semibold mb-4">Thông tin cá nhân</h2>

    @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-2">
        <p><strong>Họ tên:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</p>
        <p><strong>Loại khách hàng:</strong> {{ $user->customer_type ?? 'Chưa xác định' }}</p>
        <p><strong>Tổng lượt ghé:</strong> {{ $user->total_visits }}</p>
        <p><strong>Tổng chi tiêu:</strong> {{ number_format($user->total_spent, 0) }} VNĐ</p>
        <p><strong>Ghi chú:</strong> {{ $user->note ?? 'Không có' }}</p>
        <p><strong>Trạng thái:</strong> {{ $user->status }}</p>
    </div>

    <a href="{{ route('customer.profile') }}/edit"
       class="inline-block mt-5 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Chỉnh sửa thông tin
    </a>
</div>
@endsection
