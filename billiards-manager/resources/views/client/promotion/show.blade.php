@extends('layouts.customer')

@section('title', $promotion->name)

@section('content')
<div class="container mx-auto py-10 px-4">
    <a href="{{ route('promotions.index') }}"
       class="inline-flex items-center text-yellow-600 hover:text-yellow-700 font-semibold mb-5">
        ← Quay lại danh sách
    </a>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto">
        @if($promotion->image)
            <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->name }}" class="w-full h-64 object-cover">
        @endif

        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-3">{{ $promotion->name }}</h1>

            <p class="text-sm text-gray-500 mb-4">
                📅 {{ date('d/m/Y', strtotime($promotion->start_date)) }} - {{ date('d/m/Y', strtotime($promotion->end_date)) }}
            </p>

            <div class="border-t border-gray-200 my-4"></div>

            <h2 class="text-xl font-semibold text-yellow-600 mb-2">Mô tả chương trình</h2>
            <p class="text-gray-700 leading-relaxed mb-4">{{ $promotion->description }}</p>

            <h2 class="text-xl font-semibold text-yellow-600 mb-2">Giá trị khuyến mãi</h2>
            <p class="text-gray-800 mb-4">
                @if($promotion->discount_type === 'percent')
                    Giảm <strong>{{ $promotion->discount_value }}%</strong>
                @else
                    Giảm <strong>{{ number_format($promotion->discount_value, 0, ',', '.') }}đ</strong>
                @endif
            </p>

            <h2 class="text-xl font-semibold text-yellow-600 mb-2">Phạm vi áp dụng</h2>
            <p class="text-gray-700">{{ $promotion->scope }}</p>

            <div class="mt-8 text-center">
                <a href="{{ route('reservation.create') }}"
                   class="inline-block bg-yellow-500 text-white px-6 py-3 rounded-full font-medium hover:bg-yellow-600 transition duration-300">
                    Đặt bàn ngay
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
