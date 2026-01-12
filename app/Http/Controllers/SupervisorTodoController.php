<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterTodoRequest;
use App\Models\TodoList;
use Illuminate\View\View;

class SupervisorTodoController extends Controller
{
    /**
     * Display todos in card layout for supervisor
     */
    public function index(FilterTodoRequest $request): View
    {
        $supervisorId = $request->user()->id;
        $today = now()->format('Y-m-d');

        // Only show todos assigned to the logged-in supervisor
        // Exclude todos that already have a report for today
        $query = TodoList::with('createdBy')
            ->whereHas('supervisors', function ($q) use ($supervisorId) {
                $q->where('users.id', $supervisorId);
            })
            ->whereDoesntHave('dailyReports', function ($q) use ($supervisorId, $today) {
                $q->where('supervisor_id', $supervisorId)
                    ->whereDate('report_date', $today);
            })
            ->active()
            ->latest();

        // Filter by due date
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Filter by task
        if ($request->filled('task_id')) {
            $query->where('id', $request->task_id);
        }

        $todos = $query->paginate(12);

        // Get all assigned todos for filter dropdown (without pagination)
        // Also exclude todos that already have a report for today
        $allAssignedTodos = TodoList::with('createdBy')
            ->whereHas('supervisors', function ($q) use ($supervisorId) {
                $q->where('users.id', $supervisorId);
            })
            ->whereDoesntHave('dailyReports', function ($q) use ($supervisorId, $today) {
                $q->where('supervisor_id', $supervisorId)
                    ->whereDate('report_date', $today);
            })
            ->active()
            ->orderBy('title')
            ->get();

        return view('supervisor.todos', compact('todos', 'allAssignedTodos'));
    }
}
