@extends('layouts.customer')

@section('content')
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-8 border border-gray-100">

        {{-- Tiêu đề --}}
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-yellow-500 pl-4">
            Chỉnh sửa thông tin
        </h2>

        {{-- Thông báo thành công --}}
        @if (session('success'))
            <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Thông báo lỗi --}}
        @if ($errors->any())
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            {{-- <div class="flex items-center gap-6 mb-8">
            <img id="avatarPreview"
                 src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                 class="w-24 h-24 rounded-full border-4 border-yellow-500 shadow-md object-cover">

            <div class="flex-1">
                <label class="font-semibold text-gray-700">Ảnh đại diện</label>
                <input type="file" name="avatar" id="avatarInput"
                       class="block w-full mt-2 text-sm border rounded-lg p-2 cursor-pointer bg-gray-50">
                <p class="text-gray-500 text-sm mt-1">Hỗ trợ: JPG, PNG — tối đa 2MB</p>
            </div>
        </div> --}}

            {{-- Họ tên --}}
            <div class="mb-5">
                <label class="font-semibold text-gray-700">Họ tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full mt-2 rounded-lg border-gray-300 p-3 focus:ring focus:ring-blue-300">
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label class="font-semibold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full mt-2 rounded-lg border-gray-300 p-3 focus:ring focus:ring-blue-300">
            </div>

            {{-- Số điện thoại --}}
            <div class="mb-5">
                <label class="font-semibold text-gray-700">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full mt-2 rounded-lg border-gray-300 p-3 focus:ring focus:ring-blue-300">
            </div>

            {{-- Mật khẩu --}}
            <div class="mb-5">
                <label class="font-semibold text-gray-700">Mật khẩu mới (tùy chọn)</label>
                <input type="password" name="password"
                    class="w-full mt-2 rounded-lg border-gray-300 p-3 focus:ring focus:ring-blue-300"
                    placeholder="Nhập mật khẩu mới">
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div class="mb-8">
                <label class="font-semibold text-gray-700">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-2 rounded-lg border-gray-300 p-3 focus:ring focus:ring-blue-300"
                    placeholder="Nhập lại mật khẩu">
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ url()->previous() }}"
                    class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg shadow hover:bg-gray-400 transition font-semibold">
                    Quay lại
                </a>

                <button
                    class="bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-800 transition font-semibold">
                    Cập nhật thông tin
                </button>
            </div>


        </form>
    </div>

    {{-- JS Preview Avatar --}}
    {{-- <script>
document.getElementById('avatarInput').addEventListener('change', function(event) {
    let reader = new FileReader();
    reader.onload = function(){
        document.getElementById('avatarPreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
});
</script> --}}
@endsection
