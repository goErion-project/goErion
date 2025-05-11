<?php

use App\Models\PhysicalProduct;
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
        Schema::create('physical_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //shipping info
            $table->enum('countries_option',array_keys(PhysicalProduct::$countriesOptions))->default('all')->nullable();
            $table->text('countries');
            $table->string('country_from')->nullable();
            $table->timestamps();

            //keys
            $table->foreign('id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_products');
    }
};
