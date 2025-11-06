@extends('admin.layouts.app')

@section('title', 'Thùng rác combo')

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Thùng rác combo</h1>
    <a href="{{ route('admin.combos.index') }}" 
       class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Quay lại
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-4 px-6 text-left text-sm font-medium text-gray-600">Tên combo</th>
                    <th class="py-4 px-6 text-left text-sm font-medium text-gray-600">Mã combo</th>
                    <th class="py-4 px-6 text-left text-sm font-medium text-gray-600">Ngày xóa</th>
                    <th class="py-4 px-6 text-left text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($combos as $combo)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">{{ $combo->name }}</td>
                        <td class="py-4 px-6">{{ $combo->combo_code }}</td>
                        <td class="py-4 px-6 text-gray-500">{{ $combo->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="py-4 px-6 flex space-x-3">
                            <form action="{{ route('admin.combos.restore', $combo->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800 transition">
                                    <i class="fas fa-undo mr-1"></i>Khôi phục
                                </button>
                            </form>

                            <form action="{{ route('admin.combos.forceDelete', $combo->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Xóa vĩnh viễn combo này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 transition">
                                    <i class="fas fa-trash-alt mr-1"></i>Xóa vĩnh viễn
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500">
                            Không có combo nào trong thùng rác.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($combos->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $combos->links() }}
        </div>
    @endif
</div>
@endsection
