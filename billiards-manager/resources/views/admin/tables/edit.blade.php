@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 650px;">
        <div class="card-header bg-gradient bg-primary text-black text-center py-3 rounded-top-4">
            <h4 class="mb-0 fw-bold">
                 CHỈNH SỬA THÔNG TIN BÀN 
            </h4>
        </div>

        <div class="card-body p-4 bg-light rounded-bottom-4">
            <form method="POST" action="{{ route('admin.tables.update', $table->id) }}">
                @csrf
                @method('PUT')

                {{-- Tên bàn --}}
                <div class="mb-3 form-group">
                    <label class="form-label fw-semibold">Tên bàn</label>
                    <input type="text" name="table_name" value="{{ $table->table_name }}" 
                        class="form-control form-control-lg shadow-sm" placeholder="Nhập tên bàn..." required>
                </div>

                {{-- Mã bàn --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold"> Mã bàn</label>
                    <input type="text" name="table_number" value="{{ $table->table_number }}" 
                        class="form-control form-control-lg shadow-sm" placeholder="Nhập mã bàn (ví dụ: T01)" required>
                </div>

                {{-- Loại bàn --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold"> Loại bàn</label>
                    <select name="type" class="form-select form-select-lg shadow-sm" required>
                        <option value="standard" {{ $table->type == 'standard' ? 'selected' : '' }}>⭐ Standard</option>
                        <option value="vip" {{ $table->type == 'vip' ? 'selected' : '' }}>💎 VIP</option>
                        <option value="competition" {{ $table->type == 'competition' ? 'selected' : '' }}>🏆 Competition</option>
                    </select>
                </div>

                {{-- Giá/giờ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold"> Giá/giờ (VNĐ)</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-secondary text-white fw-bold">₫</span>
                        <input type="number" name="hourly_rate" value="{{ $table->hourly_rate }}" min="0"
                            class="form-control form-control-lg" placeholder="Nhập giá mỗi giờ" required>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold"> Trạng thái</label>
                    <select name="status" class="form-select form-select-lg shadow-sm" required>
                        <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>🟢 Trống</option>
                        <option value="maintenance" {{ $table->status == 'maintenance' ? 'selected' : '' }}>🟡 Bảo trì</option>
                    </select>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-3">
                        ⬅️ Quay lại
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-4 rounded-3 shadow">
                        💾 Cập nhật bàn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
