<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hướng dẫn nội bộ - Poly Billiards')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #e7e7e7 0%, #a7befe 99%);
            min-height: 100vh;
        }
    </style>
    @yield('styles')
</head>

<body class="min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
<!-- Logo hoàn chỉnh với viên bi trắng bên trong -->
<div class="flex items-center space-x-3">
    <div class="relative">
        <!-- Container hình vuông với gradient cam và animation -->
        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center shadow-xl transform perspective-1000 rotate-6 hover:rotate-0 transition-transform duration-300 overflow-visible">
            <!-- Viên bi đen với viền trắng -->
            <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center relative border-2 border-white shadow-inner">
                <!-- Hiệu ứng phản chiếu trên viên bi -->
                <div class="absolute top-1 left-2 w-3 h-2 bg-gray-400 rounded-full opacity-40 blur-sm"></div>
                
                <!-- Viên bi trắng nhỏ bên trong (quanh số 8) -->
                <div class="absolute w-4 h-4 bg-white rounded-full opacity-90 flex items-center justify-center">
                    <!-- Số 8 màu đen trên nền trắng -->
                    <span class="text-black font-bold text-xs">8</span>
                </div>
            </div>
        </div>
        <!-- Hiệu ứng ánh sáng cam -->
        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-orange-300/50 rounded-full blur-sm"></div>
        <!-- Hiệu ứng ánh sáng trắng -->
        <div class="absolute top-1 left-1 w-2 h-2 bg-white rounded-full opacity-70"></div>
    </div>
    
    <div>
        <h1 class="text-2xl font-black uppercase tracking-wider bg-gradient-to-r from-orange-600 via-amber-400 to-yellow-600 bg-clip-text text-transparent drop-shadow-lg">
            Poly Billiards
        </h1>
        <p class="text-amber-600 text-sm font-semibold tracking-wide">Đẳng cấp và đam mê</p>
    </div>
