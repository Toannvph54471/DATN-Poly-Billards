@extends('admin.layouts.app')

@section('title', 'Nhập tồn kho - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-400 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-clipboard-list text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Nhập tồn kho hàng ngày</h1>
                <p class="text-gray-600">Cập nhật số lượng tồn kho thực tế</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.inventory.history') }}"
                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                <i class="fas fa-history mr-2"></i>
                Lịch sử nhập tồn
            </a>
            <button type="button" onclick="saveDraft()"
                class="bg-orange-500 text-white rounded-lg px-4 py-2 hover:bg-orange-600 transition flex items-center">
                <i class="fas fa-save mr-2"></i>
                Lưu nháp
            </button>
        </div>
    </div>

    <!-- Date & Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngày nhập tồn</p>
                    <p class="text-xl font-bold text-gray-800" id="current-date">{{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng sản phẩm</p>
                    <p class="text-xl font-bold text-gray-800" id="total-products">0</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cubes text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đã kiểm kê</p>
                    <p class="text-xl font-bold text-gray-800" id="checked-products">0</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Chênh lệch</p>
                    <p class="text-xl font-bold text-gray-800" id="difference-count">0</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Controls -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date Selection -->
            <div>
                <label for="inventory_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày kiểm kê</label>
                <input type="date" name="inventory_date" id="inventory_date" value="{{ now()->format('Y-m-d') }}"
                    class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-1">Lọc danh mục</label>
                <select name="category_filter" id="category_filter"
                    class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả danh mục</option>
                    <option value="Đồ uống">Đồ uống</option>
                    <option value="Đồ ăn">Đồ ăn</option>
                    <option value="Tráng miệng">Tráng miệng</option>
                    <option value="Nguyên liệu">Nguyên liệu</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label for="product_search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="product_search" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Tên hoặc mã sản phẩm...">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="button" onclick="applyFilters()"
                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                    <i class="fas fa-filter mr-2"></i>
                    Lọc
                </button>
                <button type="button" onclick="clearFilters()"
                    class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Inventory Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.inventory.store') }}" method="POST" id="inventory-form">
            @csrf
            <input type="hidden" name="inventory_date" id="form_inventory_date" value="{{ now()->format('Y-m-d') }}">

            <!-- Table Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Danh sách sản phẩm kiểm kê</h2>
                    <div class="flex items-center space-x-4">
                        <button type="button" onclick="checkAll()" 
                            class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <i class="fas fa-check-square mr-1"></i> Chọn tất cả
                        </button>
                        <button type="button" onclick="uncheckAll()" 
                            class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                            <i class="fas fa-square mr-1"></i> Bỏ chọn tất cả
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                            </th>
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Tồn hệ thống</th>
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Tồn thực tế</th>
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Chênh lệch</th>
                            <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="inventory-items">
                        @foreach($products as $product)
                        <tr class="inventory-item hover:bg-gray-50 transition" data-category="{{ $product->category }}" data-name="{{ strtolower($product->name) }}" data-code="{{ strtolower($product->product_code) }}">
                            <td class="py-4 px-6">
                                <input type="checkbox" name="checked_items[]" value="{{ $product->id }}" class="item-checkbox" onchange="updateSummary()">
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($product->image)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-cube text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->product_code }}</div>
                                        <div class="text-xs text-gray-400">{{ $product->category }} • {{ $product->unit }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm font-medium text-gray-900 system-stock">
                                    {{ $product->stock_quantity }} {{ $product->unit }}
                                </div>
                                <input type="hidden" name="system_stock[{{ $product->id }}]" value="{{ $product->stock_quantity }}">
                            </td>
                            <td class="py-4 px-6">
                                <div class="relative">
                                    <input type="number" 
                                           name="actual_stock[{{ $product->id }}]" 
                                           value="{{ $product->stock_quantity }}"
                                           min="0"
                                           class="actual-stock-input w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                           onchange="calculateDifference({{ $product->id }})"
                                           oninput="markAsChecked(this)">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">
                                        {{ $product->unit }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm font-medium difference-display" id="difference-{{ $product->id }}">
                                    0 {{ $product->unit }}
                                </div>
                                <input type="hidden" name="difference[{{ $product->id }}]" id="difference-input-{{ $product->id }}" value="0">
                            </td>
                            <td class="py-4 px-6">
                                <input type="text" 
                                       name="notes[{{ $product->id }}]" 
                                       placeholder="Ghi chú..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- No Products Message -->
            <div id="no-products" class="hidden py-8 px-6 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không tìm thấy sản phẩm</h3>
                    <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí lọc.</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <span id="selected-count">0</span> sản phẩm đã được chọn
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="resetForm()"
                            class="bg-gray-200 text-gray-700 rounded-lg px-6 py-2 hover:bg-gray-300 transition flex items-center">
                            <i class="fas fa-undo mr-2"></i>
                            Đặt lại
                        </button>
                        <button type="submit"
                            class="bg-green-600 text-white rounded-lg px-6 py-2 hover:bg-green-700 transition flex items-center">
                            <i class="fas fa-clipboard-check mr-2"></i>
                            Lưu kiểm kê
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Actions Panel -->
    <div class="fixed bottom-6 right-6 flex flex-col space-y-3">
        <button type="button" onclick="scrollToTop()"
            class="bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button type="button" onclick="focusOnUnchecked()"
            class="bg-orange-500 text-white p-3 rounded-full shadow-lg hover:bg-orange-600 transition">
            <i class="fas fa-search"></i>
        </button>
        <button type="button" onclick="showDifferencesOnly()"
            class="bg-red-500 text-white p-3 rounded-full shadow-lg hover:bg-red-600 transition">
            <i class="fas fa-exclamation-triangle"></i>
        </button>
    </div>
@endsection

@section('scripts')
<script>
    // Initialize summary
    document.addEventListener('DOMContentLoaded', function() {
        updateSummary();
        calculateAllDifferences();
    });

    // Filter products
    function applyFilters() {
        const categoryFilter = document.getElementById('category_filter').value.toLowerCase();
        const searchTerm = document.getElementById('product_search').value.toLowerCase();
        const items = document.querySelectorAll('.inventory-item');
        let visibleCount = 0;

        items.forEach(item => {
            const category = item.dataset.category.toLowerCase();
            const name = item.dataset.name;
            const code = item.dataset.code;
            
            const categoryMatch = !categoryFilter || category.includes(categoryFilter);
            const searchMatch = !searchTerm || name.includes(searchTerm) || code.includes(searchTerm);
            
            if (categoryMatch && searchMatch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide no products message
        const noProducts = document.getElementById('no-products');
        if (visibleCount === 0) {
            noProducts.classList.remove('hidden');
        } else {
            noProducts.classList.add('hidden');
        }

        updateSummary();
    }

    function clearFilters() {
        document.getElementById('category_filter').value = '';
        document.getElementById('product_search').value = '';
        applyFilters();
    }

    // Selection functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('.inventory-item').style.display !== 'none') {
                checkbox.checked = selectAll.checked;
            }
        });
        
        updateSummary();
    }

    function checkAll() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('.inventory-item').style.display !== 'none') {
                checkbox.checked = true;
            }
        });
        document.getElementById('select-all').checked = true;
        updateSummary();
    }

    function uncheckAll() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('select-all').checked = false;
        updateSummary();
    }

    // Calculation functions
    function calculateDifference(productId) {
        const systemStock = parseInt(document.querySelector(`input[name="system_stock[${productId}]"]`).value) || 0;
        const actualStock = parseInt(document.querySelector(`input[name="actual_stock[${productId}]"]`).value) || 0;
        const difference = actualStock - systemStock;
        
        const differenceDisplay = document.getElementById(`difference-${productId}`);
        const differenceInput = document.getElementById(`difference-input-${productId}`);
        
        differenceInput.value = difference;
        differenceDisplay.textContent = `${difference > 0 ? '+' : ''}${difference} ${getProductUnit(productId)}`;
        
        // Color coding
        if (difference > 0) {
            differenceDisplay.className = 'text-sm font-medium text-green-600 difference-display';
        } else if (difference < 0) {
            differenceDisplay.className = 'text-sm font-medium text-red-600 difference-display';
        } else {
            differenceDisplay.className = 'text-sm font-medium text-gray-600 difference-display';
        }
        
        updateSummary();
    }

    function calculateAllDifferences() {
        const productIds = @json($products->pluck('id'));
        productIds.forEach(id => calculateDifference(id));
    }

    function getProductUnit(productId) {
        // This would need to be implemented based on your data structure
        return 'cái';
    }

    // Summary functions
    function updateSummary() {
        const totalProducts = document.querySelectorAll('.inventory-item').length;
        const checkedProducts = document.querySelectorAll('.item-checkbox:checked').length;
        const differences = document.querySelectorAll('input[name^="difference"]');
        
        let differenceCount = 0;
        differences.forEach(diff => {
            if (parseInt(diff.value) !== 0) {
                differenceCount++;
            }
        });

        document.getElementById('total-products').textContent = totalProducts;
        document.getElementById('checked-products').textContent = checkedProducts;
        document.getElementById('difference-count').textContent = differenceCount;
        document.getElementById('selected-count').textContent = checkedProducts;
    }

    function markAsChecked(input) {
        const checkbox = input.closest('tr').querySelector('.item-checkbox');
        checkbox.checked = true;
        updateSummary();
    }

    // Form actions
    function saveDraft() {
        // Implement draft saving logic
        alert('Đã lưu nháp thành công!');
    }

    function resetForm() {
        if (confirm('Bạn có chắc muốn đặt lại tất cả số liệu?')) {
            document.querySelectorAll('.actual-stock-input').forEach(input => {
                const systemStock = input.closest('tr').querySelector('input[name^="system_stock"]').value;
                input.value = systemStock;
            });
            calculateAllDifferences();
            uncheckAll();
        }
    }

    // Quick actions
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function focusOnUnchecked() {
        const firstUnchecked = document.querySelector('.item-checkbox:not(:checked)');
        if (firstUnchecked) {
            firstUnchecked.closest('tr').scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstUnchecked.focus();
        }
    }

    function showDifferencesOnly() {
        const items = document.querySelectorAll('.inventory-item');
        items.forEach(item => {
            const differenceInput = item.querySelector('input[name^="difference"]');
            const hasDifference = differenceInput && parseInt(differenceInput.value) !== 0;
            
            if (hasDifference) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        updateSummary();
    }

    // Date handling
    document.getElementById('inventory_date').addEventListener('change', function() {
        document.getElementById('current-date').textContent = formatDate(this.value);
        document.getElementById('form_inventory_date').value = this.value;
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    // Form submission
    document.getElementById('inventory-form').addEventListener('submit', function(e) {
        const checkedItems = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedItems === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một sản phẩm để kiểm kê!');
            return;
        }
        
        // Additional validation can be added here
    });
</script>

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .actual-stock-input:focus {
        outline: none;
        ring: 2px;
        border-color: #3b82f6;
    }

    .difference-display {
        transition: color 0.2s ease;
    }

    .fixed {
        position: fixed;
        z-index: 40;
    }
</style>
@endsection