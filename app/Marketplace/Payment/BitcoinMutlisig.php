<?php

namespace App\Marketplace\Payment;

use App\Exceptions\RequestException;
use App\Models\Purchase;

class BitcoinMutlisig extends Payment
{
    /**
     * Handle the purchase process.
     *
     * @return void
     */
    public function purchased(): void
    {
        // Logic for handling the purchase process
        // Example: Mark the purchase as initiated
        $this->purchase->status = 'purchased';
        $this->purchase->save();
    }

    /**
     * Handle the scent process.
     *
     * @return void
     */
    public function sent(): void
    {
        // Logic for handling the sent process
        // Example: Mark the purchase as sent
        $this->purchase->status = 'sent';
        $this->purchase->save();
    }

    /**
     * Handle the delivered process.
     *
     * @return void
     */
    public function delivered(): void
    {
        // Logic for handling the delivered process
        // Example: Mark the purchase as delivered
        $this->purchase->status = 'delivered';
        $this->purchase->save();
    }

    /**
     * Handle the canceled process.
     *
     * @return void
     */
    public function canceled(): void
    {
        // Logic for handling the canceled process
        // Example: Mark the purchase as canceled
        $this->purchase->status = 'canceled';
        $this->purchase->save();
    }

    /**
     * Resolve the payment with the given parameters.
     *
     * @param array $parameters
     * @return mixed
     * @throws RequestException
     */
    public function resolved(array $parameters): mixed
    {
        // Logic for resolving the payment
        // Example: Validate and process the receiving address
        if (!isset($parameters['receiving_address'])) {
            throw new RequestException('Receiving address is required for resolution.');
        }

        $this->purchase->receiving_address = $parameters['receiving_address'];
        $this->purchase->status = 'resolved';
        $this->purchase->save();
    }

    /**
     * Returns the balance to pay.
     *
     * @return float
     */
    public function balance(): float
    {
        // Logic to calculate the balance
        return $this->purchase->amount_due;
    }

    /**
     * Converts USD to the equivalent coin amount.
     *
     * @param float $usd
     * @return float
     */
    public function usdToCoin($usd): float
    {
        // Logic to convert USD to coin
        return $usd / $this->coin->getExchangeRate();
    }

    /**
     * Returns the label of the coin.
     *
     * @return string
     */
    public function coinLabel(): string
    {
        // Logic to return the coin label
        return $this->coin->getLabel();
    }
}
