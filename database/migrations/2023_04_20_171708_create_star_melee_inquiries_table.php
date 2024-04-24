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
        Schema::create('star_melee_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('shape')->nullable();
            $table->string('color')->nullable();
            $table->string('clarity')->nullable();
            $table->string('carat')->nullable();
            $table->string('price')->nullable();
            $table->string('qty')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->foreignId('user_id')->nullable()->comment("User's id");
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('star_melee_inquiries');
    }
};
