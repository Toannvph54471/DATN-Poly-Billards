@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới - F&B Management')

@section('content')
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
        <a href="{{ route('admin.products.index') }}"
            class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Thông tin cơ bản
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Nhập tên sản phẩm">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Mã sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="text" name="product_code" id="product_code" value="{{ old('product_code') }}" required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="SP0001">
                            <button type="button" onclick="generateProductCode()" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                                Tạo
                            </button>
                        </div>
                        @error('product_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Danh mục <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="product_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Loại sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <select name="product_type" id="product_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Chọn loại</option>
                            <option value="Service" {{ old('product_type') == 'Service' ? 'selected' : '' }}>Dịch vụ</option>
                            <option value="Consumption" {{ old('product_type') == 'Consumption' ? 'selected' : '' }}>Hàng tiêu dùng</option>
                        </select>
                        @error('product_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá bán <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" step="1000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="50000">
                        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá vốn
                        </label>
                        <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" min="0" step="1000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="30000">
                        @error('cost_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Số lượng tồn kho
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('stock_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Cảnh báo tồn kho tối thiểu
                        </label>
                        <input type="number" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', 5) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('min_stock_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Đơn vị <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="giờ, chai, gói">
                        @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng thái <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Đang kinh doanh</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Mô tả chi tiết sản phẩm...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Hình ảnh
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition"
                             onclick="document.getElementById('image').click()">
                            <input type="file" name="image" id="image" class="hidden" accept="image/*">
                            <div id="image-preview" class="hidden mt-4">
                                <img id="preview" class="mx-auto max-h-48 rounded-lg">
                            </div>
                            <p class="text-gray-500">Kéo thả hoặc click để tải ảnh lên</p>
                        </div>
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.products.index') }}"
                       class="bg-gray-200 text-gray-700 rounded-lg px-6 py-2 hover:bg-gray-300 transition">
                        Hủy
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
    function generateProductCode() {
        const prefix = 'SP';
        const timestamp = new Date().getTime().toString().slice(-4);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        document.getElementById('product_code').value = prefix + timestamp + random;
    }

    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                const container = document.getElementById('image-preview');
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection