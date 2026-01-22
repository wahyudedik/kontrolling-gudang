<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(Auth::user()->isSuperAdmin())
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 text-sm mb-1">Total To Do Lists</div>
                        <div class="text-3xl font-bold">{{ $stats['total_todos'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 text-sm mb-1">Total Reports</div>
                        <div class="text-3xl font-bold">{{ $stats['total_reports'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 text-sm mb-1">Active Habits</div>
                        <div class="text-3xl font-bold text-green-600">{{ $stats['active_habits'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 text-sm mb-1">Supervisors</div>
                        <div class="text-3xl font-bold">{{ $stats['total_supervisors'] }}</div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Habit Compliance Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Daily Habit Compliance (Last 7 Days)</h3>
                        <canvas id="complianceChart" height="200"></canvas>
                    </div>
                    
                    <!-- Warehouse Cleanliness Trend -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Warehouse Cleanliness Trend (Avg Score)</h3>
                        <canvas id="cleanlinessChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Bottom Row: Quick Actions & Leaderboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('todo-lists.create') }}" class="block p-3 border rounded-lg hover:bg-gray-50 transition flex items-center justify-between">
                                <span>Create New To Do List</span>
                                <span class="text-blue-500">&rarr;</span>
                            </a>
                            <a href="{{ route('reports.index') }}" class="block p-3 border rounded-lg hover:bg-gray-50 transition flex items-center justify-between">
                                <span>View Reports</span>
                                <span class="text-blue-500">&rarr;</span>
                            </a>
                            <a href="{{ route('users.index') }}" class="block p-3 border rounded-lg hover:bg-gray-50 transition flex items-center justify-between">
                                <span>Manage Users</span>
                                <span class="text-blue-500">&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Top Supervisors Leaderboard -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Top Supervisors (Most Consistent)</h3>
                        @if(count($leaderboard) > 0)
                            <div class="space-y-4">
                                @foreach($leaderboard as $index => $supervisor)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $supervisor['name'] }}</div>
                                                <div class="text-xs text-gray-500">Completed 3x Daily Habits</div>
                                            </div>
                                        </div>
                                        <div class="font-bold text-gray-700">
                                            {{ $supervisor['score'] }} Days
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No data available yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Chart.js Script -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const dates = @json($dates);
                    const complianceData = @json($complianceData);
                    const cleanlinessData = @json($cleanlinessData);

                    // Compliance Chart
                    new Chart(document.getElementById('complianceChart'), {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Compliance Rate (%)',
                                data: complianceData,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });

                    // Cleanliness Chart
                    new Chart(document.getElementById('cleanlinessChart'), {
                        type: 'bar',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Avg Cleanliness Score (1-5)',
                                data: cleanlinessData,
                                backgroundColor: 'rgb(59, 130, 246)',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5
                                }
                            }
                        }
                    });
                </script>

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
