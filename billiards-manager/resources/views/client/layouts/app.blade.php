<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billiard Club - Premium Billiard Experience')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html { scroll-behavior: smooth; }
        .fade-in { animation: fadeIn 0.6s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="fixed w-full top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center">
                    <span class="text-3xl font-bold text-amber-600">BILLIARD</span>
                    <span class="text-gray-800 text-sm ml-2 font-semibold">CLUB</span>
                </a>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-amber-600 font-medium">Trang chủ</a>
                    <a href="#about" class="text-gray-700 hover:text-amber-600 font-medium">Giới thiệu</a>
                    <a href="#services" class="text-gray-700 hover:text-amber-600 font-medium">Dịch vụ</a>
                    <a href="#gallery" class="text-gray-700 hover:text-amber-600 font-medium">Thư viện</a>
                    <a href="#pricing" class="text-gray-700 hover:text-amber-600 font-medium">Bảng giá</a>
                    <a href="#contact" class="text-gray-700 hover:text-amber-600 font-medium">Liên hệ</a>
                </nav>

                <!-- Right side buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <!-- User Dropdown -->
                        <div class="relative" id="user-menu">
                            <button id="user-menu-btn" class="flex items-center text-gray-700 hover:text-amber-600 font-medium">
                                <i class="fa-solid fa-user text-amber-600 mr-2"></i>
                                {{ Auth::user()->name }}
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </button>
                            <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden">
                                @if (Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-amber-50">Quản trị</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-amber-50">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Separate login/register buttons -->
                        <a href="{{ route('login') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full font-semibold hover:bg-gray-200 transition">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-amber-600 text-white px-4 py-2 rounded-full font-semibold hover:bg-amber-700 transition shadow-md">
                            Đăng ký
                        </a>
                    @endauth

                    <!-- Contact Button -->
            
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-800">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden pb-4 bg-white">
                <div class="flex flex-col space-y-4">
                    <a href="#home" class="text-gray-700 hover:text-amber-600 font-medium">Trang chủ</a>
                    <a href="#about" class="text-gray-700 hover:text-amber-600 font-medium">Giới thiệu</a>
                    <a href="#services" class="text-gray-700 hover:text-amber-600 font-medium">Dịch vụ</a>
                    <a href="#gallery" class="text-gray-700 hover:text-amber-600 font-medium">Thư viện</a>
                    <a href="#pricing" class="text-gray-700 hover:text-amber-600 font-medium">Bảng giá</a>
                    <a href="#contact" class="text-gray-700 hover:text-amber-600 font-medium">Liên hệ</a>
                    
                    @auth
                        <a href="#" class="text-gray-700 font-medium">{{ Auth::user()->name }}</a>
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-amber-600 font-medium">Quản trị</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-amber-600 font-medium text-left">Đăng xuất</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-amber-600 font-medium">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-amber-600 font-medium">Đăng ký</a>
                    @endauth

                    <button onclick="window.location.href='#contact'" class="bg-amber-600 text-white px-6 py-2 rounded-full font-semibold w-full hover:bg-amber-700">
                        Đặt bàn ngay
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-10">
        <div class="container mx-auto px-4 grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold text-amber-500 mb-4">BILLIARD CLUB</h3>
                <p class="text-gray-300 text-sm">Điểm đến hàng đầu cho những người đam mê bi-a. Bàn chuyên nghiệp, không gian đẳng cấp và trải nghiệm khó quên.</p>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Liên kết nhanh</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#home" class="text-gray-300 hover:text-amber-500">Trang chủ</a></li>
                    <li><a href="#about" class="text-gray-300 hover:text-amber-500">Giới thiệu</a></li>
                    <li><a href="#services" class="text-gray-300 hover:text-amber-500">Dịch vụ</a></li>
                    <li><a href="#gallery" class="text-gray-300 hover:text-amber-500">Thư viện</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Thông tin liên hệ</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><i class="fas fa-phone mr-2 text-amber-500"></i> +84 123 456 789</li>
                    <li><i class="fas fa-envelope mr-2 text-amber-500"></i> info@billiardclub.com</li>
                    <li><i class="fas fa-map-marker-alt mr-2 text-amber-500"></i> Hà Nội, Việt Nam</li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Giờ mở cửa</h4>
                <p class="text-gray-300 text-sm"><i class="fas fa-clock text-amber-500 mr-2"></i>T2 - T6: 10:00 - 02:00</p>
                <p class="pl-6 text-gray-300 text-sm">T7 - CN: 10:00 - 04:00</p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-gray-300 hover:text-amber-500"><i class="fab fa-facebook text-xl"></i></a>
                    <a href="#" class="text-gray-300 hover:text-amber-500"><i class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-300 hover:text-amber-500"><i class="fab fa-twitter text-xl"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-400 text-sm border-t border-gray-700 mt-8 pt-4">
            © 2025 Billiard Club. Bản quyền thuộc về chúng tôi.
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.toggle('fa-times');
            icon.classList.toggle('fa-bars');
        });

        // Dropdown toggle
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');
        if (userMenuBtn) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
        }
        document.addEventListener('click', (e) => {
            if (userDropdown && !document.getElementById('user-menu').contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
