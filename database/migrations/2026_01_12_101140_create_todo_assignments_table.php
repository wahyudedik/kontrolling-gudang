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
        Schema::create('todo_assignments', function (Blueprint $table) {
            $table->uuid('todo_list_id');
            $table->uuid('supervisor_id');
            $table->timestamps();

            $table->foreign('todo_list_id')->references('id')->on('todo_lists')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['todo_list_id', 'supervisor_id']);
            $table->index('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_assignments');
    }
};
