<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the Migrations.
     */
    public function up(): void
    {
        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('message');
            $table->uuid('author_id');
            $table->uuid('dispute_id');
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
    }
};
