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
        Schema::create('incoming_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('clinic_id');
            $table->integer('doctor_id');
            $table->string('full_name');
            $table->integer('age');
            $table->string('gender');
            $table->text('description');
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_apps');
    }
};
