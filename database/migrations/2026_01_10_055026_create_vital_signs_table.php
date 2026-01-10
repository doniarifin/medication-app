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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('medical_record_id')->constrained()->cascadeOnDelete();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('systole')->nullable();
            $table->integer('diastole')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiration_rate')->nullable();
            $table->decimal('body_temperature', 4, 1)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
