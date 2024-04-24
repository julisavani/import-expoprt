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
        Schema::create('star_melees', function (Blueprint $table) {
            $table->id();
            $table->string('shape')->nullable();
            $table->string('size')->nullable();
            $table->string('sieve')->nullable();
            $table->string('carat')->nullable();
            $table->string('def_vvs_vs')->nullable();
            $table->string('def_vs_si')->nullable();
            $table->string('fg_vvs_vs')->nullable();
            $table->string('fg_vs_si')->nullable();
            $table->string('pink_vvs_vs_si1')->nullable();
            $table->string('yellow_vvs_vs_si1')->nullable();
            $table->string('blue_vvs_vs_si1')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('star_melees');
    }
};
