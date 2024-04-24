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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('fluorescence_color_id')->nullable()->comment("Fluorescence's id");
            $table->string('pair')->nullable();
            $table->string('h_a')->nullable();
            $table->string('eye_clean')->default('No');
            $table->string('growth_type')->nullable();

            $table->foreign('fluorescence_color_id')->references('id')->on('fluorescences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
