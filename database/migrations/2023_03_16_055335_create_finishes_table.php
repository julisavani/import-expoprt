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
        Schema::create('finishes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('specific_type')->default(1)->comment('0=specific 1=General');
            $table->tinyInteger('type')->default(1)->comment('0=Cut 1=Polish 2=Symmetry 3=General');
            $table->tinyInteger('status')->default(1)->comment('0=deactive 1=active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finishes');
    }
};
