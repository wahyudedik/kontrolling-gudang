<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'todo_list_id',
        'supervisor_id',
        'report_date',
        'session', // morning, afternoon, evening
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    /**
     * Get the todo list this report belongs to
     */
    public function todoList(): BelongsTo
    {
        return $this->belongsTo(TodoList::class);
    }

    /**
     * Get the supervisor who created this report
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get man power data for this report
     */
    public function manPower(): HasOne
    {
        return $this->hasOne(ReportManPower::class);
    }

    /**
     * Get stock finish good items for this report
     */
    public function stockFinishGood(): HasMany
    {
        return $this->hasMany(ReportStockFinishGood::class);
    }

    /**
     * Get stock raw material items for this report
     */
    public function stockRawMaterial(): HasMany
    {
        return $this->hasMany(ReportStockRawMaterial::class);
    }

    /**
     * Get warehouse conditions for this report
     */
    public function warehouseConditions(): HasMany
    {
        return $this->hasMany(ReportWarehouseCondition::class);
    }

    /**
     * Get suppliers for this report
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(ReportSupplier::class);
    }
}
