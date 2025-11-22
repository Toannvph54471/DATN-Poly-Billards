@extends('layouts.customer')

@section('title', 'Câu hỏi thường gặp - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-display font-bold text-elegant-navy mb-4">Câu hỏi thường gặp</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Tìm câu trả lời cho những thắc mắc phổ biến về dịch vụ của chúng tôi.</p>
        </div>

        <!-- Search Bar -->
        <div class="mb-12">
            <div class="relative max-w-2xl mx-auto">
                <input type="text" 
                       id="faqSearch" 
                       placeholder="Tìm kiếm câu hỏi..."
                       class="w-full px-6 py-4 pl-12 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2 justify-center">
                <button class="faq-category px-4 py-2 bg-elegant-gold text-elegant-navy font-semibold rounded-lg transition duration-200" data-category="all">
                    Tất cả
                </button>
                <button class="faq-category px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-category="booking">
                    Đặt bàn
                </button>
                <button class="faq-category px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-category="pricing">
                    Giá cả
                </button>
                <button class="faq-category px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-category="services">
                    Dịch vụ
                </button>
                <button class="faq-category px-4 py-2 bg-gray-200 text-gray-700 hover:bg-elegant-gold hover:text-elegant-navy font-semibold rounded-lg transition duration-200" data-category="facilities">
                    Cơ sở vật chất
                </button>
            </div>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-6">
            <!-- Booking FAQs -->
            <div class="faq-section" data-category="booking">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6 flex items-center">
                        <i class="fas fa-calendar-check text-elegant-gold mr-3"></i>
                        Đặt bàn & Đặt chỗ
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Làm thế nào để đặt bàn tại Poly Billiards?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600 mb-3">Có 3 cách để đặt bàn:</p>
                                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                                    <li>Đặt trực tuyến thông qua website của chúng tôi</li>
                                    <li>Gọi điện thoại đến số (028) 1234 5678</li>
                                    <li>Đến trực tiếp quán và đặt tại quầy lễ tân</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Tôi có thể đặt bàn trước bao lâu?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600">Bạn có thể đặt bàn trước tối đa 30 ngày. Chúng tôi khuyến nghị đặt trước ít nhất 2-3 ngày cho cuối tuần và các khung giờ cao điểm.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Có thể hủy hoặc thay đổi đặt bàn không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600 mb-3">Có, bạn có thể hủy hoặc thay đổi đặt bàn:</p>
                                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                                    <li>Hủy miễn phí trước 2 giờ so với thời gian đã đặt</li>
                                    <li>Thay đổi thời gian tùy thuộc vào tình trạng bàn trống</li>
                                    <li>Liên hệ hotline để được hỗ trợ nhanh chóng</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing FAQs -->
            <div class="faq-section" data-category="pricing">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6 flex items-center">
                        <i class="fas fa-tag text-elegant-gold mr-3"></i>
                        Giá cả & Thanh toán
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Giá thuê bàn được tính như thế nào?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600 mb-3">Giá thuê bàn được tính theo giờ và phân loại theo:</p>
                                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                                    <li>Loại bàn (Standard, VIP, Tournament)</li>
                                    <li>Khung giờ (Giờ thường, Giờ cao điểm)</li>
                                    <li>Ngày trong tuần (Ngày thường, Cuối tuần)</li>
                                </ul>
                                <p class="text-gray-600 mt-3">Vui lòng xem bảng giá chi tiết tại quầy lễ tân hoặc trên website.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Có chính sách giảm giá cho học sinh/sinh viên không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600">Có, chúng tôi có chính sách giảm giá 15% cho học sinh, sinh viên khi xuất trình thẻ hợp lệ. Ưu đãi áp dụng cho tất cả các ngày trong tuần, trừ các khung giờ cao điểm.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services FAQs -->
            <div class="faq-section" data-category="services">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6 flex items-center">
                        <i class="fas fa-concierge-bell text-elegant-gold mr-3"></i>
                        Dịch vụ
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Quán có phục vụ đồ ăn thức uống không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600">Có, chúng tôi có menu đa dạng các loại đồ uống (cafe, trà, nước ngọt, bia) và đồ ăn nhẹ (snack, mì, đồ nướng). Tất cả đều được phục vụ tận bàn.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Có tổ chức sự kiện và giải đấu không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600 mb-3">Có, chúng tôi thường xuyên tổ chức:</p>
                                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                                    <li>Giải đấu billiards hàng tháng</li>
                                    <li>Sự kiện đặc biệt vào các ngày lễ</li>
                                    <li>Tổ chức tiệc sinh nhật, họp nhóm</li>
                                    <li>Team building cho công ty</li>
                                </ul>
                                <p class="text-gray-600 mt-3">Liên hệ hotline để được tư vấn chi tiết.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facilities FAQs -->
            <div class="faq-section" data-category="facilities">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-elegant-navy mb-6 flex items-center">
                        <i class="fas fa-building text-elegant-gold mr-3"></i>
                        Cơ sở vật chất
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Có chỗ đỗ xe không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600">Có, chúng tôi có bãi đỗ xe rộng rãi và an toàn:</p>
                                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                                    <li>Miễn phí đỗ xe máy</li>
                                    <li>Đỗ ô tô với giá ưu đãi cho khách hàng</li>
                                    <li>Giữ xe 24/24 có bảo vệ trực</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition duration-200 flex justify-between items-center">
                                <span>Quán có WiFi không?</span>
                                <i class="fas fa-chevron-down text-elegant-gold transition-transform duration-200"></i>
                            </button>
                            <div class="faq-answer px-6 py-4 border-t border-gray-200 hidden bg-gray-50">
                                <p class="text-gray-600">Có, chúng tôi cung cấp WiFi miễn phí tốc độ cao cho tất cả khách hàng. Vui lòng hỏi nhân viên để lấy mật khẩu.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Still Have Questions -->
        <div class="text-center mt-16">
            <div class="bg-gradient-to-r from-elegant-navy to-elegant-burgundy rounded-2xl p-8 text-white">
                <h2 class="text-2xl font-bold mb-4">Vẫn còn thắc mắc?</h2>
                <p class="text-lg mb-6 text-blue-100">Đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ bạn</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}" 
                       class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-3 rounded-lg transition duration-200 transform hover:scale-105">
                        <i class="fas fa-phone mr-2"></i>Liên hệ ngay
                    </a>
                    <a href="tel:02812345678" 
                       class="border border-elegant-gold text-elegant-gold hover:bg-elegant-gold hover:text-elegant-navy font-semibold px-8 py-3 rounded-lg transition duration-200">
                        <i class="fas fa-phone-alt mr-2"></i>Gọi: (028) 1234 5678
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // FAQ Accordion
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const answer = faqItem.querySelector('.faq-answer');
            const icon = this.querySelector('i');
            
            // Toggle answer
            answer.classList.toggle('hidden');
            
            // Rotate icon
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });

    // FAQ Category Filter
    document.querySelectorAll('.faq-category').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            document.querySelectorAll('.faq-category').forEach(btn => {
                btn.classList.remove('bg-elegant-gold', 'text-elegant-navy');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-elegant-gold', 'text-elegant-navy');
            
            // Filter FAQ sections
            document.querySelectorAll('.faq-section').forEach(section => {
                if (category === 'all' || section.getAttribute('data-category') === category) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    // FAQ Search
    document.getElementById('faqSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        document.querySelectorAll('.faq-item').forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                // Highlight matching text
                highlightText(item, searchTerm);
            } else {
                item.style.display = 'none';
            }
        });
    });

    function highlightText(element, searchTerm) {
        if (!searchTerm) return;
        
        const text = element.innerHTML;
        const regex = new RegExp(searchTerm, 'gi');
        const highlighted = text.replace(regex, match => `<mark class="bg-yellow-200">${match}</mark>`);
        element.innerHTML = highlighted;
    }
</script>
@endsection