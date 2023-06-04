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
        Schema::create('spec_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->references('id')->on('doctors')->cascadeOnDelete();
            $table->string('exp_years');
            $table->foreignId('specialty_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_docs');
    }
};
