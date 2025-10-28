@extends('admin.layouts.app')

@section('title', 'Danh sách khuyến mãi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-700">🎁 Danh sách khuyến mãi</h1>
    <a href="{{ route('admin.promotions.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
       + Thêm khuyến mãi
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full table-auto border-collapse">
        <thead class="bg-blue-50 text-gray-700 uppercase text-sm">
            <tr>
                <th class="px-6 py-3 text-left">Mã</th>
                <th class="px-6 py-3 text-left">Tên chương trình</th>
                <th class="px-6 py-3 text-center">Loại giảm</th>
                <th class="px-6 py-3 text-center">Giá trị</th>
                <th class="px-6 py-3 text-center">Tổng bill tối thiểu</th>
                <th class="px-6 py-3 text-center">Thời gian</th>
                <th class="px-6 py-3 text-center">Trạng thái</th>
                <th class="px-6 py-3 text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody class="text-gray-600">
            @forelse($promotions as $promo)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-3 font-semibold">{{ $promo->promotion_code }}</td>
                    <td class="px-6 py-3">{{ $promo->name }}</td>
                    <td class="px-6 py-3 text-center">
                        {{ $promo->discount_type == 'percent' ? 'Phần trăm' : 'Cố định' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        {{ $promo->discount_type == 'percent' ? $promo->discount_value . '%' : number_format($promo->discount_value, 0, ',', '.') . '₫' }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        {{ number_format($promo->min_total_amount, 0, ',', '.') }}₫
                    </td>
                    <td class="px-6 py-3 text-center">
                        {{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        @if($promo->status === 'active')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-medium">Hoạt động</span>
                        @else
                            <span class="px-2 py-1 rounded bg-gray-200 text-gray-600 text-xs font-medium">Ngưng</span>
                        @endif
                    </td>
                    {{-- <td class="px-6 py-3 text-center space-x-2">
                        <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="text-blue-600 hover:underline">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-3 text-center text-gray-500 italic">
                        Chưa có chương trình khuyến mãi nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $promotions->links() }}
</div>
@endsection
