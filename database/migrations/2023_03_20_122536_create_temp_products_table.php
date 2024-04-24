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
        Schema::create('temp_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('stone_id')->nullable();
            $table->string('cert_no')->nullable();
            $table->string('cert_type')->nullable();
            $table->string('cert_url')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->string('diamond_type')->nullable();
            $table->string('size_id')->nullable()->comment("Size's id");
            $table->double('carat',8,2)->default(0);
            $table->string('shape_id')->nullable()->comment("Shape's id");
            $table->string('color_id')->nullable()->comment("Color's id");
            $table->string('colors_id')->nullable()->comment("FancyColor's id");
            $table->string('overtone_id')->nullable()->comment("FancyColor's id");
            $table->string('intensity_id')->nullable()->comment("FancyColor's id");
            $table->string('clarity_id')->nullable()->comment("Clarity's id");
            $table->string('cut_id')->nullable()->comment("Finish's id");
            $table->string('polish_id')->nullable()->comment("Finish's id");
            $table->string('symmetry_id')->nullable()->comment("Finish's id");
            $table->string('fluorescence_id')->nullable()->comment("Fluorescence's id");

            $table->double('rapo_rate',15,2)->default(0);
            $table->double('rapo_amount',15,2)->default(0);
            $table->double('discount',8,2)->default(0);
            $table->double('rate',15,2)->default(0);
            $table->double('amount',15,2)->default(0);
            $table->double('table',8,2)->default(0);
            $table->double('table_per',8,2)->default(0);
            $table->double('depth',8,2)->default(0);
            $table->double('depth_per',8,2)->default(0);
            $table->string('measurement')->nullable();
            $table->double('length',8,2)->default(0);
            $table->double('width',8,2)->default(0);
            $table->double('height',8,2)->default(0);
            $table->double('ratio',5,2)->default(0);
            $table->string('bgm_id')->nullable()->comment("Fluorescence's id");
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_products');
    }
};
