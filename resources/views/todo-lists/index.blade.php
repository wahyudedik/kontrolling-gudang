<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('To Do Lists') }}
            </h2>
            <a href="{{ route('todo-lists.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                + New Todo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <form method="GET" class="flex gap-4">
                    <select name="type" class="rounded-md border-gray-300">
                        <option value="">All Types</option>
                        <option value="man_power" {{ request('type') == 'man_power' ? 'selected' : '' }}>Man Power</option>
                        <option value="finish_good" {{ request('type') == 'finish_good' ? 'selected' : '' }}>Finish Good</option>
                        <option value="raw_material" {{ request('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="gudang" {{ request('type') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                        <option value="supplier_datang" {{ request('type') == 'supplier_datang' ? 'selected' : '' }}>Supplier Datang</option>
                    </select>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($todoLists as $todo)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $todo->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeLabels = [
                                            'man_power' => 'Man Power',
                                            'finish_good' => 'Finish Good',
                                            'raw_material' => 'Raw Material',
                                            'gudang' => 'Gudang',
                                            'supplier_datang' => 'Supplier Datang',
                                        ];
                                    @endphp
                                    {{ $typeLabels[$todo->type] ?? ucfirst($todo->type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $todo->date ? $todo->date->format('d M Y') : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($todo->due_date)
                                        @php
                                            $today = now()->startOfDay();
                                            $dueDate = \Carbon\Carbon::parse($todo->due_date)->startOfDay();
                                            $daysDiff = $today->diffInDays($dueDate, false);
                                            $isOverdue = $today->gt($dueDate);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : ($daysDiff <= 2 ? 'text-yellow-600' : 'text-gray-600') }}">
                                                {{ $todo->due_date->format('d M Y') }}
                                            </span>
                                            @if($isOverdue)
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                                    Telat {{ abs($daysDiff) }} hari
                                                </span>
                                            @elseif($daysDiff <= 2 && $daysDiff >= 0)
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
                                                    @if($daysDiff == 0)
                                                        Deadline hari ini!
                                                    @else
                                                        {{ $daysDiff }} hari lagi
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $todo->createdBy->name }}</td>
                                <td class="px-6 py-4">
                                    @if($todo->supervisors->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($todo->supervisors as $supervisor)
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    {{ $supervisor->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded {{ $todo->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $todo->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('todo-lists.edit', $todo) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form method="POST" action="{{ route('todo-lists.destroy', $todo) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No To Do Lists found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $todoLists->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

