<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->isSuperAdmin())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <a href="{{ route('todo-lists.create') }}" class="block mb-2 text-blue-600 hover:text-blue-800">
                                + Create New To Do List
                            </a>
                            <a href="{{ route('reports.index') }}" class="block text-blue-600 hover:text-blue-800">
                                View Reports
                            </a>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
                            <p class="text-gray-600">Total To Do Lists: {{ \App\Models\TodoList::count() }}</p>
                            <p class="text-gray-600">Total Reports: {{ \App\Models\DailyReport::count() }}</p>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->isSupervisor())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::user()->name }}!</h3>
                        <p class="mb-4">You can view and fill To Do Lists from the menu above.</p>
                        <a href="{{ route('supervisor.todos') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            View To Dos
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
