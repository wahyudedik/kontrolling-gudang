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
        Schema::create('todo_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('todo_list_id');
            $table->enum('item_type', ['man_power', 'stock_finish_good', 'stock_raw_material', 'warehouse_condition', 'supplier']);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('todo_list_id')->references('id')->on('todo_lists')->onDelete('cascade');
            $table->index(['todo_list_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_items');
    }
};
