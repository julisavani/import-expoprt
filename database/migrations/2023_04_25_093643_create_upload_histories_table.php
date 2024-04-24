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
        Schema::create('upload_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->nullable();
            $table->tinyInteger('upload_type')->default(0)->comment('0=manual 2=file');
            $table->tinyInteger('status')->default(0)->comment('0=error 1=Completed');
            $table->integer('total')->default(0);
            $table->integer('valid')->default(0);
            $table->integer('invalid')->default(0);
            $table->uuid('uuid');
            $table->foreignId('vendor_id')->nullable()->comment("vendor's id");
            $table->timestamps();
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_histories');
    }
};
