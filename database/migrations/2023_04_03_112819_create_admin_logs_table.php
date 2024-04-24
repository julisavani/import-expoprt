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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->comment("Admin's id");
            $table->foreignId('user_id')->nullable()->comment("User's id");
            $table->string('event')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->tinyInteger('log_type')->default(0);
            $table->tinyInteger('flag')->default(1);
            $table->bigInteger('log_id')->nullable();
            $table->uuid('product_id')->nullable()->comment("product's id");
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
