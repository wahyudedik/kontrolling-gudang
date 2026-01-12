<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportWarehouseCondition extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'report_warehouse_condition';

    protected $fillable = [
        'daily_report_id',
        'warehouse',
        'check_1',
        'check_2',
        'check_3',
        'check_4',
        'check_5',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_1' => 'boolean',
            'check_2' => 'boolean',
            'check_3' => 'boolean',
            'check_4' => 'boolean',
            'check_5' => 'boolean',
        ];
    }

    /**
     * Get the daily report this belongs to
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
