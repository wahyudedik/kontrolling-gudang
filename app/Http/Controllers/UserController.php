<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::where('role', 'supervisor')
            ->withCount(['assignedTodos', 'dailyReports'])
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Supervisor berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load([
            'assignedTodos' => function($query) {
                $query->orderBy('due_date', 'desc');
            },
            'dailyReports.todoList' => function($query) {
                $query->select('id', 'title');
            }
        ]);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Supervisor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting if user has assigned todos or reports
        $assignedTodosCount = $user->assignedTodos()->count();
        $reportsCount = $user->dailyReports()->count();
        
        if ($assignedTodosCount > 0 || $reportsCount > 0) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus supervisor yang memiliki todo atau report.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Supervisor berhasil dihapus.');
    }
}
