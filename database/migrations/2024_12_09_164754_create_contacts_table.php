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
        Schema::create('contacts', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name')->unique();
            $table->longText('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->integer('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states');
            $table->string('phone')->nullable();
            $table->string('pincode',6)-> nullable();
            $table->boolean('active')->default(true);
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
