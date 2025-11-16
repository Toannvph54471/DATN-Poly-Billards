@extends('layouts.customer')

@section('content')
<div class="max-w-2xl mx-auto p-6 mt-8 bg-white shadow rounded-lg">

    <h2 class="text-xl font-semibold mb-4">Chỉnh sửa thông tin</h2>

    <form method="POST" action="{{ route('customer.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" 
                   value="{{ old('name', $user->name) }}"
                   class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" 
                   value="{{ old('email', $user->email) }}"
                   class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="phone" 
                   value="{{ old('phone', $user->phone) }}"
                   class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label>Loại khách hàng</label>
            <input type="text" name="customer_type" 
                   value="{{ old('customer_type', $user->customer_type) }}"
                   class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label>Ghi chú</label>
            <textarea name="note" class="w-full border rounded p-2">{{ old('note', $user->note) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Mật khẩu mới (để trống nếu không đổi)</label>
            <input type="password" name="password" class="w-full border rounded p-2">
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Cập nhật
        </button>
    </form>
</div>
@endsection
