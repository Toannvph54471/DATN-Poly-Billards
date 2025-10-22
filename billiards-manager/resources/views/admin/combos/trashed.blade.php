@extends('admin.layouts.app')

@section('title', 'Combo đã xóa')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Danh sách combo đã xóa mềm</h2>

    <a href="{{ route('admin.combos.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Quay lại danh sách
    </a>

    <table class="w-full text-sm border">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Mã Combo</th>
                <th class="px-4 py-2 text-left">Tên</th>
                <th class="px-4 py-2 text-left">Giá</th>
                <th class="px-4 py-2 text-left">Ngày xóa</th>
                <th class="px-4 py-2 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($combos as $combo)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $combo->combo_code }}</td>
                    <td class="px-4 py-2">{{ $combo->name }}</td>
                    <td class="px-4 py-2">{{ number_format($combo->price, 0, ',', '.') }} đ</td>
                    <td class="px-4 py-2">{{ $combo->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 text-right">
                        <form action="{{ route('admin.combos.restore', $combo->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:underline">Khôi phục</button>
                        </form>

                        <form action="{{ route('admin.combos.forceDelete', $combo->id) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Không có combo nào đã xóa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $combos->links('pagination::tailwind') }}</div>
</div>
@endsection
