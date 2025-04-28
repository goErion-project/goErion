<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    /**
     * User Management Migration
     *
     * This migration creates the core user-related tables for the application:
     * - users: Stores user account information including authentication and messaging details
     * - password_reset_tokens: Manages password reset functionality
     *
     * Tables created:
     * - users:
     *   - Primary UUID identifier
     *   - Authentication credentials (username, password)
     *   - Security features (2FA, session management)
     *   - Blockchain/Crypto related fields (mnemonic, payment_address)
     *   - Communication fields (bitmessage_address, pgp_key, messaging keys)
     *   - Referral system integration
     *
     * - Password_reset_tokens:
     *   - Email-based password reset functionality
     *   - Token management for a reset process
     *
     * @package goErion
     * @category Database\Migrations
     * @author Your Name
     */

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('session_id')->nullable();
            $table->text('mnemonic');
            $table->text('payment_address')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->boolean('login_2fa')->default(false);
            $table->string('referral_code');
            $table->uuid('referrer_by')->nullable();
            $table->text('bitmessage_address')->nullable();
            $table->text('pgp_key')->nullable();
            $table->longText('msg_public_key')->nullable();
            $table->longText('msg_private_key')->nullable();
            $table->timestamps();
            $table->foreign('referrer_by')->references('id')->on('users')->onDelete('set null');
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

//        Schema::create('sessions', function (Blueprint $table) {
//            $table->string('id')->primary();
//            $table->foreignId('user_id')->nullable()->index();
//            $table->string('ip_address', 45)->nullable();
//            $table->text('user_agent')->nullable();
//            $table->longText('payload');
//            $table->integer('last_activity')->index();
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
//        Schema::dropIfExists('sessions');
    }
};
