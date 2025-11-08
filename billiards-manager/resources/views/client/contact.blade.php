@extends('layouts.customer')

@section('title', 'Liên hệ - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-display font-bold text-elegant-navy mb-4">Liên hệ chúng tôi</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ với chúng tôi theo thông tin dưới đây.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <!-- Contact Information -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6">Thông tin liên hệ</h2>
                    
                    <div class="space-y-6">
                        <!-- Address -->
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-elegant-gold rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-elegant-navy text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Địa chỉ</h3>
                                <p class="text-gray-600">123 Đường ABC, Quận 1, TP.HCM</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-elegant-gold rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-elegant-navy text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Điện thoại</h3>
                                <p class="text-gray-600">(028) 1234 5678</p>
                                <p class="text-gray-600">(028) 8765 4321</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-elegant-gold rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-elegant-navy text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Email</h3>
                                <p class="text-gray-600">info@polybilliards.com</p>
                                <p class="text-gray-600">support@polybilliards.com</p>
                            </div>
                        </div>

                        <!-- Hours -->
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-elegant-gold rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-elegant-navy text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Giờ mở cửa</h3>
                                <div class="space-y-1">
                                    <p class="text-gray-600 flex justify-between">
                                        <span>Thứ 2 - Thứ 6:</span>
                                        <span class="font-medium">8:00 - 24:00</span>
                                    </p>
                                    <p class="text-gray-600 flex justify-between">
                                        <span>Thứ 7 - Chủ nhật:</span>
                                        <span class="font-medium">24/24</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kết nối với chúng tôi</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-elegant-navy hover:bg-elegant-burgundy rounded-full flex items-center justify-center transition duration-200 transform hover:scale-110">
                                <i class="fab fa-facebook-f text-white text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-elegant-navy hover:bg-elegant-burgundy rounded-full flex items-center justify-center transition duration-200 transform hover:scale-110">
                                <i class="fab fa-tiktok text-white text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-elegant-navy hover:bg-elegant-burgundy rounded-full flex items-center justify-center transition duration-200 transform hover:scale-110">
                                <i class="fab fa-youtube text-white text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-elegant-navy hover:bg-elegant-burgundy rounded-full flex items-center justify-center transition duration-200 transform hover:scale-110">
                                <i class="fab fa-zalo text-white text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form & Map -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6">Gửi tin nhắn cho chúng tôi</h2>
                    <form id="contactForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       placeholder="Nhập họ tên của bạn">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại *</label>
                                <input type="tel" id="phone" name="phone" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       placeholder="Nhập số điện thoại">
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       placeholder="Nhập email của bạn">
                            </div>

                            <div class="md:col-span-2">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Chủ đề *</label>
                                <select id="subject" name="subject" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200">
                                    <option value="">Chọn chủ đề</option>
                                    <option value="booking">Đặt bàn & Giá cả</option>
                                    <option value="promotion">Khuyến mãi</option>
                                    <option value="event">Tổ chức sự kiện</option>
                                    <option value="complaint">Khiếu nại dịch vụ</option>
                                    <option value="suggestion">Góp ý</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Nội dung tin nhắn *</label>
                                <textarea id="message" name="message" rows="5" required 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                          placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                            </div>
                        </div>

                        <div>
                            <button type="submit" 
                                    class="w-full bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold py-4 px-6 rounded-lg transition duration-200 transform hover:scale-105 flex items-center justify-center">
                                <i class="fas fa-paper-plane mr-3"></i>
                                Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Map -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="h-80 w-full">
                        <!-- Google Map Embed -->
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.404724283579!2d106.69602631533436!3d10.786392461935132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f36c4b6e79f%3A0x67f17d71c15d1b0c!2sLandmark%2081!5e0!3m2!1sen!2s!4v1647420275568!5m2!1sen!2s" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-b-2xl">
                        </iframe>
                    </div>
                    <div class="p-4 bg-elegant-navy text-white">
                        <p class="text-sm text-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Tìm đường đến Poly Billiards
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Preview -->
        <div class="bg-gradient-to-r from-elegant-navy to-elegant-burgundy rounded-2xl p-8 text-white">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-display font-bold mb-4">Câu hỏi thường gặp</h2>
                <p class="text-lg text-blue-100">Tìm câu trả lời nhanh cho những thắc mắc phổ biến</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-question-circle text-elegant-gold mr-3"></i>
                        Làm thế nào để đặt bàn?
                    </h3>
                    <p class="text-blue-100">Bạn có thể đặt bàn trực tuyến thông qua website, gọi điện thoại hoặc đến trực tiếp quán.</p>
                </div>
                
                <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold mb-3 flex items-center">
                        <i class="fas fa-question-circle text-elegant-gold mr-3"></i>
                        Có chỗ đỗ xe không?
                    </h3>
                    <p class="text-blue-100">Có, chúng tôi có bãi đỗ xe rộng rãi và an toàn cho cả xe máy và ô tô.</p>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <a href="{{ route('faq') }}" 
                   class="inline-block bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-3 rounded-lg transition duration-200 transform hover:scale-105">
                    Xem tất cả câu hỏi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hiển thị thông báo gửi thành công
        alert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
        
        // Reset form
        this.reset();
    });
</script>
@endsection