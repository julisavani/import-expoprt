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
        Schema::table('temp_products', function (Blueprint $table) {
            $table->string('availability')->nullable();
            $table->string('milky')->nullable();
            $table->string('shade')->nullable();
            $table->string('crown_angle')->nullable();
            $table->string('crown_height')->nullable();
            $table->string('crown_open')->nullable();
            $table->string('pavilion_angle')->nullable();
            $table->string('pavilion_height')->nullable();
            $table->string('pavilion_open')->nullable();
            $table->string('white_table')->nullable();
            $table->string('white_side')->nullable();
            $table->string('table_black')->nullable();
            $table->string('side_black')->nullable();
            $table->string('table_open')->nullable();
            $table->string('girdle')->nullable();
            $table->string('girdle_desc')->nullable();
            $table->string('culet')->nullable();
            $table->string('key_to_symbols')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_products', function (Blueprint $table) {
            //
        });
    }
};
