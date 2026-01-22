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
        Schema::create('todo_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->enum('type', ['man_power', 'finish_good', 'raw_material', 'gudang_cs1', 'gudang_cs2', 'gudang_cs3', 'gudang_cs4', 'gudang_cs5', 'gudang_cs6', 'gudang', 'supplier_datang', 'daily']);
            $table->date('date')->nullable();
            $table->uuid('created_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['type', 'is_active']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_lists');
    }
};
