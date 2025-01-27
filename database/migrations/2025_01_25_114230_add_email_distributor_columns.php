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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->integer('distributor_id')->nullable();
            $table->foreign('distributor_id')->references('id')->on('distributors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropForeign('distributor_id');
            $table->dropColumn('distributor_id');
        });
    }
};
