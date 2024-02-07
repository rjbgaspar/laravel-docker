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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            // SPE Begin Keycloak integration
            $table->uuid('kc_id');
            $table->string('kc_login', 50);
            $table->string('kc_first_name', 50)->nullable();
            $table->string('kc_last_name', 50)->nullable();
            $table->string('kc_email', 191)->nullable();
            $table->string('kc_image_url', 256)->nullable();
            $table->boolean('kc_activated');
            $table->string('kc_lang_key', 10)->nullable();
            $table->string('kc_created_by', 50);
            $table->dateTime('kc_created_date')->nullable();
            $table->text('kc_authorities')->nullable();
            // SPE End Keycloak integration
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
