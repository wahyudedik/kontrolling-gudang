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
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->enum('session', ['morning', 'afternoon', 'evening'])->after('report_date')->default('morning');
            $table->string('photo_path')->nullable()->after('status');
            
            // Drop old unique constraint
            $table->dropUnique(['todo_list_id', 'supervisor_id', 'report_date']);
            
            // Add new unique constraint including session
            $table->unique(['todo_list_id', 'supervisor_id', 'report_date', 'session'], 'daily_reports_unique_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique('daily_reports_unique_session');
            $table->unique(['todo_list_id', 'supervisor_id', 'report_date']);
            
            $table->dropColumn(['session', 'photo_path']);
        });
    }
};
