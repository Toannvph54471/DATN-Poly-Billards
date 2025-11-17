@extends('layouts.customer')

@section('title', 'Chương trình khuyến mãi - Poly Billiards')

@section('styles')
<style>
    .promo-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .promo-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .promo-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }
    .discount-badge {
        background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .promo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-elegant-navy via-elegant-burgundy to-elegant-charcoal text-white py-20 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-elegant-gold opacity-10 rounded-full blur-3xl"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 text-center">
        <div class="mb-8">
            <div class="inline-block bg-elegant-gold/20 rounded-full px-6 py-3 backdrop-blur-sm border border-elegant-gold/50 mb-6">
                <span class="text-elegant-gold font-semibold">
                    <i class="fas fa-star mr-2"></i>Ưu đãi đặc biệt
                </span>
            </div>
        </div>
        <h1 class="text-5xl lg:text-6xl font-display font-bold mb-6">
            Khuyến Mãi 
            <span class="text-elegant-gold">Hấp Dẫn</span>
        </h1>
        <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
            Đừng bỏ lỡ các chương trình ưu đãi đặc biệt dành riêng cho bạn. 
            Tiết kiệm chi phí khi đặt bàn tại Poly Billiards!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('reservation.create') }}" 
               class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-calendar-check mr-2"></i>
                Đặt bàn ngay
            </a>
            <a href="#promotions" 
               class="border-2 border-white text-white hover:bg-white hover:text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300">
                <i class="fas fa-arrow-down mr-2"></i>
                Xem ưu đãi
            </a>
        </div>
    </div>
</section>

<!-- Promotions Grid -->
<section id="promotions" class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        @if($promotions->isEmpty())
            <div class="text-center py-20">
                <div class="inline-block bg-white rounded-full p-8 shadow-xl mb-6">
                    <i class="fas fa-gift text-gray-400 text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Chưa có khuyến mãi</h3>
                <p class="text-gray-600 mb-8">Hiện tại chưa có chương trình khuyến mãi nào. Vui lòng quay lại sau!</p>
                <a href="{{ route('home') }}" 
                   class="inline-block bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300">
                    <i class="fas fa-home mr-2"></i>
                    Về trang chủ
                </a>
            </div>
        @else
            <!-- Stats Bar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-elegant-gold rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-elegant-navy mb-2">{{ $promotions->count() }}</h3>
                    <p class="text-gray-600">Chương trình đang có</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-elegant-burgundy rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-percent text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-elegant-navy mb-2">{{ $promotions->where('discount_type', 'percent')->max('discount_value') }}%</h3>
                    <p class="text-gray-600">Giảm giá tối đa</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-elegant-navy mb-2">24/7</h3>
                    <p class="text-gray-600">Áp dụng mọi lúc</p>
                </div>
            </div>

            <!-- Promotions Grid -->
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($promotions as $promotion)
                <div class="promo-card bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Image -->
                    <div class="relative h-56">
                        @if($promotion->image)
                            <img src="{{ asset('storage/' . $promotion->image) }}" 
                                 alt="{{ $promotion->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-elegant-gold to-yellow-500 flex items-center justify-center">
                                <i class="fas fa-gift text-white text-6xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="promo-overlay"></div>
                        
                        <!-- Discount Badge -->
                        <div class="promo-badge">
                            <div class="discount-badge text-elegant-navy font-bold px-4 py-2 rounded-full shadow-xl">
                                @if($promotion->discount_type === 'percent')
                                    <span class="text-2xl">{{ $promotion->discount_value }}%</span>
                                @else
                                    <span class="text-lg">{{ number_format($promotion->discount_value, 0, ',', '.') }}đ</span>
                                @endif
                            </div>
                        </div>

                        <!-- Days Left Badge -->
                        @php
                        $daysLeft = \Carbon\Carbon::parse($promotion->end_date)->diffInDays(now());
                        @endphp
                        @if($daysLeft <= 3)
                            <div class="absolute top-5 left-5 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">
                                <i class="fas fa-fire mr-1"></i>
                                Còn {{ $daysLeft }} ngày
                            </div>
                        @endif

                        <!-- Title Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold text-xl mb-1 drop-shadow-lg">
                                {{ $promotion->name }}
                            </h3>
                            <p class="text-gray-200 text-sm">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ date('d/m/Y', strtotime($promotion->start_date)) }} - {{ date('d/m/Y', strtotime($promotion->end_date)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                            {{ Str::limit($promotion->description, 120) }}
                        </p>

                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block mb-1">Phạm vi áp dụng</span>
                                <span class="text-sm font-semibold text-elegant-navy">
                                    <i class="fas fa-map-marker-alt text-elegant-gold mr-1"></i>
                                    {{ $promotion->scope }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('promotions.show', $promotion->id) }}"
                               class="flex-1 bg-elegant-navy hover:bg-opacity-90 text-white font-semibold py-3 px-4 rounded-xl transition duration-300 text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Chi tiết
                            </a>
                            <a href="{{ route('reservation.create') }}"
                               class="flex-1 bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold py-3 px-4 rounded-xl transition duration-300 text-center">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Đặt ngay
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-elegant-navy text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="inline-block bg-elegant-gold/20 rounded-full px-6 py-3 backdrop-blur-sm border border-elegant-gold/50 mb-6">
            <span class="text-elegant-gold font-semibold">
                <i class="fas fa-crown mr-2"></i>Ưu đãi VIP
            </span>
        </div>
        <h2 class="text-4xl font-display font-bold mb-6">
            Trở thành thành viên để nhận nhiều ưu đãi hơn
        </h2>
        <p class="text-xl text-gray-300 mb-8">
            Đăng ký thành viên ngay hôm nay để tích điểm và nhận các ưu đãi đặc biệt dành riêng cho bạn!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" 
                   class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Đăng ký ngay
                </a>
            @else
                <a href="{{ route('reservation.create') }}" 
                   class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-gift mr-2"></i>
                    Sử dụng ưu đãi
                </a>
            @endguest
            <a href="{{ route('contact') }}" 
               class="border-2 border-white text-white hover:bg-white hover:text-elegant-navy font-bold px-8 py-4 rounded-full transition duration-300">
                <i class="fas fa-phone mr-2"></i>
                Liên hệ tư vấn
            </a>
        </div>
    </div>
</section>
@endsection