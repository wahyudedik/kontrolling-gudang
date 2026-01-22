<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reports') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.export', request()->all()) }}"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm sm:text-base">
                    Export Excel
                </a>
                <a href="{{ route('reports.export_pdf', request()->all()) }}"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm sm:text-base">
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6 mb-6">
                @php
                    $hasActiveFilters = request('due_date') || request('task_id') || request('supervisor_id') || request('from_date') || request('to_date');
                @endphp
                @if($hasActiveFilters)
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-800">Filter aktif</span>
                        </div>
                        <a href="{{ route('reports.index') }}" 
                            class="text-sm text-blue-600 hover:text-blue-800 underline">
                            Clear All
                        </a>
                    </div>
                @endif
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ request('due_date') }}"
                            class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task</label>
                        <select name="task_id" class="w-full rounded-md border-gray-300">
                            <option value="">All Tasks</option>
                            @foreach ($todoLists as $todo)
                                <option value="{{ $todo->id }}"
                                    {{ request('task_id') == $todo->id ? 'selected' : '' }}>
                                    {{ $todo->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama SPV</label>
                        <select name="supervisor_id" class="w-full rounded-md border-gray-300">
                            <option value="">All Supervisors</option>
                            @foreach ($supervisors as $spv)
                                <option value="{{ $spv->id }}"
                                    {{ request('supervisor_id') == $spv->id ? 'selected' : '' }}>
                                    {{ $spv->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                            class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                            class="w-full rounded-md border-gray-300">
                    </div>
                    <div class="col-span-full flex justify-end gap-2">
                        <a href="{{ route('reports.index') }}" 
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Clear Filter
                        </a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse($reports as $report)
                    @php
                        $todoList = $report->todoList;
                        $today = now()->startOfDay();
                        $dueDate = $todoList->due_date
                            ? \Carbon\Carbon::parse($todoList->due_date)->startOfDay()
                            : null;
                        $reportDate = \Carbon\Carbon::parse($report->report_date)->startOfDay();

                        $isOverdue = $dueDate && $reportDate->gt($dueDate);
                        $daysLate = $dueDate && $isOverdue ? $reportDate->diffInDays($dueDate) : 0;
                    @endphp
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-gray-900">{{ $report->todoList->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded {{ $report->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="ml-1 font-medium">{{ $report->report_date->format('d M Y') }}</span>
                            </div>
                            @if ($dueDate)
                                <div>
                                    <span class="text-gray-500">Due Date:</span>
                                    <span class="ml-1 {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ $dueDate->format('d M Y') }}
                                    </span>
                                    @if ($isOverdue)
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                            Telat {{ $daysLate }} hari
                                        </span>
                                    @elseif($reportDate->lte($dueDate))
                                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                            Tepat waktu
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-500">Supervisor:</span>
                                <span class="ml-1">{{ $report->supervisor->name }}</span>
                            </div>
                        </div>
                        <div class="pt-3 border-t">
                            <a href="{{ route('daily-reports.show', $report) }}" class="block text-center text-blue-600 hover:text-blue-900 text-sm py-1">
                                View Report
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                        No reports found.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To Do List</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                @php
                                    $todoList = $report->todoList;
                                    $today = now()->startOfDay();
                                    $dueDate = $todoList->due_date
                                        ? \Carbon\Carbon::parse($todoList->due_date)->startOfDay()
                                        : null;
                                    $reportDate = \Carbon\Carbon::parse($report->report_date)->startOfDay();

                                    $isOverdue = $dueDate && $reportDate->gt($dueDate);
                                    $daysLate = $dueDate && $isOverdue ? $reportDate->diffInDays($dueDate) : 0;
                                @endphp
                                <tr>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">{{ $report->report_date->format('d M Y') }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">{{ $report->todoList->title }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        @if ($dueDate)
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                                    {{ $dueDate->format('d M Y') }}
                                                </span>
                                                @if ($isOverdue)
                                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded"
                                                        title="Report dikirim {{ $daysLate }} hari setelah due date">
                                                        Telat {{ $daysLate }} hari
                                                    </span>
                                                @elseif($reportDate->lte($dueDate))
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                        Tepat waktu
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">{{ $report->supervisor->name }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs rounded {{ $report->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('daily-reports.show', $report) }}"
                                            class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No reports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
