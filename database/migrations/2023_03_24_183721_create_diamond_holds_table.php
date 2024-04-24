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
        Schema::create('diamond_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment("User's id");
            $table->uuid('product_id')->nullable()->comment("product's id");
            $table->tinyInteger('status')->default(1);
            $table->string('reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamond_holds');
    }
};
