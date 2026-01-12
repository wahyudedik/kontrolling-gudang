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
        Schema::create('report_man_power', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('daily_report_id');
            $table->integer('employees_present')->default(0);
            $table->integer('employees_absent')->default(0);
            $table->timestamps();

            $table->foreign('daily_report_id')->references('id')->on('daily_reports')->onDelete('cascade');
            $table->index('daily_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_man_power');
    }
};
