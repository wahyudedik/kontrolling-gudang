<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Step 1: Change enum to VARCHAR temporarily
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type VARCHAR(50)");

        // Step 2: Change back to enum with new values including 'daily'
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type ENUM('man_power', 'finish_good', 'raw_material', 'gudang', 'supplier_datang', 'daily')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type VARCHAR(50)");
        DB::table('todo_lists')
            ->where('type', 'daily')
            ->delete(); // Remove daily habits before reverting
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type ENUM('man_power', 'finish_good', 'raw_material', 'gudang', 'supplier_datang')");
    }
};
