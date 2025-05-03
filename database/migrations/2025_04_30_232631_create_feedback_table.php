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
        Schema::create('feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->uuid('buyer_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->text('product_name');
            $table->decimal('product_value', 16, 2);
            $table->enum('type', ['positive', 'negative', 'neutral']);
            $table->tinyInteger('quality_rate');
            $table->tinyInteger('communication_rate');
            $table->tinyInteger('shipping_rate');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
