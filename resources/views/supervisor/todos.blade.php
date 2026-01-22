<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('To Do Lists') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('supervisor.todos') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Due Date') }}</label>
                        <input type="date" name="due_date" value="{{ request('due_date') }}"
                            class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Task') }}</label>
                        <select name="task_id" class="w-full rounded-md border-gray-300">
                            <option value="">{{ __('All Tasks') }}</option>
                            @foreach ($allAssignedTodos as $todo)
                                <option value="{{ $todo->id }}"
                                    {{ request('task_id') == $todo->id ? 'selected' : '' }}>
                                    {{ $todo->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Message -->
            @if ($todos->count() === 0 && !request()->has('due_date') && !request()->has('task_id'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-blue-800">
                        <strong>Info:</strong>
                        {{ __('Semua todo untuk hari ini sudah diisi. Todo yang sudah diisi tidak akan muncul di daftar ini.') }}
                    </p>
                </div>
            @endif

            <!-- Todo Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todos as $todo)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">{{ $todo->title }}</h3>
                            <p class="text-sm text-gray-600 mb-2">
                                {{ __('Type') }}: <span class="font-medium">
                                    @php
                                        $typeLabels = [
                                            'man_power' => __('Man Power'),
                                            'finish_good' => __('Finish Good'),
                                            'raw_material' => __('Raw Material'),
                                            'gudang' => __('Gudang'),
                                            'supplier_datang' => __('Supplier Datang'),
                                            'daily' => __('Habit Harian'),
                                        ];
                                    @endphp
                                    {{ $typeLabels[$todo->type] ?? ucfirst($todo->type) }}
                                </span>
                            </p>

                            @if ($todo->type === 'daily')
                                <div class="mt-2 mb-4">
                                    <p class="text-xs text-gray-500 mb-1">Progress Hari Ini:</p>
                                    <div class="flex space-x-2">
                                        @php
                                            // Check dailyReports for this todo (loaded in controller)
                                            $reports = $todo->dailyReports ?? collect();
                                            $morning = $reports->where('session', 'morning')->first();
                                            $afternoon = $reports->where('session', 'afternoon')->first();
                                            $evening = $reports->where('session', 'evening')->first();
                                        @endphp

                                        <span
                                            class="px-2 py-1 text-xs rounded {{ $morning ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ __('Morning') }} {{ $morning ? '✓' : '○' }}
                                        </span>
                                        <span
                                            class="px-2 py-1 text-xs rounded {{ $afternoon ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ __('Afternoon') }} {{ $afternoon ? '✓' : '○' }}
                                        </span>
                                        <span
                                            class="px-2 py-1 text-xs rounded {{ $evening ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ __('Evening') }} {{ $evening ? '✓' : '○' }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if ($todo->date)
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ __('Date') }}: <span
                                        class="font-medium">{{ $todo->date->format('d M Y') }}</span>
                                </p>
                            @endif
                            @if ($todo->due_date)
                                @php
                                    $today = now()->startOfDay();
                                    $dueDate = \Carbon\Carbon::parse($todo->due_date)->startOfDay();
                                    $daysDiff = $today->diffInDays($dueDate, false);
                                    $isOverdue = $today->gt($dueDate);
                                @endphp
                                <p class="text-sm mb-2">
                                    Due Date:
                                    <span
                                        class="font-medium {{ $isOverdue ? 'text-red-600' : ($daysDiff <= 2 ? 'text-yellow-600' : 'text-gray-600') }}">
                                        {{ $todo->due_date->format('d M Y') }}
                                    </span>
                                    @if ($isOverdue)
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                            Telat {{ abs($daysDiff) }} hari
                                        </span>
                                    @elseif($daysDiff <= 2 && $daysDiff >= 0)
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
                                            @if ($daysDiff == 0)
                                                Deadline hari ini!
                                            @else
                                                {{ $daysDiff }} hari lagi
                                            @endif
                                        </span>
                                    @elseif($daysDiff > 2)
                                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                            Masih {{ $daysDiff }} hari
                                        </span>
                                    @endif
                                </p>
                            @endif
                            <p class="text-sm text-gray-600 mb-4">
                                Created by: <span class="font-medium">{{ $todo->createdBy->name }}</span>
                            </p>
                            <a href="{{ route('daily-reports.create', $todo) }}"
                                class="block w-full text-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Isi Report
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">No To Do Lists available.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $todos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
