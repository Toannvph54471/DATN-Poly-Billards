@extends('layouts.customer')

@section('title', 'Giới thiệu - Poly Billiards')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative max-w-6xl mx-auto px-4 text-center">
        <div class="flex justify-center items-center space-x-4 mb-8">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                <div class="flex items-center space-x-3">
    <div class="relative">
        <!-- Quả bida với hiệu ứng gradient và bóng -->
        <div class="w-12 h-12 bg-gradient-to-br from-blue-700 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
            <!-- Đường kẻ trắng trên quả bida -->
            <div class="absolute w-8 h-8 border-2 border-white rounded-full opacity-30"></div>
            <!-- Số 8 màu trắng (giống quả bida số 8) -->
            <span class="text-white font-bold text-xs z-10">8</span>
        </div>
        <!-- Hiệu ứng ánh sáng -->
        <div class="absolute top-1 left-1 w-2 h-2 bg-white rounded-full opacity-60"></div>
    </div>
</div>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold">Poly Billiards Management</h1>
        </div>
        <p class="text-xl mb-8 text-blue-100 leading-relaxed max-w-3xl mx-auto">
            Hệ thống quản lý toàn diện dành cho quán bi-a Poly Billiards
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="" 
               class="border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold px-8 py-4 rounded-lg text-lg transition duration-200">
                <i class="fas fa-question-circle mr-3"></i>
                Hướng dẫn sử dụng
            </a>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-blue-800 mb-4">Giới thiệu hệ thống</h2>
            <p class="text-lg text-gray-600">Hệ thống quản lý toàn diện được thiết kế riêng cho Poly Billiards</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="bg-blue-50 rounded-2xl p-8">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-cogs text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-3">Tính năng chính</h3>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Hệ thống POS bán hàng 
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Quản lý bàn bi-a và thời gian chơi
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Quản lý hóa đơn và thanh toán
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Báo cáo và thống kê
                    </li>
                </ul>
            </div>

            <div class="bg-blue-50 rounded-2xl p-8">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-blue-800 mb-3">Đối tượng sử dụng</h3>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <i class="fas fa-user-shield text-blue-500 mr-2"></i>
                        Quản trị viên
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                        Quản lý
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Nhân viên
                    </li>
                </ul>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 text-white text-center">
            <h3 class="text-2xl font-bold mb-4">Thông tin hệ thống</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-3xl font-bold mb-2">{{ date('Y') }}</div>
                    <p class="text-blue-200">Năm triển khai</p>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">24/7</div>
                    <p class="text-blue-200">Hỗ trợ kỹ thuật</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Support -->
<section class="py-16 bg-blue-50">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-blue-800 mb-6">Hỗ trợ kỹ thuật</h2>
        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            Nếu bạn gặp bất kỳ vấn đề nào với hệ thống, vui lòng liên hệ:
        </p>
        <div class="bg-white rounded-2xl p-8 shadow-lg max-w-md mx-auto">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-blue-800 mb-2">Bộ phận kỹ thuật</h3>
            <p class="text-gray-600 mb-4">Hỗ trợ 24/7</p>
            <a href="tel:0912345678" class="text-blue-600 font-semibold text-lg hover:text-blue-800 transition duration-200">
                <i class="fas fa-phone mr-2"></i>0912 345 678
            </a>
        </div>
    </div>
</section>
@endsection