<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TodoList extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'type',
        'date',
        'due_date',
        'created_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user who created this todo list
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all items for this todo list
     */
    public function items(): HasMany
    {
        return $this->hasMany(TodoItem::class)->orderBy('order');
    }

    /**
     * Get all daily reports for this todo list
     */
    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    /**
     * Get supervisors assigned to this todo list
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'todo_assignments', 'todo_list_id', 'supervisor_id')
            ->withTimestamps();
    }

    /**
     * Scope to get active todos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get todos by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
