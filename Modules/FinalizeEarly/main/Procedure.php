<?php

namespace Modules\FinalizeEarly\main;

use App\Models\Purchase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
//use Illuminate\Support\Facades\Log;

class Procedure
{
    public function commandHandle($command): void
    {
        // Debug SQL query
        $sql = Purchase::where(function($query) {
            $query->where('state', 'sent')
                ->where('type', 'fe');
        })
            ->orWhere(function($query) {
                $query->where('state', 'purchased')
                    ->where('type', 'fe');
            })
            ->toSql();

        $command->info("Generated SQL: " . $sql);

        // Get all purchases in this state with finalize early
        $purchasedPurchases = Purchase::where(function($query) {
            $query->where('state', 'sent')
                ->where('type', 'fe');
        })
            ->orWhere(function($query) {
                $query->where('state', 'purchased')
                    ->where('type', 'fe');
            })
            ->get();

        $command->info("There are " . $purchasedPurchases->count() . " Finalize Early unresolved purchases!");

        // Process each purchase
        foreach ($purchasedPurchases as $purchase) {
            $command->info("\nPurchase {$purchase->short_id}:");

            // Check if there are enough balances
            if ($purchase->enoughBalance()) {
                try {
                    // Complete them if there are
                    $purchase->complete();
                    $command->info("This purchase is completed!");
                } catch (RequestException $exception) {
                    $purchase->status_notification = $exception->getMessage();
                    $purchase->save();
                    $command->error("We are unable to complete this purchase, please check the log for details!");
                    Log::error($exception);
                }
            } else {
                $command->warn("There is not enough balance on this purchase!");
            }
        }

        $remainingCount = Purchase::where('state', 'purchased')
            ->where('type', 'fe')
            ->count();

        $command->info("\nThere are {$remainingCount} Finalize Early unresolved purchases waiting for funds!");
    }
}
