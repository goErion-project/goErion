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
        Schema::create('disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('winner_id')->nullable();
            $table->timestamps();

            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');// set null when winner is deleted
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
