@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-8">
    <div class="mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-8 py-6">
                <div class="flex items-center justify-center space-x-3">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">CHỈNH SỬA THÔNG TIN BÀN</h2>
                </div>
                <p class="text-blue-100 text-center mt-2">Cập nhật thông tin chi tiết về bàn</p>
            </div>

            <!-- Card Body -->
            <div class="p-8 bg-white">
                <form method="POST" action="{{ route('admin.tables.update', $table->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tên bàn -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-table mr-2 text-blue-500"></i>
                                Tên bàn
                            </label>
                            <div class="relative">
                                <input type="text" name="table_name" value="{{ $table->table_name }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Nhập tên bàn..." required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-pencil-alt text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Mã bàn -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                                Mã bàn
                            </label>
                            <div class="relative">
                                <input type="text" name="table_number" value="{{ $table->table_number }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="T01, T02..." required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Loại bàn -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-star mr-2 text-blue-500"></i>
                                Loại bàn
                            </label>
                            <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                <option value="standard" {{ $table->type == 'standard' ? 'selected' : '' }}>
                                    ⭐ Standard
                                </option>
                                <option value="vip" {{ $table->type == 'vip' ? 'selected' : '' }}>
                                    💎 VIP
                                </option>
                                <option value="competition" {{ $table->type == 'competition' ? 'selected' : '' }}>
                                    🏆 Competition
                                </option>
                            </select>
                        </div>

                        <!-- Giá/giờ -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-money-bill-wave mr-2 text-blue-500"></i>
                                Giá/giờ (VNĐ)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">₫</span>
                                </div>
                                <input type="number" name="hourly_rate" value="{{ $table->hourly_rate }}" min="0"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="0" required>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-circle mr-2 text-blue-500"></i>
                                Trạng thái
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative">
                                    <input type="radio" name="status" value="available" {{ $table->status == 'available' ? 'checked' : '' }} 
                                        class="hidden peer" required>
                                    <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                            <span class="font-medium text-gray-700">🟢 Trống</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Bàn đang sẵn sàng sử dụng</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="status" value="maintenance" {{ $table->status == 'maintenance' ? 'checked' : '' }}
                                        class="hidden peer" required>
                                    <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                            <span class="font-medium text-gray-700">🟡 Bảo trì</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Bàn đang được bảo trì</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <a href="{{ route('admin.tables.index') }}" 
                           class="flex items-center space-x-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                            <i class="fas fa-arrow-left"></i>
                            <span>Quay lại</span>
                        </a>
                        
                        <div class="flex space-x-3">
                            <button type="reset" 
                                    class="flex items-center space-x-2 px-6 py-3 border border-red-300 text-red-600 rounded-xl hover:bg-red-50 transition-all duration-200 font-medium">
                                <i class="fas fa-redo"></i>
                                <span>Đặt lại</span>
                            </button>
                            <button type="submit" 
                                    class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium shadow-md">
                                <i class="fas fa-save"></i>
                                <span>Cập nhật bàn</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
