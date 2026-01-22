<?php

namespace App\Observers;

use App\Models\User;
use App\Models\TodoList;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->isSupervisor()) {
            // Assign all active daily habits to the new supervisor
            $dailyHabits = TodoList::where('type', 'daily')
                ->where('is_active', true)
                ->get();

            if ($dailyHabits->isNotEmpty()) {
                $user->assignedTodos()->syncWithoutDetaching($dailyHabits->pluck('id'));
            }
        }
    }
}
