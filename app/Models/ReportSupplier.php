<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSupplier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'daily_report_id',
        'supplier_name',
        'jenis_barang',
    ];

    /**
     * Get the daily report this belongs to
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
