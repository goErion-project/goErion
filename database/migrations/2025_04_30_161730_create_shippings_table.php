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
        Schema::create('shippings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('name');
            $table->decimal('price', 16, 2);
            $table->string('duration',30);
            $table->integer('from_quantity');
            $table->integer('to_quantity');
            $table->timestamps();

            //keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
