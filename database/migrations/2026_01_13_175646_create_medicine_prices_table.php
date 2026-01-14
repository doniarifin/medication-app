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
        Schema::create('medicine_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->uuid('medicine_id');
            $table->decimal('unit_price', 13, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignUuid('medicine_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines_prices');
    }
};
