<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse($reports as $report)
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-gray-900">{{ $report->todoList->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded {{ $report->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        <div class="text-sm mb-3">
                            <span class="text-gray-500">Date:</span>
                            <span class="ml-1 font-medium">{{ $report->report_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex gap-2 pt-3 border-t">
                            <a href="{{ route('daily-reports.show', $report) }}" class="flex-1 text-center text-blue-600 hover:text-blue-900 text-sm py-1">View</a>
                            @if($report->supervisor_id === auth()->id())
                                <a href="{{ route('daily-reports.edit', $report) }}" class="flex-1 text-center text-indigo-600 hover:text-indigo-900 text-sm py-1">Edit</a>
                            @endif
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
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">{{ $report->report_date->format('d M Y') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">{{ $report->todoList->title }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded {{ $report->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('daily-reports.show', $report) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            @if($report->supervisor_id === auth()->id())
                                                <a href="{{ route('daily-reports.edit', $report) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No reports found.</td>
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

