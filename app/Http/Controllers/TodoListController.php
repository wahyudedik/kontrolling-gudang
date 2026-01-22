<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoListRequest;
use App\Http\Requests\UpdateTodoListRequest;
use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = TodoList::with('createdBy', 'supervisors');

        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }

        $todoLists = $query->latest()->paginate(15);

        return view('todo-lists.index', compact('todoLists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $supervisors = \App\Models\User::where('role', 'supervisor')->orderBy('name')->get();
        return view('todo-lists.create', compact('supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoListRequest $request): RedirectResponse
    {
        $todoList = TodoList::create([
            'title' => $request->title,
            'type' => $request->type,
            'difficulty_level' => $request->difficulty_level ?? 'medium',
            'date' => $request->date,
            'due_date' => $request->due_date,
            'created_by' => $request->user()->id,
            'is_active' => true,
        ]);

        // Assign to supervisors
        if ($request->has('supervisor_ids') && is_array($request->supervisor_ids) && count($request->supervisor_ids) > 0) {
            $todoList->supervisors()->sync($request->supervisor_ids);
        }

        // Create default todo items (5 fixed items)
        $defaultItems = [
            ['item_type' => 'man_power', 'order' => 1],
            ['item_type' => 'stock_finish_good', 'order' => 2],
            ['item_type' => 'stock_raw_material', 'order' => 3],
            ['item_type' => 'warehouse_condition', 'order' => 4],
            ['item_type' => 'supplier', 'order' => 5],
        ];

        foreach ($defaultItems as $item) {
            $todoList->items()->create($item);
        }

        return redirect()->route('todo-lists.index')
            ->with('success', 'To Do List berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoList $todoList): View
    {
        $todoList->load('createdBy', 'items', 'dailyReports.supervisor');

        return view('todo-lists.show', compact('todoList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TodoList $todoList): View
    {
        $supervisors = \App\Models\User::where('role', 'supervisor')->orderBy('name')->get();
        $todoList->load('supervisors');
        return view('todo-lists.edit', compact('todoList', 'supervisors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoListRequest $request, TodoList $todoList): RedirectResponse
    {
        $todoList->update($request->validated());

        // Update supervisor assignments
        if ($request->has('supervisor_ids') && is_array($request->supervisor_ids) && count($request->supervisor_ids) > 0) {
            $todoList->supervisors()->sync($request->supervisor_ids);
        }

        return redirect()->route('todo-lists.index')
            ->with('success', 'To Do List berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoList $todoList): RedirectResponse
    {
        $todoList->delete();

        return redirect()->route('todo-lists.index')
            ->with('success', 'To Do List berhasil dihapus.');
    }
}
