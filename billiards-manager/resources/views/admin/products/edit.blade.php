@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa sản phẩm - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-xl flex items-center justify-center shadow-lg mr-4">
                <i class="fas fa-edit text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa sản phẩm</h1>
                <p class="text-gray-600">Cập nhật thông tin sản phẩm: {{ $product->name }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
            <button type="button" onclick="confirmDelete({{ $product->id }})"
                class="bg-red-600 text-white rounded-lg px-4 py-2 hover:bg-red-700 transition flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Xóa
            </button>
        </div>
    </div>

    <!-- Product Form -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
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
                        <input type="text" name="product_code" id="product_code" value="{{ old('product_code', $product->product_code) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-50"
                            placeholder="VD: SP001" required readonly>
                        <p class="mt-1 text-xs text-gray-500">Mã sản phẩm không thể thay đổi</p>
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
                            <option value="">{{ $product->category }}</option>
                            <option value="Đồ uống" {{ old('category', $product->category) == 'Đồ uống' ? 'selected' : '' }}>Đồ uống</option>
                            <option value="Đồ ăn" {{ old('category', $product->category) == 'Đồ ăn' ? 'selected' : '' }}>Đồ ăn</option>
                            <option value="Tráng miệng" {{ old('category', $product->category) == 'Tráng miệng' ? 'selected' : '' }}>Tráng miệng</option>
                            <option value="Khai vị" {{ old('category', $product->category) == 'Khai vị' ? 'selected' : '' }}>Khai vị</option>
                            <option value="Món chính" {{ old('category', $product->category) == 'Món chính' ? 'selected' : '' }}>Món chính</option>
                            <option value="Nguyên liệu" {{ old('category', $product->category) == 'Nguyên liệu' ? 'selected' : '' }}>Nguyên liệu</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="product_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Loại sản phẩm
                        </label>
@php
    $listType = config('constants.product.types', []);
@endphp

<select name="product_type" id="product_type"
    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    <option value="">-- Chọn loại sản phẩm --</option>

    @foreach ($listType as $type)
        <option value="{{ $type }}" {{ old('product_type', $product->product_type) == $type ? 'selected' : '' }}>
            {{ $type }}
        </option>
    @endforeach
</select>

@error('product_type')
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
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" step="1000"
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
                            <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="1000"
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
                            <option value="cái" {{ old('unit', $product->unit) == 'cái' ? 'selected' : '' }}>cái</option>
                            <option value="chai" {{ old('unit', $product->unit) == 'chai' ? 'selected' : '' }}>chai</option>
                            <option value="ly" {{ old('unit', $product->unit) == 'ly' ? 'selected' : '' }}>ly</option>
                            <option value="đĩa" {{ old('unit', $product->unit) == 'đĩa' ? 'selected' : '' }}>đĩa</option>
                            <option value="phần" {{ old('unit', $product->unit) == 'phần' ? 'selected' : '' }}>phần</option>
                            <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="g" {{ old('unit', $product->unit) == 'g' ? 'selected' : '' }}>g</option>
                            <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="lít" {{ old('unit', $product->unit) == 'lít' ? 'selected' : '' }}>lít</option>
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
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
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
                        <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0"
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
                                <input type="radio" name="is_available" value="1" {{ old('is_available', $product->is_available) == 1 ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Đang kinh doanh</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_available" value="0" {{ old('is_available', $product->is_available) == 0 ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Ngừng kinh doanh</span>
                            </label>
                        </div>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stock Alert -->
                @if($product->stock_quantity <= $product->min_stock && $product->min_stock > 0)
                <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-orange-800">Cảnh báo tồn kho thấp</h4>
                            <p class="text-sm text-orange-700 mt-1">
                                Tồn kho hiện tại ({{ $product->stock_quantity }}) đang ở mức thấp hơn hoặc bằng tồn kho tối thiểu ({{ $product->min_stock }})
                            </p>
                        </div>
                    </div>
                </div>
                @endif
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
                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Hình ảnh sản phẩm
                        </label>
                        
                        <!-- Current Image -->
                        @if($product->image)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Ảnh hiện tại:</p>
                            <div class="relative inline-block">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                    class="h-48 rounded-lg shadow-sm object-cover">
                                <button type="button" onclick="removeCurrentImage()"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả sản phẩm
                        </label>
                        <textarea name="description" id="description" rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
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
                        Cập nhật sản phẩm
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete Form -->
    <form id="delete-form-{{ $product->id }}" 
          action="  " 
          method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
<script>
    function confirmDelete(productId) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc chắn muốn xóa sản phẩm này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + productId).submit();
            }
        });
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

    function removeCurrentImage() {
        if (confirm('Bạn có chắc muốn xóa ảnh hiện tại?')) {
            // You can implement AJAX to remove image or add a hidden field to track image removal
            // For now, just reload the page without the image
            window.location.href = "{{ route('admin.products.edit', $product->id) }}?remove_image=1";
        }
    }

    // Auto calculate profit margin
    document.getElementById('price').addEventListener('input', calculateProfit);
    document.getElementById('cost_price').addEventListener('input', calculateProfit);

    function calculateProfit() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        
        if (price > 0 && costPrice > 0) {
            const profit = price - costPrice;
            const margin = ((profit / price) * 100).toFixed(1);
            
            // Display profit information
            console.log(`Lợi nhuận: ${profit.toLocaleString()}đ | Biên lợi nhuận: ${margin}%`);
        }
    }

    // Stock warning
    document.getElementById('stock_quantity').addEventListener('input', checkStockLevel);
    document.getElementById('min_stock').addEventListener('input', checkStockLevel);

    function checkStockLevel() {
        const stock = parseInt(document.getElementById('stock_quantity').value) || 0;
        const minStock = parseInt(document.getElementById('min_stock').value) || 0;
        
        if (minStock > 0 && stock <= minStock) {
            // You can add visual feedback here
            console.log('Cảnh báo: Tồn kho đang ở mức thấp!');
        }
    }
</script>
@endsection