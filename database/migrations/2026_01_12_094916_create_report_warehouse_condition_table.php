<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_warehouse_condition', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('daily_report_id');
            $table->enum('warehouse', ['cs1', 'cs2', 'cs3', 'cs4', 'cs5', 'cs6']);
            $table->boolean('check_1')->default(false);
            $table->boolean('check_2')->default(false);
            $table->boolean('check_3')->default(false);
            $table->boolean('check_4')->default(false);
            $table->boolean('check_5')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('daily_report_id')->references('id')->on('daily_reports')->onDelete('cascade');
            $table->index(['daily_report_id', 'warehouse']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_warehouse_condition');
    }
};
