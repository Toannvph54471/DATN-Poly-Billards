@extends('layouts.customer')

@section('title', $promotion->name . ' - Poly Billiards')

@section('styles')
<style>
    .feature-item {
        transition: all 0.3s ease;
    }
    .feature-item:hover {
        transform: translateX(10px);
        color: #d4af37;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<section class="bg-elegant-navy text-white py-6">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('home') }}" class="hover:text-elegant-gold transition">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('promotions.index') }}" class="hover:text-elegant-gold transition">
                Khuyến mãi
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-elegant-gold">{{ $promotion->name }}</span>
        </nav>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Hero Image -->
            <div class="relative h-96">
                @if($promotion->image)
                    <img src="{{ asset('storage/' . $promotion->image) }}" 
                         alt="{{ $promotion->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-elegant-gold via-yellow-500 to-elegant-burgundy flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-gift text-8xl mb-4 opacity-50"></i>
                            <p class="text-2xl font-bold">Poly Billiards</p>
                        </div>
                    </div>
                @endif
                
                <!-- Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                
                <!-- Discount Badge -->
                <div class="absolute top-8 right-8">
                    <div class="bg-gradient-to-br from-elegant-gold to-yellow-500 text-elegant-navy font-bold px-6 py-4 rounded-2xl shadow-2xl transform rotate-3 hover:rotate-0 transition duration-300">
                        @if($promotion->discount_type === 'percent')
                            <div class="text-4xl">{{ $promotion->discount_value }}%</div>
                            <div class="text-sm">GIẢM GIÁ</div>
                        @else
                            <div class="text-2xl">{{ number_format($promotion->discount_value, 0, ',', '.') }}đ</div>
                            <div class="text-sm">GIẢM TRỰC TIẾP</div>
                        @endif
                    </div>
                </div>

                <!-- Title Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                    <h1 class="text-4xl font-display font-bold mb-4 drop-shadow-lg">
                        {{ $promotion->name }}
                    </h1>
                    <div class="flex items-center space-x-6 text-lg">
                        <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                            <i class="fas fa-calendar-alt text-elegant-gold mr-2"></i>
                            <span>{{ date('d/m/Y', strtotime($promotion->start_date)) }}</span>
                        </div>
                        <span class="text-elegant-gold">→</span>
                        <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                            <i class="fas fa-calendar-check text-elegant-gold mr-2"></i>
                            <span>{{ date('d/m/Y', strtotime($promotion->end_date)) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Days Left Badge -->
                @php
                $daysLeft = \Carbon\Carbon::parse($promotion->end_date)->diffInDays(now());
                @endphp
                @if($daysLeft <= 7)
                    <div class="absolute top-8 left-8 bg-red-500 text-white px-6 py-3 rounded-full font-bold animate-pulse shadow-xl">
                        <i class="fas fa-fire mr-2"></i>
                        Còn {{ $daysLeft }} ngày
                    </div>
                @endif
            </div>

            <!-- Content -->
            <div class="p-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Description -->
                        <div>
                            <h2 class="text-2xl font-bold text-elegant-navy mb-4 flex items-center">
                                <div class="w-12 h-12 bg-elegant-gold rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-info-circle text-white"></i>
                                </div>
                                Mô tả chương trình
                            </h2>
                            <div class="bg-gray-50 rounded-xl p-6">
                                <p class="text-gray-700 leading-relaxed text-lg">
                                    {{ $promotion->description }}
                                </p>
                            </div>
                        </div>

                        <!-- Discount Details -->
                        <div>
                            <h2 class="text-2xl font-bold text-elegant-navy mb-4 flex items-center">
                                <div class="w-12 h-12 bg-elegant-burgundy rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-percentage text-white"></i>
                                </div>
                                Giá trị ưu đãi
                            </h2>
                            <div class="bg-gradient-to-br from-elegant-gold/10 to-yellow-100 rounded-xl p-8 border-2 border-elegant-gold">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-600 mb-2">Mức giảm</p>
                                        <p class="text-4xl font-bold text-elegant-navy">
                                            @if($promotion->discount_type === 'percent')
                                                {{ $promotion->discount_value }}%
                                            @else
                                                {{ number_format($promotion->discount_value, 0, ',', '.') }}đ
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-6xl text-elegant-gold opacity-20">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scope -->
                        <div>
                            <h2 class="text-2xl font-bold text-elegant-navy mb-4 flex items-center">
                                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-map-marked-alt text-white"></i>
                                </div>
                                Phạm vi áp dụng
                            </h2>
                            <div class="bg-gray-50 rounded-xl p-6">
                                <p class="text-gray-700 text-lg flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    {{ $promotion->scope }}
                                </p>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div>
                            <h2 class="text-2xl font-bold text-elegant-navy mb-4 flex items-center">
                                <div class="w-12 h-12 bg-gray-600 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-file-contract text-white"></i>
                                </div>
                                Điều kiện & điều khoản
                            </h2>
                            <div class="bg-gray-50 rounded-xl p-6 space-y-3">
                                <div class="feature-item flex items-start">
                                    <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                    <p class="text-gray-700">Áp dụng cho tất cả khách hàng</p>
                                </div>
                                <div class="feature-item flex items-start">
                                    <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                    <p class="text-gray-700">Không áp dụng đồng thời với các chương trình khác</p>
                                </div>
                                <div class="feature-item flex items-start">
                                    <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                    <p class="text-gray-700">Chương trình có thể kết thúc sớm nếu hết ngân sách</p>
                                </div>
                                <div class="feature-item flex items-start">
                                    <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                                    <p class="text-gray-700">Poly Billiards có quyền thay đổi điều khoản mà không báo trước</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-4 space-y-6">
                            <!-- CTA Card -->
                            <div class="bg-gradient-to-br from-elegant-navy to-elegant-charcoal rounded-2xl p-8 text-white shadow-2xl">
                                <div class="text-center mb-6">
                                    <div class="inline-block bg-elegant-gold/20 rounded-full px-4 py-2 mb-4">
                                        <i class="fas fa-bolt text-elegant-gold mr-2"></i>
                                        <span class="text-sm font-semibold">Đặt ngay hôm nay!</span>
                                    </div>
                                    <h3 class="text-2xl font-bold mb-2">Nhận ưu đãi ngay</h3>
                                    <p class="text-gray-300 text-sm">Tiết kiệm lên đến 
                                        <span class="text-elegant-gold font-bold">
                                            @if($promotion->discount_type === 'percent')
                                                {{ $promotion->discount_value }}%
                                            @else
                                                {{ number_format($promotion->discount_value, 0, ',', '.') }}đ
                                            @endif
                                        </span>
                                    </p>
                                </div>
                                
                                <a href="{{ route('reservation.create') }}" 
                                   class="block w-full bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-bold py-4 px-6 rounded-xl transition duration-300 text-center transform hover:scale-105 shadow-lg mb-4">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Đặt bàn ngay
                                </a>

                                <a href="{{ route('promotions.index') }}" 
                                   class="block w-full border-2 border-white text-white hover:bg-white hover:text-elegant-navy font-semibold py-4 px-6 rounded-xl transition duration-300 text-center">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Xem ưu đãi khác
                                </a>
                            </div>

                            <!-- Share Card -->
                            <div class="bg-white rounded-2xl p-6 shadow-xl">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-share-alt text-elegant-gold mr-2"></i>
                                    Chia sẻ ưu đãi
                                </h4>
                                <div class="flex space-x-3">
                                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition duration-300">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <button class="flex-1 bg-blue-400 hover:bg-blue-500 text-white py-3 rounded-lg transition duration-300">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                    <button class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg transition duration-300">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg transition duration-300">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Contact Card -->
                            <div class="bg-gradient-to-br from-elegant-cream to-white rounded-2xl p-6 shadow-xl border-2 border-elegant-gold/20">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-headset text-elegant-gold mr-2"></i>
                                    Cần hỗ trợ?
                                </h4>
                                <p class="text-gray-600 text-sm mb-4">Liên hệ với chúng tôi để được tư vấn chi tiết</p>
                                <a href="{{ route('contact') }}" 
                                   class="block w-full bg-elegant-navy hover:bg-opacity-90 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 text-center">
                                    <i class="fas fa-phone mr-2"></i>
                                    Liên hệ ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Promotions -->
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-elegant-navy mb-8 text-center">
                Các chương trình khác
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- You can add related promotions here -->
                <a href="{{ route('promotions.index') }}" 
                   class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition duration-300 group">
                    <div class="w-20 h-20 bg-elegant-gold rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 mb-2">Xem tất cả ưu đãi</h3>
                    <p class="text-gray-600 text-sm">Khám phá thêm nhiều chương trình hấp dẫn</p>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection