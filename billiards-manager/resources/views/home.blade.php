@extends('client.layouts.app')

@section('title', 'Trang chủ - Billiard Club')

@section('content')

<!-- Hero Section -->
<section id="home" class="relative h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg width=&quot;60&quot; height=&quot;60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;2&quot; fill=&quot;%23d97706&quot;/></svg>'); background-size: 60px 60px;"></div>
    
    <div class="relative z-10 container mx-auto px-4 text-center">
        <div class="text-6xl mb-6">🎱</div>
        <h1 class="text-5xl md:text-7xl font-bold text-gray-800 mb-6">
            Chào mừng đến <span class="text-amber-600">Billiard Club</span>
        </h1>
        <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Trải nghiệm những bàn bi-a đẳng cấp và không gian sang trọng bậc nhất thành phố
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#contact" class="bg-amber-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-amber-700 transition transform hover:scale-105 shadow-lg">
                Đặt bàn ngay
            </a>
            <a href="#about" class="border-2 border-amber-600 text-amber-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-amber-600 hover:text-white transition transform hover:scale-105">
                Tìm hiểu thêm
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                    Về <span class="text-amber-600">Câu lạc bộ</span>
                </h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Chào mừng đến với câu lạc bộ bi-a hàng đầu, nơi đam mê gặp gỡ sự xuất sắc. Hơn 15 năm qua, chúng tôi đã mang đến cho những người đam mê môi trường hoàn hảo để tận hưởng trò chơi yêu thích của họ.
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Cơ sở vật chất hiện đại của chúng tôi có các bàn bi-a chuyên nghiệp, ánh sáng môi trường và bầu không khí chào đón khiến mỗi lần ghé thăm đều đáng nhớ.
                </p>
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-amber-50 p-6 rounded-lg border-2 border-amber-200">
                        <div class="text-4xl font-bold text-amber-600 mb-2">15+</div>
                        <div class="text-gray-600 font-medium">Năm kinh nghiệm</div>
                    </div>
                    <div class="bg-amber-50 p-6 rounded-lg border-2 border-amber-200">
                        <div class="text-4xl font-bold text-amber-600 mb-2">20+</div>
                        <div class="text-gray-600 font-medium">Bàn cao cấp</div>
                    </div>
                </div>
            </div>
            <div class="relative h-96 bg-gradient-to-br from-amber-100 to-orange-100 rounded-lg overflow-hidden shadow-xl">
                <div class="absolute inset-0 flex items-center justify-center text-amber-600">
                    <div class="text-center">
                        <div class="text-8xl mb-4">🎱</div>
                        <div class="text-2xl font-bold text-gray-800">Bàn chuyên nghiệp</div>
                        <div class="text-gray-600 mt-2">Thiết bị chất lượng cao cấp</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Dịch vụ <span class="text-amber-600">của chúng tôi</span>
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Mọi thứ bạn cần cho trải nghiệm bi-a hoàn hảo dưới một mái nhà
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Service 1 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">🎯</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Bàn cao cấp</h3>
                <p class="text-gray-600">Bàn bi-a chuẩn chuyên nghiệp được bảo dưỡng hoàn hảo cho trải nghiệm chơi tối ưu.</p>
            </div>

            <!-- Service 2 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">🏆</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Giải đấu</h3>
                <p class="text-gray-600">Các cuộc thi và sự kiện thường xuyên cho người chơi ở mọi trình độ với giải thưởng hấp dẫn.</p>
            </div>

            <!-- Service 3 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">👨‍🏫</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Huấn luyện</h3>
                <p class="text-gray-600">Giảng viên chuyên nghiệp có sẵn cho các bài học riêng và các buổi đào tạo nhóm.</p>
            </div>

            <!-- Service 4 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">🍺</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Bar & Lounge</h3>
                <p class="text-gray-600">Quầy bar với đồ uống cao cấp và khu vực lounge thoải mái để thư giãn.</p>
            </div>

            <!-- Service 5 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">🎉</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Sự kiện riêng</h3>
                <p class="text-gray-600">Tổ chức tiệc, sự kiện doanh nghiệp hoặc lễ kỷ niệm của bạn tại địa điểm độc quyền.</p>
            </div>

            <!-- Service 6 -->
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-100">
                <div class="text-5xl mb-4">📱</div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Đặt bàn online</h3>
                <p class="text-gray-600">Hệ thống đặt bàn dễ dàng qua website hoặc ứng dụng di động của chúng tôi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Thư viện <span class="text-amber-600">ảnh</span>
            </h2>
            <p class="text-gray-600">Khám phá cơ sở vật chất cao cấp của chúng tôi</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @for($i = 1; $i <= 6; $i++)
            <div class="relative h-64 bg-gradient-to-br from-amber-100 to-orange-100 rounded-lg overflow-hidden group cursor-pointer shadow-lg hover:shadow-xl transition">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-200 to-orange-200 opacity-0 group-hover:opacity-50 transition"></div>
                <div class="absolute inset-0 flex items-center justify-center text-amber-700">
                    <div class="text-center transform group-hover:scale-110 transition">
                        <div class="text-6xl mb-2">🎱</div>
                        <div class="text-lg font-bold text-gray-800">Hình ảnh {{ $i }}</div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Bảng <span class="text-amber-600">giá</span>
            </h2>
            <p class="text-gray-600">Chọn gói phù hợp với nhu cầu bi-a của bạn</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Package 1 -->
            <div class="bg-white rounded-lg p-8 shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Theo giờ</h3>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-amber-600">50,000</span>
                    <span class="text-gray-600 ml-2">VNĐ</span>
                    <div class="text-gray-500 text-sm mt-1">mỗi giờ</div>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Truy cập bàn tiêu chuẩn
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Tiện nghi cơ bản
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Không cần đặt trước
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Thuê cơ miễn phí
                    </li>
                </ul>
                <button class="w-full py-3 rounded-full font-semibold transition bg-gray-100 text-gray-800 hover:bg-gray-200">
                    Chọn gói này
                </button>
            </div>

            <!-- Package 2 - Popular -->
            <div class="bg-white rounded-lg p-8 shadow-lg hover:shadow-xl transition transform hover:scale-105 ring-4 ring-amber-500">
                <div class="bg-amber-500 text-white text-sm font-bold px-4 py-1 rounded-full inline-block mb-4">
                    PHỔ BIẾN NHẤT
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Thẻ tháng</h3>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-amber-600">1,200,000</span>
                    <span class="text-gray-600 ml-2">VNĐ</span>
                    <div class="text-gray-500 text-sm mt-1">mỗi tháng</div>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Truy cập không giới hạn
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Ưu tiên đặt bàn
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Giảm 10% tại bar
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Buổi huấn luyện miễn phí
                    </li>
                </ul>
                <button class="w-full py-3 rounded-full font-semibold transition bg-amber-600 text-white hover:bg-amber-700 shadow-lg">
                    Chọn gói này
                </button>
            </div>

            <!-- Package 3 -->
            <div class="bg-white rounded-lg p-8 shadow-lg hover:shadow-xl transition transform hover:scale-105 border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">VIP năm</h3>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-amber-600">10,000,000</span>
                    <span class="text-gray-600 ml-2">VNĐ</span>
                    <div class="text-gray-500 text-sm mt-1">mỗi năm</div>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Truy cập VIP không giới hạn
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Phòng lounge riêng
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Giảm 20% mọi dịch vụ
                    </li>
                    <li class="flex items-center text-gray-700">
                        <span class="text-amber-600 mr-2 font-bold">✓</span>
                        Tham gia giải đấu
                    </li>
                </ul>
                <button class="w-full py-3 rounded-full font-semibold transition bg-gray-100 text-gray-800 hover:bg-gray-200">
                    Chọn gói này
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Liên hệ <span class="text-amber-600">với chúng tôi</span>
            </h2>
            <p class="text-gray-600">Chúng tôi rất vui được lắng nghe từ bạn</p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
            <!-- Contact Information -->
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Thông tin liên hệ</h3>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-amber-600 mr-4 mt-1 text-2xl"></i>
                        <div>
                            <div class="text-gray-800 font-semibold mb-1">Địa chỉ</div>
                            <div class="text-gray-600">123 Phố Bi-a, Hà Nội, Việt Nam</div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-phone text-amber-600 mr-4 mt-1 text-2xl"></i>
                        <div>
                            <div class="text-gray-800 font-semibold mb-1">Điện thoại</div>
                            <div class="text-gray-600">+84 123 456 789</div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-envelope text-amber-600 mr-4 mt-1 text-2xl"></i>
                        <div>
                            <div class="text-gray-800 font-semibold mb-1">Email</div>
                            <div class="text-gray-600">info@billiardclub.com</div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-clock text-amber-600 mr-4 mt-1 text-2xl"></i>
                        <div>
                            <div class="text-gray-800 font-semibold mb-1">Giờ mở cửa</div>
                            <div class="text-gray-600">Thứ 2 - Thứ 6: 10:00 - 02:00</div>
                            <div class="text-gray-600">Thứ 7 - CN: 10:00 - 04:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gray-50 p-8 rounded-lg shadow-lg">
                <div class="space-y-4">
                    <div>
                        <input 
                            type="text" 
                            name="name"
                            placeholder="Họ và tên" 
                            class="w-full px-4 py-3 bg-white border-2 border-gray-200 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <div>
                        <input 
                            type="email" 
                            name="email"
                            placeholder="Email của bạn" 
                            class="w-full px-4 py-3 bg-white border-2 border-gray-200 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <div>
                        <input 
                            type="tel" 
                            name="phone"
                            placeholder="Số điện thoại" 
                            class="w-full px-4 py-3 bg-white border-2 border-gray-200 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <div>
                        <textarea 
                            name="message"
                            rows="4" 
                            placeholder="Tin nhắn của bạn" 
                            class="w-full px-4 py-3 bg-white border-2 border-gray-200 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            required
                        ></textarea>
                    </div>
                    <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-semibold hover:bg-amber-700 transition shadow-lg">
                        Gửi tin nhắn
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection