<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supervisor Details') }} - {{ $user->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">User Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-lg font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-lg font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Role</p>
                        <p class="text-lg font-medium">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Created At</p>
                        <p class="text-lg font-medium">{{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assigned Todos -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Assigned Todos ({{ $user->assignedTodos->count() }})</h3>
                    @if($user->assignedTodos->count() > 0)
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($user->assignedTodos as $todo)
                                <div class="border p-3 rounded-md">
                                    <p class="font-medium">{{ $todo->title }}</p>
                                    <p class="text-sm text-gray-600">
                                        Type: {{ ucfirst(str_replace('_', ' ', $todo->type)) }}
                                    </p>
                                    @if($todo->due_date)
                                        <p class="text-sm text-red-600">
                                            Due: {{ $todo->due_date->format('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No assigned todos</p>
                    @endif
                </div>

                <!-- Daily Reports -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Daily Reports ({{ $user->dailyReports->count() }})</h3>
                    @if($user->dailyReports->count() > 0)
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($user->dailyReports->take(10) as $report)
                                <div class="border p-3 rounded-md">
                                    <p class="font-medium">{{ $report->report_date->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-600">
                                        Todo: {{ $report->todoList->title }}
                                    </p>
                                    <p class="text-sm">
                                        Status: 
                                        <span class="px-2 py-1 text-xs rounded {{ $report->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </p>
                                </div>
                            @endforeach
                            @if($user->dailyReports->count() > 10)
                                <p class="text-sm text-gray-500 text-center">... and {{ $user->dailyReports->count() - 10 }} more</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No reports yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

