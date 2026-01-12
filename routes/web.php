<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupervisorTodoController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin Routes
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::resource('todo-lists', TodoListController::class);
    Route::resource('users', UserController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Supervisor Routes
Route::middleware(['auth', 'supervisor'])->group(function () {
    Route::get('supervisor/todos', [SupervisorTodoController::class, 'index'])->name('supervisor.todos');
    Route::get('daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::get('daily-reports/create/{todoList}', [DailyReportController::class, 'create'])->name('daily-reports.create');
    Route::post('daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
    Route::get('daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('daily-reports.show');
    Route::get('daily-reports/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('daily-reports.edit');
    Route::put('daily-reports/{dailyReport}', [DailyReportController::class, 'update'])->name('daily-reports.update');
    Route::delete('daily-reports/{dailyReport}', [DailyReportController::class, 'destroy'])->name('daily-reports.destroy');
});

require __DIR__ . '/auth.php';
