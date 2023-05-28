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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->foreignId('address_id')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('email');
            $table->string('password');
            $table->integer('num_of_doctors')->default(0);
            $table->integer('num_of_rate')->default(1);
            $table->float('total_of_rate')->default(3);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
