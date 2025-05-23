<?php

use App\Models\Purchase;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table -> uuid('offer_id');
            $table -> uuid('shipping_id') -> nullable(); // shipping == null when a digital product is purchased
            $table -> uuid('buyer_id') -> nullable(); // buyer == null when a user deletes an account
            $table -> uuid('vendor_id');
            $table -> uuid('dispute_id') -> nullable(); // a dispute is not null if it is disputed
            $table -> uuid('feedback_id') -> nullable(); // feedback is null when the purchase is deleted

            $table -> unsignedInteger('quantity');

            $table -> text('address');
            $table -> decimal('to_pay', 24,12); // btc to pay on this purchase
            $table -> text('message');
            $table -> enum('state', array_keys(Purchase::$states)) ->default(Purchase::DEFAULT_STATE);
            $table -> enum('type', array_keys(Purchase::$types)) ->default(Purchase::DEFAULT_TYPE);
            $table -> string('coin_name', 5) ->default('btc');

            // multisig params
            $table -> text('marketplace_address') -> nullable();
            $table -> text('multisig_address') -> nullable();
            $table -> text('redeem_script') -> nullable();
            $table -> text('private_key') -> nullable();
            $table -> text('hex') -> nullable();

            $table -> boolean('read') ->default(false);
            $table -> text('delivered_product') -> nullable();

            $table->timestamps();

            // foreign keys
            $table -> foreign('offer_id') -> references('id') -> on('offers') -> onDelete('cascade');
            $table -> foreign('shipping_id') -> references('id') -> on('shippings') -> onDelete('set null');
            $table -> foreign('buyer_id') -> references('id') -> on('users') -> onDelete('set null');
            $table -> foreign('vendor_id') -> references('id') -> on('vendors') -> onDelete('cascade');
            $table -> foreign('feedback_id') -> references('id') -> on('feedback') -> onDelete('set null');
        });
    }

    /**
     * Reverse the Migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
