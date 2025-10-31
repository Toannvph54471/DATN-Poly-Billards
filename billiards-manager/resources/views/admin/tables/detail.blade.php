@extends('admin.layouts.app')

@section('title', 'Chi tiết bàn - Billiards Management')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chi tiết bàn</h1>
                <p class="text-gray-600">Thông tin chi tiết và quản lý bàn {{ $table->table_name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.tables.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Hiển thị thông báo -->
        @if (session('success'))
            <div id="successMessage"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 transition-all duration-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="errorMessage"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 transition-all duration-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Danh sách sản phẩm và quản lý đơn hàng (Bên trái) -->
            <div class="lg:col-span-2">
                <!-- Form thêm sản phẩm nhanh -->
                <div class="bg-white p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Thêm sản phẩm nhanh</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tìm kiếm sản phẩm -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm sản phẩm</label>
                            <div class="relative">
                                <input type="text" id="productSearch" placeholder="Nhập tên sản phẩm..."
                                    class="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Lọc danh mục -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                            <select id="categoryFilter"
                                class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tất cả</option>
                                <option value="Drink">Đồ uống</option>
                                <option value="Food">Đồ ăn</option>
                                <option value="Service">Dịch vụ</option>
                            </select>
                        </div>
                    </div>

                    <!-- Danh sách sản phẩm dạng đơn giản -->
                    <div class="mt-4 max-h-64 overflow-y-auto border border-gray-200 rounded">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tồn kho</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="productList">
                                @foreach ($products as $product)
                                    <tr class="product-item hover:bg-gray-50" data-category="{{ $product->category }}"
                                        data-name="{{ strtolower($product->name) }}">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $product->product_code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-green-600">
                                            {{ number_format($product->price, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <button onclick="addToOrder({{ $product->id }})"
                                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition flex items-center"
                                                {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-plus mr-1 text-xs"></i>
                                                Thêm
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Thông báo không có sản phẩm -->
                    <div id="noProductsMessage" class="hidden text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-gray-500">Hãy thử tìm kiếm với từ khóa khác.</p>
                    </div>
                </div>

                <!-- Đơn hàng hiện tại -->
                <div class="bg-white p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Đơn hàng hiện tại</h2>

                    <div class="mb-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đơn giá
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thành
                                            tiền</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" id="orderItems">
                                    <!-- Các sản phẩm trong đơn hàng sẽ được thêm vào đây -->
                                </tbody>
                            </table>
                        </div>

                        <div id="emptyOrderMessage" class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-cart text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có sản phẩm nào</h3>
                            <p class="text-gray-500">Hãy thêm sản phẩm từ danh sách bên trên.</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tổng tiền sản phẩm:</span>
                                <span class="font-semibold" id="productTotal">0 đ</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tiền bàn:</span>
                                <span class="font-semibold" id="tableCost">0 đ</span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 pt-2">
                                <span class="text-gray-800">Tổng cộng:</span>
                                <span class="text-green-600" id="orderTotal">0 đ</span>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-4">
                            <button onclick="clearOrder()"
                                class="flex-1 bg-gray-600 text-white py-3 font-semibold hover:bg-gray-700 transition rounded">
                                <i class="fas fa-times mr-2"></i>
                                Hủy đơn
                            </button>
                            <button onclick="confirmPayment()"
                                class="flex-1 bg-green-600 text-white py-3 font-semibold hover:bg-green-700 transition rounded">
                                <i class="fas fa-check mr-2"></i>
                                Xác nhận thanh toán
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin bàn (Bên phải) -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Thông tin bàn</h2>

                    <!-- Biểu tượng bàn -->
                    <div class="flex justify-center mb-6">
                        <div class="relative">
                            <div id="tableIcon"
                                class="w-24 h-24 flex items-center justify-center rounded-full
                                @if ($table->status == 'available') bg-gray-100
                                @elseif($table->status == 'in_use') bg-green-100
                                @else bg-yellow-100 @endif">
                                <i
                                    class="fas fa-table text-3xl 
                                    @if ($table->status == 'available') text-gray-600
                                    @elseif($table->status == 'in_use') text-green-600
                                    @else text-yellow-600 @endif"></i>
                            </div>
                            @if ($table->status == 'in_use')
                                <div id="activeIndicator" class="absolute -top-2 -right-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-5 w-5 bg-green-400 opacity-75 rounded-full"></span>
                                    <span class="relative inline-flex h-5 w-5 bg-green-500 rounded-full"></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Thông tin chi tiết -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tên bàn:</span>
                            <span class="font-semibold text-gray-900">{{ $table->table_name }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Số bàn:</span>
                            <span class="font-semibold text-gray-900">{{ $table->table_number }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Loại bàn:</span>
                            <span class="font-semibold text-gray-900 capitalize">{{ $table->type ?? 'N/A' }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Giá/giờ:</span>
                            <span
                                class="font-semibold text-green-600">{{ number_format($table->hourly_rate, 0, ',', '.') }}
                                đ</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Vị trí:</span>
                            <span class="font-semibold text-gray-900">{{ $table->position ?? 'Không rõ' }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Trạng thái:</span>
                            <span id="tableStatus" class="font-semibold">
                                @if ($table->status == 'available')
                                    <span class="text-gray-600">⏸️ Trống</span>
                                @elseif($table->status == 'in_use')
                                    <span class="text-green-600">▶️ Đang sử dụng</span>
                                @else
                                    <span class="text-yellow-600">🔧 Bảo trì</span>
                                @endif
                            </span>
                        </div>

                        <!-- Thông tin Bill hiện tại (nếu có) -->
                        @if ($table->status == 'in_use' && $table->currentBill)
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                                <h3 class="font-semibold text-blue-800 mb-2 text-sm">Thông tin hóa đơn</h3>
                                <div class="text-xs text-blue-700 space-y-1">
                                    <div><span class="font-medium">Mã bill:</span> {{ $table->currentBill->bill_number }}
                                    </div>
                                    <div><span class="font-medium">Bắt đầu:</span>
                                        {{ $table->currentBill->start_time->format('H:i d/m/Y') }}</div>
                                    <div><span class="font-medium">Thời gian:</span> <span id="currentDuration">0</span>
                                        phút</div>
                                    <div><span class="font-medium">Tạm tính:</span> <span id="currentCost">0</span> đ
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        @if ($table->status == 'available')
                            <form id="startForm" method="POST" action="{{ route('admin.tables.start', $table->id) }}">
                                @csrf
                                <button type="submit" id="startButton"
                                    class="w-full bg-green-600 text-white py-3 font-semibold hover:bg-green-700 transition rounded flex items-center justify-center">
                                    <i class="fas fa-play mr-2"></i>
                                    Bắt đầu sử dụng
                                </button>
                            </form>
                        @elseif($table->status == 'in_use')
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('admin.tables.stop', $table->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-red-600 text-white py-3 font-semibold hover:bg-red-700 transition rounded flex items-center justify-center">
                                        <i class="fas fa-stop mr-2"></i>
                                        Kết thúc phiên
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.tables.activate', $table->id) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 text-white py-3 font-semibold hover:bg-green-700 transition rounded flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Kích hoạt bàn
                                </button>
                            </form>
                        @endif

                        <div class="flex space-x-3 mt-3">
                            <a href="{{ route('admin.tables.edit', $table->id) }}"
                                class="flex-1 bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 transition rounded flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Sửa
                            </a>

                            <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa bàn này không?');" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-600 text-white py-2 font-medium hover:bg-red-700 transition rounded flex items-center justify-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Dữ liệu sản phẩm và đơn hàng
        const products = {!! json_encode($products) !!};
        let currentOrder = [];

        // Lọc sản phẩm
        function filterProducts() {
            const searchTerm = document.getElementById('productSearch').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const productList = document.getElementById('productList');
            const noProductsMessage = document.getElementById('noProductsMessage');

            let hasVisibleProducts = false;

            document.querySelectorAll('.product-item').forEach(item => {
                const productName = item.getAttribute('data-name');
                const productCategory = item.getAttribute('data-category');

                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = !categoryFilter || productCategory === categoryFilter;

                if (matchesSearch && matchesCategory) {
                    item.style.display = 'table-row';
                    hasVisibleProducts = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Hiển thị thông báo không có sản phẩm
            if (hasVisibleProducts) {
                noProductsMessage.classList.add('hidden');
                productList.classList.remove('hidden');
            } else {
                noProductsMessage.classList.remove('hidden');
                productList.classList.add('hidden');
            }
        }

        // Thêm sản phẩm vào đơn hàng
        function addToOrder(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            const existingItem = currentOrder.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                currentOrder.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    unit: product.unit
                });
            }

            updateOrderDisplay();

            // Hiệu ứng feedback
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-1 text-xs"></i>Đã thêm';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 1000);
        }

        // Cập nhật hiển thị đơn hàng
        function updateOrderDisplay() {
            const orderItemsContainer = document.getElementById('orderItems');
            const emptyOrderMessage = document.getElementById('emptyOrderMessage');
            const productTotalElement = document.getElementById('productTotal');
            const tableCostElement = document.getElementById('tableCost');
            const orderTotalElement = document.getElementById('orderTotal');

            orderItemsContainer.innerHTML = '';

            if (currentOrder.length === 0) {
                emptyOrderMessage.classList.remove('hidden');
                productTotalElement.textContent = '0 đ';
                tableCostElement.textContent = '0 đ';
                orderTotalElement.textContent = '0 đ';
                return;
            }

            emptyOrderMessage.classList.add('hidden');

            let productTotal = 0;

            currentOrder.forEach(item => {
                const itemTotal = item.price * item.quantity;
                productTotal += itemTotal;

                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-900">${item.name}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${formatCurrency(item.price)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <div class="flex items-center">
                            <button onclick="decreaseQuantity(${item.id})" class="bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="px-3 py-1 border-y border-gray-200 min-w-12 text-center bg-white">${item.quantity}</span>
                            <button onclick="increaseQuantity(${item.id})" class="bg-gray-200 px-2 py-1 rounded-r hover:bg-gray-300">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">${formatCurrency(itemTotal)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <button onclick="removeFromOrder(${item.id})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                orderItemsContainer.appendChild(row);
            });

            const tableCost =
                {{ $table->status == 'in_use' && $table->currentBill ? ($table->hourly_rate / 60) * ($currentUsage->duration_minutes ?? 0) : 0 }};

            productTotalElement.textContent = formatCurrency(productTotal);
            tableCostElement.textContent = formatCurrency(tableCost);
            orderTotalElement.textContent = formatCurrency(productTotal + tableCost);
        }

        // Tăng số lượng
        function increaseQuantity(productId) {
            const item = currentOrder.find(item => item.id === productId);
            if (item) {
                item.quantity += 1;
                updateOrderDisplay();
            }
        }

        // Giảm số lượng
        function decreaseQuantity(productId) {
            const item = currentOrder.find(item => item.id === productId);
            if (item) {
                item.quantity -= 1;
                if (item.quantity <= 0) {
                    removeFromOrder(productId);
                } else {
                    updateOrderDisplay();
                }
            }
        }

        // Xóa sản phẩm khỏi đơn hàng
        function removeFromOrder(productId) {
            currentOrder = currentOrder.filter(item => item.id !== productId);
            updateOrderDisplay();
        }

        // Hủy đơn hàng
        function clearOrder() {
            if (currentOrder.length === 0) {
                const cancelBtn = document.querySelector('button[onclick="clearOrder()"]');
                cancelBtn.animate([{
                        transform: 'translateX(0)'
                    },
                    {
                        transform: 'translateX(-10px)'
                    },
                    {
                        transform: 'translateX(10px)'
                    },
                    {
                        transform: 'translateX(0)'
                    }
                ], {
                    duration: 300,
                    iterations: 1
                });
                return;
            }

            if (confirm('Bạn có chắc muốn hủy toàn bộ đơn hàng?')) {
                currentOrder = [];
                updateOrderDisplay();
            }
        }

        // Xác nhận thanh toán
        function confirmPayment() {
            if (currentOrder.length === 0) {
                alert('Vui lòng thêm sản phẩm vào đơn hàng trước khi thanh toán!');
                return;
            }

            if (confirm('Xác nhận thanh toán đơn hàng?')) {
                alert('Thanh toán thành công!');
                currentOrder = [];
                updateOrderDisplay();
            }
        }

        // Định dạng tiền tệ
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Khởi tạo
        document.addEventListener('DOMContentLoaded', function() {
            updateOrderDisplay();

            // Sự kiện tìm kiếm
            document.getElementById('productSearch').addEventListener('input', filterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);

            // Sự kiện form start
            const startForm = document.getElementById('startForm');
            const startButton = document.getElementById('startButton');

            if (startForm && startButton) {
                startForm.addEventListener('submit', function(e) {
                    startButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
                    startButton.disabled = true;
                    startButton.classList.add('opacity-50');
                });
            }

            // Tự động ẩn thông báo
            setTimeout(() => {
                const successMessage = document.getElementById('successMessage');
                const errorMessage = document.getElementById('errorMessage');

                if (successMessage) {
                    successMessage.style.opacity = '0';
                    setTimeout(() => successMessage.remove(), 300);
                }

                if (errorMessage) {
                    errorMessage.style.opacity = '0';
                    setTimeout(() => errorMessage.remove(), 300);
                }
            }, 5000);
        });

        // Cập nhật thời gian sử dụng real-time
        @if ($table->status == 'in_use' && $table->currentBill)
            function updateCurrentUsage() {
                const startTime = new Date('{{ $table->currentBill->start_time }}').getTime();
                const now = new Date().getTime();
                const duration = Math.floor((now - startTime) / (1000 * 60));
                const hourlyRate = {{ $table->hourly_rate }};
                const cost = Math.floor((duration / 60) * hourlyRate);

                document.getElementById('currentDuration').textContent = duration;
                document.getElementById('currentCost').textContent = formatCurrency(cost);
            }

            updateCurrentUsage();
            setInterval(updateCurrentUsage, 60000);
        @endif
    </script>
@endsection
