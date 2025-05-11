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
        // add deleted to offers
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('deleted')->default(false);
        });

        // add deleted to shippings
        Schema::table('shippings', function (Blueprint $table) {
            $table->boolean('deleted')->default(false);
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_offers_deleted');
    }
};
