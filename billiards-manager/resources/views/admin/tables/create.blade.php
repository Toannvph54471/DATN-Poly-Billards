@extends('admin.layouts.app')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <div class="flex items-center mb-6">
            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
            <h2 class="text-2xl font-bold text-gray-800">Thêm bàn mới</h2>
        </div>

        {{-- Thông báo thành công --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- Form thêm bàn --}}
        <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Mã bàn --}}
                <div>
                    <label for="table_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Mã bàn <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="table_number" id="table_number"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        placeholder="VD: T01, VIP01..." value="{{ old('table_number') }}" required>
                    @error('table_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tên bàn --}}
                <div>
                    <label for="table_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên bàn <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="table_name" id="table_name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        placeholder="VD: Bàn VIP 1" value="{{ old('table_name') }}" required>
                    @error('table_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Loại bàn --}}
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                        Loại bàn <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" required>
                        <option value="">-- Chọn loại bàn --</option>
                        <option value="standard" {{ old('type') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="competition" {{ old('type') == 'competition' ? 'selected' : '' }}>Thi đấu</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Giá theo giờ --}}
                <div>
                    <label for="hourly_rate" class="block text-sm font-semibold text-gray-700 mb-2">
                        Giá theo giờ (VNĐ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="hourly_rate" id="hourly_rate"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        placeholder="VD: 50000" value="{{ old('hourly_rate') }}" required>
                    @error('hourly_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.tables.index') }}"
                    class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Lưu bàn
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
