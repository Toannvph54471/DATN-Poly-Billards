@extends('admin.layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-8">
        <div class="mx-auto max-w-4xl">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white">THÊM BÀN MỚI</h2>
                    </div>
                    <p class="text-green-100 text-center mt-2">Nhập thông tin chi tiết để thêm bàn mới vào hệ thống</p>
                </div>

                <div class="p-8 bg-white">
                    <form method="POST" action="{{ route('admin.tables.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tên bàn -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tên bàn</label>
                                <input type="text" name="table_name" value="{{ old('table_name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Nhập tên bàn..." required>
                                @error('table_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mã bàn -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Mã bàn</label>
                                <input type="text" name="table_number" value="{{ old('table_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="T01, T02..." required>
                                @error('table_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- Loại bàn --}}
                            <div class="mb-4">
                                <label for="table_rate_id" class="block text-gray-700 font-medium">Loại bàn / Bảng
                                    giá</label>
                                <select name="table_rate_id" id="table_rate_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Chọn loại bàn</option>
                                    @foreach ($tableRates as $rate)
                                        <option value="{{ $rate->id }}"
                                            {{ old('table_rate_id', $table->table_rate_id ?? '') == $rate->id ? 'selected' : '' }}>
                                            {{ $rate->name }} - {{ number_format($rate->hourly_rate, 0, ',', '.') }}đ/giờ
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Sức chứa -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Sức chứa</label>
                                <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Số người tối đa" required>
                                @error('capacity')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <!-- Trạng thái -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700">Trạng thái</label>
                                <select name="status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                    <option value="available"
                                        {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available
                                    </option>
                                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied
                                    </option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                        Maintenance</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
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
                                    class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-200 font-medium shadow-md">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Thêm bàn mới</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
