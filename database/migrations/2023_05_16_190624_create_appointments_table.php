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
        Schema::create('appointments', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('clinic_id')->nullable();
            $table->foreignId('doctor_id')->nullable();
            $table->string('full_name');
            $table->integer('age');
            $table->string('gender');
            $table->date('date');
            $table->string('time')->nullable();
            $table->text('description')->nullable();
            $table->boolean('hide_user')->default(0);
            $table->enum('status', ['archived', 'pending', 'booked'])->default('pending');
            $table->float('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
