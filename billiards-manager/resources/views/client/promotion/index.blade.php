@extends('layouts.customer')

@section('title', 'Chương trình khuyến mãi')

@section('content')
<div class="container mx-auto py-10 px-4">
    <h2 class="text-3xl font-bold text-center text-yellow-500 mb-8">
        🎉 Chương trình khuyến mãi tại Poly Billiards
    </h2>

    @if($promotions->isEmpty())
        <p class="text-center text-gray-500">Hiện chưa có chương trình khuyến mãi nào.</p>
    @else
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($promotions as $promotion)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-[1.03] hover:shadow-2xl">
                    @if($promotion->image)
                        <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-yellow-300 to-yellow-500 flex items-center justify-center">
                            <span class="text-white text-xl font-semibold">Poly Billiards</span>
                        </div>
                    @endif
                    <div class="p-5 flex flex-col h-full">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            {{ $promotion->name }}
                        </h3>

                        <p class="text-sm text-gray-500 mb-2">
                            📅 {{ date('d/m/Y', strtotime($promotion->start_date)) }} - {{ date('d/m/Y', strtotime($promotion->end_date)) }}
                        </p>

                        <p class="text-gray-600 mb-3 line-clamp-3">
                            {{ Str::limit($promotion->description, 120) }}
                        </p>

                        <p class="text-sm text-gray-500 mb-4">
                            <strong>Phạm vi áp dụng:</strong> {{ $promotion->scope }}
                        </p>

                        <div class="text-sm text-gray-500 mb-4">
                            <a href="{{ route('promotions.show', $promotion->id) }}"
                               class="inline-block bg-yellow-500 text-white font-medium py-2 px-4 rounded-full hover:bg-yellow-600 transition duration-200">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @php
                $daysLeft = \Carbon\Carbon::parse($promotion->end_date)->diffInDays(now());
                @endphp
                @if($daysLeft < 3)
                   <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Sắp kết thúc</span>
                @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
