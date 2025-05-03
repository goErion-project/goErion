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
        $stateQutoed = array_map(function($state){
            return "'$state'";
        }, array_keys(\App\Models\Purchase::$states));
        $statesStringinfied = implode(",", $stateQutoed);

        // custom statement to add enum value to states of the purchases
        DB::statement("ALTER TABLE purchases MODIFY COLUMN state ENUM($statesStringinfied) DEFAULT 'purchased' NOT NULL" );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_cancelation');
    }
};
