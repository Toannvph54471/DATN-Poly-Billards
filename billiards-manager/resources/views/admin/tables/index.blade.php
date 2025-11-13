@extends('admin.layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Table Management</h1>
        <p class="text-gray-600">Manage your pool tables and their status</p>
    </div>

    <!-- Filters và Search -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.tables.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Search tables...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach ($tableTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.tables.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
                <a href="{{ route('admin.tables.create') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 ml-auto">
                    <i class="fas fa-plus mr-2"></i>Add New Table
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách bàn -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if ($tables->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table
                                Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hourly Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($tables as $table)
                            <tr class="hover:bg-gray-50 group relative cursor-pointer"
                                onclick="window.location='{{ route('admin.tables.detail', $table) }}'">

                                <!-- Các cột dữ liệu -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $table->table_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $table->table_name }}</div>
                                    @if ($table->description)
                                        <div class="text-xs text-gray-400 mt-1">{{ Str::limit($table->description, 50) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $table->getCategoryName() }}</span>
                                </td>

                                <td class="px-6- py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>{{ $table->capacity }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            App\Models\Table::TYPE_STANDARD => 'bg-gray-100 text-gray-800',
                                            App\Models\Table::TYPE_VIP => 'bg-purple-100 text-purple-800',
                                            App\Models\Table::TYPE_COMPETITION => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$table->type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $tableTypes[$table->type] ?? $table->type }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            App\Models\Table::STATUS_AVAILABLE => 'bg-green-100 text-green-800',
                                            App\Models\Table::STATUS_OCCUPIED => 'bg-red-100 text-red-800',
                                            App\Models\Table::STATUS_PAUSED => 'bg-yellow-100 text-yellow-800',
                                            App\Models\Table::STATUS_MAINTENANCE => 'bg-gray-100 text-gray-800',
                                            App\Models\Table::STATUS_RESERVED => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$table->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statuses[$table->status] ?? $table->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($table->getHourlyRate(), 2) }}/hour
                                </td>

                                <!-- Cột Actions - Cần xử lý riêng để không bị ảnh hưởng bởi click -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2" onclick="event.stopPropagation()">
                                        <a href="{{ route('admin.tables.detail', $table) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.tables.edit', $table) }}"
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if ($table->isAvailable())
                                            <form action="{{ route('admin.tables.update', $table) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="status"
                                                    value="{{ App\Models\Table::STATUS_OCCUPIED }}">
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Mark as Occupied" onclick="event.stopPropagation()">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @elseif($table->status === App\Models\Table::STATUS_OCCUPIED)
                                            <form action="{{ route('admin.tables.update', $table) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="status"
                                                    value="{{ App\Models\Table::STATUS_AVAILABLE }}">
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                    title="Mark as Available" onclick="event.stopPropagation()">
                                                    <i class="fas fa-stop"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.tables.destroy', $table) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this table?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                onclick="event.stopPropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $tables->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-table fa-3x text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No tables found</h3>
                <p class="text-gray-500 mt-2">Get started by creating a new table.</p>
                <a href="{{ route('admin.tables.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4">
                    <i class="fas fa-plus mr-2"></i>Add New Table
                </a>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" id="success-message">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('success-message').remove();
            }, 3000);
        </script>
    @endif

    @if (session('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg" id="error-message">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('error-message').remove();
            }, 5000);
        </script>
    @endif

    @push('styles')
        <style>
            /* Hiệu ứng hover cho toàn bộ hàng */
            tr[onclick]:hover {
                background-color: #f3f4f6 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.2s ease-in-out;
            }

            /* Con trỏ chuột cho toàn bộ hàng */
            tr[onclick] {
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }

            /* Đảm bảo các link trong actions không bị ảnh hưởng */
            tr[onclick] .flex.space-x-2 a,
            tr[onclick] .flex.space-x-2 button,
            tr[onclick] .flex.space-x-2 form {
                position: relative;
                z-index: 10;
            }
        </style>
    @endpush
@endsection
