<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Poly Billiards</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        'elegant': {
                            'navy': '#1e3a5f',
                            'burgundy': '#8b2635',
                            'gold': '#d4af37',
                            'cream': '#f5f5f5',
                            'charcoal': '#2d3748'
                        }
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'body': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @yield('styles')
</head>

<div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-3 pointer-events-none">
    <!-- Toasts will be inserted here -->
</div>

<style>
/* Toast Animation */
@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

.toast-enter {
    animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-exit {
    animation: slideOutRight 0.3s ease-in;
}

.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: currentColor;
    opacity: 0.3;
    animation: progress linear;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
// ===== TOAST NOTIFICATION SYSTEM =====
window.Toast = (function() {
    const container = document.getElementById('toast-container');
    let toastCount = 0;

    const icons = {
        success: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`,
        error: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`,
        warning: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>`,
        info: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`,
        loading: `<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>`
    };

    const colors = {
        success: {
            bg: 'bg-white',
            border: 'border-l-4 border-green-500',
            icon: 'text-green-500',
            text: 'text-gray-800',
            progress: 'text-green-500'
        },
        error: {
            bg: 'bg-white',
            border: 'border-l-4 border-red-500',
            icon: 'text-red-500',
            text: 'text-gray-800',
            progress: 'text-red-500'
        },
        warning: {
            bg: 'bg-white',
            border: 'border-l-4 border-yellow-500',
            icon: 'text-yellow-500',
            text: 'text-gray-800',
            progress: 'text-yellow-500'
        },
        info: {
            bg: 'bg-white',
            border: 'border-l-4 border-blue-500',
            icon: 'text-blue-500',
            text: 'text-gray-800',
            progress: 'text-blue-500'
        },
        loading: {
            bg: 'bg-white',
            border: 'border-l-4 border-gray-500',
            icon: 'text-gray-500',
            text: 'text-gray-800',
            progress: 'text-gray-500'
        }
    };

    function show(message, type = 'info', duration = 4000) {
        const id = `toast-${++toastCount}`;
        const color = colors[type] || colors.info;
        
        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `${color.bg} ${color.border} rounded-lg shadow-2xl p-4 max-w-md pointer-events-auto toast-enter relative overflow-hidden`;
        
        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 ${color.icon}">
                    ${icons[type] || icons.info}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium ${color.text}">
                        ${message}
                    </p>
                </div>
                <button onclick="Toast.close('${id}')" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            ${type !== 'loading' ? `<div class="toast-progress ${color.progress}" style="animation-duration: ${duration}ms"></div>` : ''}
        `;
        
        container.appendChild(toast);
        
        // Auto remove
        if (type !== 'loading' && duration > 0) {
            setTimeout(() => {
                close(id);
            }, duration);
        }
        
        return id;
    }

    function close(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-exit');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    function success(message, duration = 4000) {
        return show(message, 'success', duration);
    }

    function error(message, duration = 5000) {
        return show(message, 'error', duration);
    }

    function warning(message, duration = 4000) {
        return show(message, 'warning', duration);
    }

    function info(message, duration = 4000) {
        return show(message, 'info', duration);
    }

    function loading(message) {
        return show(message, 'loading', 0);
    }

    function promise(promise, messages) {
        const loadingId = loading(messages.loading || 'Đang xử lý...');
        
        return promise
            .then((result) => {
                close(loadingId);
                success(messages.success || 'Thành công!');
                return result;
            })
            .catch((error) => {
                close(loadingId);
                error(messages.error || 'Có lỗi xảy ra!');
                throw error;
            });
    }

    return {
        show,
        success,
        error,
        warning,
        info,
        loading,
        close,
        promise
    };
})();

// Shorthand
window.toast = window.Toast;
</script>
<body class="font-body bg-elegant-cream">
    <!-- Header -->
    <nav class="bg-elegant-navy shadow-lg border-b-4 border-elegant-gold">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="text-white font-display text-2xl font-bold flex items-center">
                        <div class="w-12 h-12 bg-elegant-gold rounded-full flex items-center justify-center mr-3 shadow-lg">
                            <i class="fas fa-billiard-ball text-elegant-navy text-xl"></i>
                        </div>
                        Poly Billiards
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:block">
                    <div class="ml-10 flex items-baseline space-x-1">
                        <a href="{{ route('home') }}" class="text-elegant-cream hover:bg-primary-700 hover:text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-home mr-2"></i>Trang chủ
                        </a>
                        <a href="{{route('reservation.create')}}" class="text-elegant-cream hover:bg-primary-700 hover:text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-calendar-plus mr-2"></i>Đặt bàn
                        </a>
                        <a href="{{route('promotions.index')}}" class="text-elegant-cream hover:bg-primary-700 hover:text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105"> 
                            <i class="fas fa-tag mr-2"></i>Khuyến mãi
                        </a>
                        <a href="{{route('contact')}}" class="text-elegant-cream hover:bg-primary-700 hover:text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105"> 
                            <i class="fas fa-phone mr-2"></i>Liên hệ
                        </a>
                        <a href="{{route('faq')}}" class="text-elegant-cream hover:bg-primary-700 hover:text-white px-4 py-3 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105"> 
                            <i class="fas fa-question-circle mr-2"></i>FAQ
                        </a>
                    </div>
                </div>

                <!-- User Authentication Links -->
                <div class="hidden lg:block">
                    <div class="ml-4 flex items-center space-x-4">
                        @auth
                            <span class="text-elegant-gold font-medium bg-elegant-charcoal px-3 py-2 rounded-lg">
                                <i class="fas fa-user mr-2"></i>{{ Auth::user()->name }}
                            </span>
                            
                            @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                                <a href="{{ route('admin.users.index') }}" class="text-elegant-cream hover:text-elegant-gold font-medium transition duration-200 bg-elegant-burgundy hover:bg-red-800 px-4 py-2 rounded-lg">
                                    <i class="fas fa-cog mr-2"></i>Quản trị
                                </a>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-elegant-cream hover:text-elegant-gold font-medium transition duration-200 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                </button>
                            </form>
                        @else
                            <a href="" class="text-elegant-cream hover:text-elegant-gold font-medium transition duration-200">
                                <i class="fas fa-search mr-2"></i>Tra cứu
                            </a>
                            <a href="{{ route('login') }}" class="text-elegant-cream hover:text-elegant-gold font-medium transition duration-200">
                                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-6 py-3 rounded-lg transition duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-user-plus mr-2"></i>Đăng ký
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="lg:hidden">
                    <button type="button" class="bg-primary-700 inline-flex items-center justify-center p-3 rounded-lg text-elegant-cream hover:bg-primary-600 focus:outline-none transition duration-200" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="lg:hidden hidden bg-elegant-navy border-t border-primary-600" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                    <i class="fas fa-home mr-3"></i>Trang chủ
                </a>
                <a href="{{route('reservation.create')}}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                    <i class="fas fa-calendar-plus mr-3"></i>Đặt bàn
                </a>
                <a href="{{route('promotions.index')}}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                    <i class="fas fa-tag mr-3"></i>Khuyến mãi
                </a>
                <a href="{{route('contact')}}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                    <i class="fas fa-phone mr-3"></i>Liên hệ
                </a>
                <a href="{{route('faq')}}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                    <i class="fas fa-question-circle mr-3"></i>FAQ
                </a>

                <li class="nav-item">
                <a href="{{ route('customer.bills.index') }}" class="nav-link">
                    <i class="fas fa-file-invoice mr-2"></i>
                  Lịch sử hóa đơn
                    </a>
                </li>
                
                @auth
                    <div class="border-t border-primary-600 pt-4">
                        <div class="flex items-center px-3 pb-3">
                            <i class="fas fa-user-circle text-elegant-gold text-2xl mr-3"></i>
                            <div>
                                <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-primary-200">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('admin.users.index') }}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                                <i class="fas fa-cog mr-3"></i>Quản trị
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
                        <a href="{{route('reservation.create')}}" class="text-elegant-cream hover:bg-primary-700 block px-3 py-3 rounded-lg text-base font-medium transition duration-200">
                            <i class="fas fa-search mr-3"></i>Tra cứu đặt bàn
                        </a>
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
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-elegant-navy text-elegant-cream pt-12 pb-8 border-t-4 border-elegant-gold">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-display font-bold mb-6 flex items-center">
                        <div class="w-10 h-10 bg-elegant-gold rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-billiard-ball text-elegant-navy"></i>
                        </div>
                        Poly Billiards
                    </h3>
                    <p class="text-primary-200 mb-4 leading-relaxed">
                        Thiên đường bi-a chuyên nghiệp với không gian sang trọng và dịch vụ đẳng cấp.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-primary-700 hover:bg-primary-600 rounded-full flex items-center justify-center transition duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary-700 hover:bg-primary-600 rounded-full flex items-center justify-center transition duration-200">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary-700 hover:bg-primary-600 rounded-full flex items-center justify-center transition duration-200">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-elegant-gold">Liên hệ</h4>
                    <div class="space-y-3">
                        <p class="flex items-center text-primary-200">
                            <i class="fas fa-map-marker-alt text-elegant-gold mr-3 w-5"></i>
                            123 Đường ABC, Quận 1, TP.HCM
                        </p>
                        <p class="flex items-center text-primary-200">
                            <i class="fas fa-phone text-elegant-gold mr-3 w-5"></i>
                            (028) 1234 5678
                        </p>
                        <p class="flex items-center text-primary-200">
                            <i class="fas fa-envelope text-elegant-gold mr-3 w-5"></i>
                            info@polybilliards.com
                        </p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-elegant-gold">Giờ mở cửa</h4>
                    <div class="space-y-2 text-primary-200">
                        <p class="flex justify-between">
                            <span>Thứ 2 - Thứ 6:</span>
                            <span>8:00 - 24:00</span>
                        </p>
                        <p class="flex justify-between">
                            <span>Thứ 7 - CN:</span>
                            <span>24/24</span>
                        </p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-elegant-gold">Liên kết nhanh</h4>
                    <div class="space-y-2">
                        <a href="{{route('reservation.create')}}" class="block text-primary-200 hover:text-elegant-gold transition duration-200">Đặt bàn</a>
                        <a href="{{route('promotions.index')}}" class="block text-primary-200 hover:text-elegant-gold transition duration-200">Khuyến mãi</a>
                        <a href="{{route('faq')}}" class="block text-primary-200 hover:text-elegant-gold transition duration-200">Câu hỏi thường gặp</a>
                        <a href="{{route('contact')}}" class="block text-primary-200 hover:text-elegant-gold transition duration-200">Liên hệ</a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-primary-700 pt-6 text-center text-primary-300">
                <p>&copy; 2024 Poly Billiards. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        window.Validator = (function() {
    
    // Validation rules
    const rules = {
        required: (value, fieldName) => {
            if (!value || value.toString().trim() === '') {
                return `${fieldName} không được để trống`;
            }
            return null;
        },
        
        email: (value) => {
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return 'Email không hợp lệ';
            }
            return null;
        },
        
        phone: (value) => {
            if (value && !/^(0|\+84)[0-9]{9}$/.test(value.replace(/\s/g, ''))) {
                return 'Số điện thoại không hợp lệ (10 chữ số, bắt đầu bằng 0)';
            }
            return null;
        },
        
        min: (value, min, fieldName) => {
            if (value && parseFloat(value) < min) {
                return `${fieldName} phải >= ${min}`;
            }
            return null;
        },
        
        max: (value, max, fieldName) => {
            if (value && parseFloat(value) > max) {
                return `${fieldName} phải <= ${max}`;
            }
            return null;
        },
        
        minLength: (value, length, fieldName) => {
            if (value && value.length < length) {
                return `${fieldName} phải có ít nhất ${length} ký tự`;
            }
            return null;
        },
        
        maxLength: (value, length, fieldName) => {
            if (value && value.length > length) {
                return `${fieldName} không được vượt quá ${length} ký tự`;
            }
            return null;
        },
        
        date: (value) => {
            if (value && isNaN(Date.parse(value))) {
                return 'Ngày không hợp lệ';
            }
            return null;
        },
        
        futureDate: (value, fieldName) => {
            if (value) {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    return `${fieldName} phải là ngày trong tương lai`;
                }
            }
            return null;
        },
        
        time: (value) => {
            if (value && !/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value)) {
                return 'Giờ không hợp lệ';
            }
            return null;
        },
        
        pattern: (value, pattern, message) => {
            if (value && !new RegExp(pattern).test(value)) {
                return message || 'Định dạng không hợp lệ';
            }
            return null;
        },
        
        numeric: (value, fieldName) => {
            if (value && isNaN(value)) {
                return `${fieldName} phải là số`;
            }
            return null;
        },
        
        integer: (value, fieldName) => {
            if (value && !Number.isInteger(Number(value))) {
                return `${fieldName} phải là số nguyên`;
            }
            return null;
        }
    };

    // Validate single field
    function validateField(value, validations, fieldName) {
        const errors = [];
        
        for (const [rule, params] of Object.entries(validations)) {
            if (rules[rule]) {
                const error = Array.isArray(params)
                    ? rules[rule](value, ...params, fieldName)
                    : rules[rule](value, params, fieldName);
                
                if (error) {
                    errors.push(error);
                }
            }
        }
        
        return errors.length > 0 ? errors[0] : null;
    }

    // Validate form data
    function validate(formData, validationRules) {
        const errors = {};
        
        for (const [field, rules] of Object.entries(validationRules)) {
            const value = formData[field];
            const error = validateField(value, rules, rules.label || field);
            
            if (error) {
                errors[field] = error;
            }
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }

    // Show validation errors in UI
    function showErrors(errors, formPrefix = '') {
        // Clear previous errors
        document.querySelectorAll('.validation-error').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'border-2');
            el.classList.add('border-gray-300');
        });

        // Show new errors
        for (const [field, message] of Object.entries(errors)) {
            const input = document.getElementById(formPrefix + field);
            if (input) {
                // Highlight input
                input.classList.remove('border-gray-300');
                input.classList.add('border-red-500', 'border-2');
                
                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error text-red-500 text-sm mt-1 flex items-center';
                errorDiv.innerHTML = `
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>${message}</span>
                `;
                input.parentElement.appendChild(errorDiv);
            }
        }

        // Show toast for first error
        const firstError = Object.values(errors)[0];
        if (firstError) {
            Toast.error(firstError);
        }
    }

    // Clear all validation errors
    function clearErrors() {
        document.querySelectorAll('.validation-error').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'border-2');
            el.classList.add('border-gray-300');
        });
    }

    // Real-time validation
    function attachRealTimeValidation(inputId, validations, fieldName) {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.addEventListener('blur', function() {
            const error = validateField(this.value, validations, fieldName);
            
            // Clear previous error
            const prevError = this.parentElement.querySelector('.validation-error');
            if (prevError) prevError.remove();
            
            this.classList.remove('border-red-500', 'border-2');
            this.classList.add('border-gray-300');

            if (error) {
                this.classList.remove('border-gray-300');
                this.classList.add('border-red-500', 'border-2');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error text-red-500 text-sm mt-1';
                errorDiv.textContent = error;
                this.parentElement.appendChild(errorDiv);
            }
        });

        // Remove error on input
        input.addEventListener('input', function() {
            const prevError = this.parentElement.querySelector('.validation-error');
            if (prevError) {
                prevError.remove();
                this.classList.remove('border-red-500', 'border-2');
                this.classList.add('border-gray-300');
            }
        });
    }

    return {
        validate,
        validateField,
        showErrors,
        clearErrors,
        attachRealTimeValidation,
        rules
    };
})();
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>

    @yield('scripts')
</body>
</html>