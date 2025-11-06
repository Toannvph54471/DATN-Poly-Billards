@extends('layouts.customer')

@section('title', 'Poly Billiards - Trang chủ')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-elegant-navy via-elegant-charcoal to-elegant-burgundy text-white py-24 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="absolute top-10 right-10 w-64 h-64 bg-elegant-gold opacity-10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-80 h-80 bg-primary-600 opacity-10 rounded-full blur-3xl"></div>
    
    <div class="relative max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center">
            <div class="lg:w-1/2 mb-12 lg:mb-0 text-center lg:text-left">
                <h1 class="text-5xl lg:text-6xl font-display font-bold mb-6 leading-tight">
                    Poly 
                    <span class="text-elegant-gold">Billiards</span>
                </h1>
                <p class="text-xl mb-8 text-primary-200 leading-relaxed">
                    Trải nghiệm bi-a đẳng cấp trong không gian sang trọng. 
                    Nơi hội tụ của những tay cơ thực thụ.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#" 
                       class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-4 rounded-lg text-lg transition duration-200 transform hover:scale-105 shadow-lg flex items-center justify-center">
                        <i class="fas fa-calendar-plus mr-3 text-xl"></i>
                        Đặt bàn ngay
                    </a>
                    <a href="#" 
                       class="border-2 border-elegant-gold text-elegant-gold hover:bg-elegant-gold hover:text-elegant-navy font-semibold px-8 py-4 rounded-lg text-lg transition duration-200 transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-tag mr-3 text-xl"></i>
                        Khuyến mãi
                    </a>
                </div>
            </div>
            <div class="lg:w-1/2 flex justify-center">
                <div class="relative">
                    <div class="w-80 h-80 bg-elegant-gold rounded-full opacity-20 animate-pulse"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                        <i class="fas fa-billiard-ball text-elegant-gold text-9xl mb-4"></i>
                        <p class="text-elegant-gold font-semibold text-lg">Chuyên nghiệp - Sang trọng - Đẳng cấp</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-display font-bold text-elegant-navy mb-4">Tại sao chọn Poly Billiards?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Chúng tôi mang đến trải nghiệm bi-a hoàn hảo với những ưu điểm vượt trội</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-elegant-cream to-white shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-20 h-20 bg-elegant-gold rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trophy text-elegant-navy text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-elegant-navy mb-4">Bàn Bi-a Chuyên Nghiệp</h3>
                <p class="text-gray-600 leading-relaxed">Hệ thống bàn bi-a tournament tiêu chuẩn quốc tế, được bảo trì định kỳ</p>
            </div>
            
            <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-elegant-cream to-white shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-20 h-20 bg-elegant-burgundy rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-couch text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-elegant-navy mb-4">Không Gian Sang Trọng</h3>
                <p class="text-gray-600 leading-relaxed">Thiết kế hiện đại, ánh sáng chuẩn, âm thanh cao cấp và view thành phố</p>
            </div>
            
            <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-elegant-cream to-white shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-20 h-20 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-concierge-bell text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-elegant-navy mb-4">Dịch Vụ 5 Sao</h3>
                <p class="text-gray-600 leading-relaxed">Đồ uống cao cấp, ăn nhẹ đa dạng, phục vụ tận bàn 24/7</p>
            </div>
        </div>
    </div>
</section>

<!-- Quick Navigation -->
<section class="py-20 bg-gradient-to-br from-elegant-cream to-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl font-display font-bold text-center text-elegant-navy mb-16">Dịch Vụ Của Chúng Tôi</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{route('reservation.create')}}" 
               class="group bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-elegant-gold">
                <div class="w-20 h-20 bg-gradient-to-br from-elegant-gold to-yellow-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-calendar-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-elegant-navy mb-3">Đặt Bàn</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Đặt trước bàn bi-a để đảm bảo chỗ chơi theo ý muốn</p>
            </a>
            
            <a href="{{route('reservations.index')}}" 
               class="group bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-primary-500">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-search text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-elegant-navy mb-3">Tra Cứu</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Kiểm tra trạng thái và thông tin đặt bàn của bạn</p>
            </a>
            
            <a href="{{route('promotions.index')}}" 
               class="group bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-elegant-burgundy">
                <div class="w-20 h-20 bg-gradient-to-br from-elegant-burgundy to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-percentage text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-elegant-navy mb-3">Khuyến Mãi</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Khám phá các ưu đãi đặc biệt dành riêng cho bạn</p>
            </a>
            
            <a href="{{ route('contact') }}" 
               class="group bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-green-500">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-phone text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-elegant-navy mb-3">Liên Hệ</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Đặt câu hỏi và nhận tư vấn từ đội ngũ chuyên nghiệp</p>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-elegant-navy text-white">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-display font-bold mb-6">Sẵn sàng trải nghiệm?</h2>
        <p class="text-xl text-primary-200 mb-8 max-w-2xl mx-auto">
            Tham gia cộng đồng những người yêu thích bi-a tại Poly Billiards. Đẳng cấp, chuyên nghiệp và đầy cảm hứng.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" 
               class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-4 rounded-lg text-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-play mr-2"></i>Bắt đầu ngay
            </a>
            <a href="{{ route('contact') }}" 
               class="border-2 border-elegant-gold text-elegant-gold hover:bg-elegant-gold hover:text-elegant-navy font-semibold px-8 py-4 rounded-lg text-lg transition duration-200">
                <i class="fas fa-info-circle mr-2"></i>Tìm hiểu thêm
            </a>
        </div>
    </div>
</section>
@endsection