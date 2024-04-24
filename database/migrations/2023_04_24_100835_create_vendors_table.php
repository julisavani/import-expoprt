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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile',15)->nullable();
            $table->string('other_phone',15)->nullable();
            $table->string('about')->nullable();
            $table->string('job_title')->nullable();
            $table->string('business_type')->nullable();
            $table->string('buying_group')->nullable();
            $table->string('group_title')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->foreignId('country_id')->comment("Country's id");
            $table->tinyInteger('status')->default(0)->comment('0=deactive 1=active');
            $table->timestamp('verified_at')->nullable();
            $table->tinyInteger('flag')->default(0);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
