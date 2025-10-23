@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-cubes text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Thêm sản phẩm mới</h1>
                <p class="text-gray-600">Thêm sản phẩm mới vào hệ thống quản lý</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Product Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route("admin.products.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Thông tin cơ bản
                    </h2>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nhập tên sản phẩm" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product Code -->
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Mã sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="product_code" id="product_code" value="{{ old('product_code') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="VD: SP001" required>
                            <button type="button" onclick="generateProductCode()" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-200 transition">
                                <i class="fas fa-sync-alt mr-1"></i> Tạo mã
                            </button>
                        </div>
                        @error('product_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Danh mục <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Chọn danh mục</option>
                            <option value="Đồ uống" {{ old('category') == 'Đồ uống' ? 'selected' : '' }}>Đồ uống</option>
                            <option value="Đồ ăn" {{ old('category') == 'Đồ ăn' ? 'selected' : '' }}>Đồ ăn</option>
                            <option value="Tráng miệng" {{ old('category') == 'Tráng miệng' ? 'selected' : '' }}>Tráng miệng</option>
                            <option value="Khai vị" {{ old('category') == 'Khai vị' ? 'selected' : '' }}>Khai vị</option>
                            <option value="Món chính" {{ old('category') == 'Món chính' ? 'selected' : '' }}>Món chính</option>
                            <option value="Nguyên liệu" {{ old('category') == 'Nguyên liệu' ? 'selected' : '' }}>Nguyên liệu</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Loại sản phẩm
                        </label>
                        <select name="type" id="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Chọn loại sản phẩm</option>
                            <option value="Thức uống" {{ old('type') == 'Thức uống' ? 'selected' : '' }}>Thức uống</option>
                            <option value="Đồ ăn nhanh" {{ old('type') == 'Đồ ăn nhanh' ? 'selected' : '' }}>Đồ ăn nhanh</option>
                            <option value="Món nướng" {{ old('type') == 'Món nướng' ? 'selected' : '' }}>Món nướng</option>
                            <option value="Món luộc" {{ old('type') == 'Món luộc' ? 'selected' : '' }}>Món luộc</option>
                            <option value="Món xào" {{ old('type') == 'Món xào' ? 'selected' : '' }}>Món xào</option>
                            <option value="Món hấp" {{ old('type') == 'Món hấp' ? 'selected' : '' }}>Món hấp</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory Section -->
            <div class="border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-tags text-green-500 mr-2"></i>
                        Giá cả & Tồn kho
                    </h2>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá bán <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">₫</span>
                            </div>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="1000"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="0" required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá vốn
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">₫</span>
                            </div>
                            <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" min="0" step="1000"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="0">
                        </div>
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Đơn vị tính <span class="text-red-500">*</span>
                        </label>
                        <select name="unit" id="unit"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Chọn đơn vị</option>
                            <option value="cái" {{ old('unit') == 'cái' ? 'selected' : '' }}>cái</option>
                            <option value="chai" {{ old('unit') == 'chai' ? 'selected' : '' }}>chai</option>
                            <option value="ly" {{ old('unit') == 'ly' ? 'selected' : '' }}>ly</option>
                            <option value="đĩa" {{ old('unit') == 'đĩa' ? 'selected' : '' }}>đĩa</option>
                            <option value="phần" {{ old('unit') == 'phần' ? 'selected' : '' }}>phần</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>g</option>
                            <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="lít" {{ old('unit') == 'lít' ? 'selected' : '' }}>lít</option>
                        </select>
                        @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Quantity -->
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Số lượng tồn kho
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="0">
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Min Stock -->
                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Tồn kho tối thiểu
                        </label>
                        <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="0">
                        @error('min_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div>
                        <label for="is_available" class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng thái
                        </label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="is_available" value="1" {{ old('is_available', 1) == 1 ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Đang kinh doanh</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_available" value="0" {{ old('is_available') == 0 ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Ngừng kinh doanh</span>
                            </label>
                        </div>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Image & Description Section -->
            <div class="border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-image text-purple-500 mr-2"></i>
                        Hình ảnh & Mô tả
                    </h2>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Hình ảnh sản phẩm
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Tải ảnh lên</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">hoặc kéo thả</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        PNG, JPG, GIF tối đa 10MB
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="image-preview" class="mt-3 hidden">
                            <img id="preview" class="max-w-full h-48 rounded-lg shadow-sm">
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả sản phẩm
                        </label>
                        <textarea name="description" id="description" rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.products.index') }}"
                        class="bg-gray-200 text-gray-700 rounded-lg px-6 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Hủy bỏ
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Lưu sản phẩm
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    // Generate product code
    function generateProductCode() {
        const prefix = 'SP';
        const timestamp = new Date().getTime().toString().slice(-4);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        document.getElementById('product_code').value = prefix + timestamp + random;
    }

    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('image-preview');
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto calculate profit margin
    document.getElementById('price').addEventListener('input', calculateProfit);
    document.getElementById('cost_price').addEventListener('input', calculateProfit);

    function calculateProfit() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        
        if (price > 0 && costPrice > 0) {
            const profit = price - costPrice;
            const margin = ((profit / price) * 100).toFixed(1);
            
            // You can display this information somewhere if needed
            console.log(`Lợi nhuận: ${profit.toLocaleString()}đ | Biên lợi nhuận: ${margin}%`);
        }
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const productCode = document.getElementById('product_code').value;
        const price = document.getElementById('price').value;
        
        if (!name || !productCode || !price) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ các trường bắt buộc (có dấu *)');
        }
    });
</script>
@endsection

<style>
    .border-dashed {
        border-style: dashed;
    }
    
    input:focus, select:focus, textarea:focus {
        outline: none;
        ring: 2px;
    }
    
    .transition {
        transition: all 0.2s ease-in-out;
    }
</style>