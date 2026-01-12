<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supervisors') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm sm:text-base">
                + New Supervisor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse($users as $user)
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                            <div>
                                <span class="text-gray-500">Assigned Todos:</span>
                                <span class="font-medium ml-1">{{ $user->assigned_todos_count }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Reports:</span>
                                <span class="font-medium ml-1">{{ $user->daily_reports_count }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-1">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-3 border-t">
                            <a href="{{ route('users.show', $user) }}" class="flex-1 text-center text-blue-600 hover:text-blue-900 text-sm py-1">View</a>
                            <a href="{{ route('users.edit', $user) }}" class="flex-1 text-center text-indigo-600 hover:text-indigo-900 text-sm py-1">Edit</a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-red-600 hover:text-red-900 text-sm py-1" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                        No supervisors found.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Todos</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reports</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $user->assigned_todos_count }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $user->daily_reports_count }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No supervisors found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

