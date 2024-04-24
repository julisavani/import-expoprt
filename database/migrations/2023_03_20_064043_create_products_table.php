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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stone_id')->unique();
            $table->string('cert_no')->nullable();
            $table->tinyInteger('cert_type')->default(0);
            $table->string('cert_url')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->tinyInteger('diamond_type')->default(0);
            $table->foreignId('size_id')->nullable()->comment("Size's id");
            $table->double('carat',8,2)->default(0);
            $table->foreignId('shape_id')->nullable()->comment("Shape's id");
            $table->foreignId('color_id')->nullable()->comment("Color's id");
            $table->foreignId('colors_id')->nullable()->comment("FancyColor's id");
            $table->foreignId('overtone_id')->nullable()->comment("FancyColor's id");
            $table->foreignId('intensity_id')->nullable()->comment("FancyColor's id");
            $table->foreignId('clarity_id')->nullable()->comment("Clarity's id");
            $table->foreignId('cut_id')->nullable()->comment("Finish's id");
            $table->foreignId('polish_id')->nullable()->comment("Finish's id");
            $table->foreignId('symmetry_id')->nullable()->comment("Finish's id");
            $table->foreignId('fluorescence_id')->nullable()->comment("Fluorescence's id");

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
            $table->double('ratio',8,2)->default(0);
            $table->foreignId('bgm_id')->nullable()->comment("Fluorescence's id");
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('hold')->default(0);
            $table->tinyInteger('confirm')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('size_id')->references('id')->on('sizes');
            $table->foreign('shape_id')->references('id')->on('shapes');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('colors_id')->references('id')->on('fancy_colors');
            $table->foreign('overtone_id')->references('id')->on('fancy_colors');
            $table->foreign('intensity_id')->references('id')->on('fancy_colors');
            $table->foreign('clarity_id')->references('id')->on('clarities');
            $table->foreign('cut_id')->references('id')->on('finishes');
            $table->foreign('polish_id')->references('id')->on('finishes');
            $table->foreign('symmetry_id')->references('id')->on('finishes');
            $table->foreign('fluorescence_id')->references('id')->on('fluorescences');
            $table->foreign('bgm_id')->references('id')->on('fluorescences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
