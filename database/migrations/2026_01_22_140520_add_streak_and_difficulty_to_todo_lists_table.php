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
        Schema::table('todo_lists', function (Blueprint $table) {
            $table->integer('streak_count')->default(0)->after('is_active');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium')->after('streak_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todo_lists', function (Blueprint $table) {
            $table->dropColumn(['streak_count', 'difficulty_level']);
        });
    }
};
