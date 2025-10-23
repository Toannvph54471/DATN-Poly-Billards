@extends('admin.layouts.app')

@section('title', 'Sản phẩm đã xóa')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Sản phẩm đã xóa</h1>
    <a href="{{ route('admin.products.index') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
        <i class="fas fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="py-3 px-6 text-left">#</th>
            <th class="py-3 px-6 text-left">Tên sản phẩm</th>
            <th class="py-3 px-6 text-left">Giá</th>
            <th class="py-3 px-6 text-left">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $product)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-4 px-6">{{ $loop->iteration }}</td>
                <td class="py-4 px-6 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="py-4 px-6 text-gray-700">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                <td class="py-4 px-6">
                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này không?');">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-900 transition">
                            <i class="fas fa-undo"></i> Khôi phục
                        </button>
                    </form>
                    <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm này không? Hành động này không thể hoàn tác.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 transition">
                            <i class="fas fa-trash-alt"></i> Xóa vĩnh viễn
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-4 px-6 text-center text-gray-500">Không có sản phẩm nào bị xóa.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