</div>

                <!-- Navigation - Desktop -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="" 
                       class="text-gray-700 hover:text-blue-600 font-medium transition duration-200 {{ request()->routeIs('guide.home') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        <i class="fas fa-home mr-2"></i>Giới thiệu
                    </a>
                    
                    <a href="" 
                       class="text-gray-700 hover:text-blue-600 font-medium transition duration-200 {{ request()->routeIs('guide.faq') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        <i class="fas fa-question-circle mr-2"></i>Câu hỏi thường gặp
                    </a>
                </nav>

                <!-- User Authentication Links (Desktop) -->
                <div class="lg:block">
                    <div class="ml-4 flex items-center space-x-4">
                        @auth
                            <div class="relative group">
                                <!-- User Button với thiết kế mới -->
                                <button class="flex items-center bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-medium px-4 py-2.5 rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-2 text-sm transition-transform duration-300 group-hover:rotate-180"></i>
                                </button>

                                <!-- Dropdown Menu với thiết kế cải tiến -->
                                <div class="absolute right-0 mt-3 w-64 bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden backdrop-blur-sm bg-white/95">
                                    <!-- User Info Header -->
                                    <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                                                <i class="fas fa-user text-white text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    @if(Auth::user()->isAdmin())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-crown mr-1"></i>Quản trị viên
                                                        </span>
                                                    @elseif(Auth::user()->isManager())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            <i class="fas fa-star mr-1"></i>Quản lý
                                                        </span>
                                                    @elseif(Auth::user()->isEmployee())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-user-tie mr-1"></i>Nhân viên
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Menu Items -->
                                    <div class="py-2">
                                        @if(Auth::user()->isEmployee())
                                            <a href="{{ route('admin.pos.dashboard') }}" 
                                               class="flex items-center px-5 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors shadow-sm">
                                                    <i class="fas fa-cash-register text-blue-600 text-sm"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">POS Bán hàng</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Hệ thống thanh toán</p>
                                                </div>
                                                <i class="fas fa-chevron-right text-gray-400 text-xs group-hover:text-blue-500 transition-colors"></i>
                                            </a>
                                        @endif

                                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                                            <a href="{{ route('admin.dashboard') }}" 
                                               class="flex items-center px-5 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors shadow-sm">
                                                    <i class="fas fa-cog text-blue-600 text-sm"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">Quản trị hệ thống</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Bảng điều khiển</p>
                                                </div>
                                                <i class="fas fa-chevron-right text-gray-400 text-xs group-hover:text-blue-500 transition-colors"></i>
                                            </a>
                                        @endif

                                        @if(Auth::user()->employee ?? false)
                                            <a href="{{ route('attendance.my-qr') }}" 
                                               class="flex items-center px-5 py-3 text-blue-700 bg-blue-50/80 hover:bg-blue-100 transition-all duration-200 group border-l-3 border-blue-500">
                                                <div class="w-10 h-10 rounded-xl bg-blue-200 flex items-center justify-center mr-3 group-hover:bg-blue-300 transition-colors shadow-sm">
                                                    <i class="fas fa-qrcode text-blue-700 text-sm"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">Mã QR Cá Nhân</p>
                                                    <p class="text-xs text-blue-600 mt-0.5">Điểm danh hôm nay</p>
                                                </div>
                                                <i class="fas fa-external-link-alt text-blue-500 text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <!-- Separator -->
                                    <div class="border-t border-gray-100 mx-4 my-1"></div>
                                    
                                    <!-- Logout Button -->
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full flex items-center px-5 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors shadow-sm">
                                                <i class="fas fa-sign-out-alt text-red-500 text-sm"></i>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="font-medium text-sm">Đăng xuất</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Kết thúc phiên làm việc</p>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400 text-xs group-hover:text-red-500 transition-colors"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Authentication Links với thiết kế mới -->
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('login') }}" 
                                   class="flex items-center text-gray-700 hover:text-blue-600 font-medium transition-all duration-300 px-4 py-2 rounded-lg hover:bg-blue-50 group">
                                    <i class="fas fa-sign-in-alt mr-2 text-blue-500 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold">Đăng nhập</span>
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="flex items-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    <span>Đăng ký</span>
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" id="mobileMenuButton" 
                            class="text-gray-700 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200 py-4">
            <div class="flex flex-col space-y-4 px-4">
                <a href="" 
                   class="text-gray-700 hover:text-blue-600 font-medium transition duration-200 {{ request()->routeIs('guide.home') ? 'text-blue-600 bg-blue-50' : '' }} px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-home mr-3 w-5 text-center"></i>
                    Giới thiệu trang web
                </a>
                
                <a href="" 
                   class="text-gray-700 hover:text-blue-600 font-medium transition duration-200 {{ request()->routeIs('guide.faq') ? 'text-blue-600 bg-blue-50' : '' }} px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-question-circle mr-3 w-5 text-center"></i>
                    Câu hỏi thường gặp
                </a>

                @auth
                    <div class="border-t border-primary-600 pt-4">
                        <div class="flex items-center px-3 pb-3">
                            <i class="fas fa-user-circle text-elegant-gold text-2xl mr-3"></i>
                            <div>
                                <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-primary-200">{{ Auth::user()->email }}</div>
                            </div>
                        </div>

                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('admin.dashboard') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                                <i class="fas fa-cog mr-3"></i>Quản trị
                            </a>
                        @endif

                        @if(Auth::user()->isEmployee())
                            <a href="{{ route('admin.pos.dashboard') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                                <i class="fas fa-cash-register mr-3"></i>POS
                            </a>
                        @endif

                        @if(Auth::user()->employee ?? false)
                            <a href="{{ route('attendance.my-qr') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200 text-yellow-400">
                                <i class="fas fa-qrcode mr-3"></i>Mã QR Cá Nhân
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                                <i class="fas fa-sign-out-alt mr-3"></i>Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-primary-600 pt-4">
                        <a href="{{ route('login') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                            <i class="fas fa-sign-in-alt mr-3"></i>Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-elegant-gold text-elegant-navy font-semibold block px-3 py-3 rounded-lg text-base font-medium transition duration-200 mt-2">
                            <i class="fas fa-user-plus mr-3"></i>Đăng ký
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex justify-center items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-billiard-ball text-blue-600 text-sm"></i>
                    </div>
                    <h2 class="text-xl font-bold">Poly Billiards</h2>
                </div>
                <p class="text-blue-200 mb-4">Hệ thống quản lý & Hướng dẫn nội bộ</p>
                <p class="text-blue-300 text-sm">© 2024 Poly Billiards. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>