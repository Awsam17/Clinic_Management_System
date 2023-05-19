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
        Schema::create('doc_clinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id');
            $table->foreignId('doctor_id');
            $table->dateTime('join_date');
            $table->dateTime('end_date');
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_clinics');
    }
};
