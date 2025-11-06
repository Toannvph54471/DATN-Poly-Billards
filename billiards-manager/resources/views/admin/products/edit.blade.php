@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa sản phẩm - F&B Management')

@section('content')
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

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

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
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Mã sản phẩm <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="product_code" id="product_code" value="{{ old('product_code', $product->product_code) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                                <option value="{{ $id }}" {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
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
                            <option value="Service" {{ old('product_type', $product->product_type) == 'Service' ? 'selected' : '' }}>Dịch vụ</option>
                            <option value="Consumption" {{ old('product_type', $product->product_type) == 'Consumption' ? 'selected' : '' }}>Hàng tiêu dùng</option>
                        </select>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá bán <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0" step="1000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá vốn
                        </label>
                        <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="1000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('cost_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Số lượng tồn kho
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('stock_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Cảnh báo tồn kho tối thiểu
                        </label>
                        <input type="number" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('min_stock_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Đơn vị <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $product->unit) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng thái <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Active" {{ old('status', $product->status) == 'Active' ? 'selected' : '' }}>Đang kinh doanh</option>
                            <option value="Inactive" {{ old('status', $product->status) == 'Inactive' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Hình ảnh
                        </label>
                        @if($product->image)
                            <div class="flex items-center space-x-4 mb-4">
                                <img src="{{ asset('storage/' . $product->image) }}" class="h-24 w-24 rounded-lg object-cover">
                                <button type="button" onclick="removeCurrentImage()" class="text-red-600 hover:text-red-800">
                                    Xóa ảnh
                                </button>
                            </div>
                        @endif
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
                        Cập nhật
                    </button>
                </div>
            </div>
        </form>
    </div>
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

    function removeCurrentImage() {
        if (confirm('Xóa ảnh hiện tại?')) {
            window.location.href = "{{ route('admin.products.edit', $product->id) }}?remove_image=1";
        }
    }
</script>
@endsection