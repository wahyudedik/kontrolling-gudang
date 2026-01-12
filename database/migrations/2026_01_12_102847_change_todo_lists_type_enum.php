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
        // Step 1: Change enum to VARCHAR temporarily
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type VARCHAR(50)");
        
        // Step 2: Update existing data to new type values
        // Convert 'template' and 'daily' to 'man_power' as default
        DB::table('todo_lists')
            ->whereIn('type', ['template', 'daily'])
            ->update(['type' => 'man_power']);
        
        // Step 3: Change back to enum with new values
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type ENUM('man_power', 'finish_good', 'raw_material', 'gudang_cs1', 'gudang_cs2', 'gudang_cs3', 'gudang_cs4', 'gudang_cs5', 'gudang_cs6', 'supplier_datang')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE todo_lists MODIFY COLUMN type ENUM('template', 'daily')");
    }
};
