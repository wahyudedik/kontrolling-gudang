<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportManPower extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'report_man_power';

    protected $fillable = [
        'daily_report_id',
        'employees_present',
        'employees_absent',
    ];

    protected function casts(): array
    {
        return [
            'employees_present' => 'integer',
            'employees_absent' => 'integer',
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
