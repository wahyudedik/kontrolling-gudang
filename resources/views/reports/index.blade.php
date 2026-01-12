<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reports') }}
            </h2>
            <a href="{{ route('reports.export', request()->all()) }}"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Export Excel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                    <div class="col-span-full flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reports Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To Do List</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">{{ $report->report_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $report->todoList->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($dueDate)
                                        <div class="flex items-center gap-2">
                                            <span class="{{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
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
                                <td class="px-6 py-4 whitespace-nowrap">{{ $report->supervisor->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs rounded {{ $report->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
