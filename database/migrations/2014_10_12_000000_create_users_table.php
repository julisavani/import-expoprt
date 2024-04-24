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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('fullname')->nullable();
            $table->foreignId('country_id')->comment("Country's id");
            $table->string('company')->nullable();
            $table->string('mobile',15)->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('wechat')->nullable();
            $table->string('skype')->nullable();
            $table->string('document')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=deactive 1=active');
            $table->tinyInteger('terms')->default(0);
            $table->double('version',8,1)->default(0.1);
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
        Schema::dropIfExists('users');
    }
};
