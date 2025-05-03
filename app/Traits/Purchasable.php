<?php

namespace App\Traits;

use App\Events\Purchase\CanceledPurchase;
use App\Events\Purchase\NewPurchase;
use App\Events\Purchase\ProductDelivered;
use App\Events\Purchase\ProductDisputed;
use App\Events\Purchase\ProductDisputeResolved;
use App\Events\Purchase\ProductSent;
use App\Exceptions\RequestException;
use App\Marketplace\Cart;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Shipping;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait Purchasable
{
    /**
     *  Runs purchased procedure
     *  It has been called in DB transaction
     * @throws RequestException|BindingResolutionException
     * @throws \Exception
     */
    public function purchased(): void
    {
        // check if shipping is not deleted in the meantime
        // shipping is deleted between adding to cart and checkout
        if($this->shipping && Shipping::query()->where('id',$this->shipping->id)->where('deleted', '=', 1)->exists()){
            Cart::getCart()->clearCart(); // clear cart
            throw new RequestException('Selected shipping is deleted, please add the product again.');
        }

        // Generate Payment Service from Service Container
        $this -> payment = app() -> makeWith(\App\Marketplace\Payment\Payment::class, ['purchase' => $this]);

        // Runs purchased procedure of the payment
        $this -> getPayment() -> purchased();
        // Prepare purchase
        $this -> encryptMessage();
        // calculate bitcoin to pay at this moment
        $this -> to_pay = $this -> getPayment() -> usdToCoin($this -> getSumDollars());
        // Substract the quantity from the product
        $this -> offer -> product -> substractQuantity($this -> quantity);
        $this -> offer -> product -> save();

        event(new NewPurchase($this));

        // if it is an autodelivery mark as sent and run sent procedure
        if($this -> offer -> product -> isAutodelivery()){
            // Mark as sent and run sent procedure
            $this -> getPayment() -> sent();
            $this -> state = 'sent';
            $this -> save();

            // pull products to the delivered product section
            $productsToDelivery = $this -> offer -> product -> digital -> getProducts($this -> quantity);

            $this -> delivered_product = implode("\n", $productsToDelivery);
            $this -> save();
        }
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     */
    private function markingAsSent(): void
    {
        throw_unless($this->enoughBalance(),new RequestException("Order must be paid by buyer"));
        try{
            DB::beginTransaction();
            // Payment service runs procedure
            $this -> getPayment() -> sent();

            $this -> state = 'sent';
            $this -> save();

            DB::commit();
            event(new ProductSent($this));

        }
        catch (\Exception $e){
            DB::rollBack();

            Log::error("Purchase $this->id : " . $e->getMessage());

            throw new RequestException('Error happened! Please try again later!');
        }
    }

    /**
     * Runs procedure when the product is sent
     * Atomic in transaction
     * @throws RequestException
     * @throws \Throwable
     */
    public function sent(): void
    {
        // checking for vendor
        if(!$this -> isVendor())
            throw new RequestException('You must be vendor of this product to mark this sale as sent!');

        // checking for purchased
        throw_unless($this->state=='purchased', new RequestException(
            'Purchase must be in purchased state!'
        ));

        // checking for normal type
        throw_if($this->type!='normal',new RequestException('This purchase is not Escrow type!'));

        // Calling procedure for marking as sent
        $this->markingAsSent();
    }

    /**
     * Function procedure same as delivered but without buyer check
     * Releasing sent purchases and making the purchase delivered
     * @throws \Throwable
     */
    public function release(): void
    {
        // state must be 'sent' to be delivered
        throw_unless($this->state=='sent', new RequestException('This purchase is already delivered!'));


        try{
            $this->state = 'delivered';
            $this -> save();

            // state now must be delivered
            throw_unless($this->state=='delivered', new \Exception('This purchase is already delivered!'));
            $this -> getPayment() -> delivered();

            event(new ProductDelivered($this));
        }
        catch (\Exception $e){
            // return to before state
            $this->state='sent';
            $this->save();

            // log out the exception message
            Log::error("Purchase $this->id " . $e ->getMessage());
            throw new RequestException('Error happened! Please try again later!');
        }
    }

    /**
     * Adapted for Finalize Early purchases
     *
     * Function that does mark the purchase as delivered but in case of error restores to purchased state
     * Adapted for completing purchases
     *
     * @throws RequestException
     * @throws \Throwable
     */
    private function markingAsDelivered(): void
    {
        // state must be 'sent' to be delivered
        throw_unless($this->state=='sent', new RequestException('This purchase is already delivered!'));


        try{
            $this->state = 'delivered';
            $this -> save();

            // state now must be delivered
            throw_unless($this->state=='delivered', new \Exception('This purchase is already delivered!'));
            $this -> getPayment() -> delivered();

            event(new ProductDelivered($this));
        }
        catch (\Exception $e){
            // return to 'purchased' state cause this is called for finalize early
            $this->state='purchased';
            $this->save();

            // log out the exception message
            Log::error("Purchase $this->id :"  . $e ->getMessage());
            throw new RequestException($e ->getMessage());
        }
    }


    /**
     * Runs procedure when the product is delivered
     * Atomic in transactions
     * @throws RequestException
     * @throws \Throwable
     */
    public function delivered(): void
    {
        if(!$this -> isBuyer())
            throw new RequestException('You must be buyer to mark this purchase as delivered!');

        throw_if($this->type!='normal', new RequestException('This purchase must be Escrow type!'));

        // state must be 'sent' to be delivered
        throw_unless($this->state=='sent', new RequestException('This purchase is already delivered!'));


        try{
            $this->state = 'delivered';
            $this -> save();

            // state now must be delivered
            throw_unless($this->state=='delivered', new \Exception('This purchase is already delivered!'));
            $this -> getPayment() -> delivered();

            event(new ProductDelivered($this));
        }
        catch (\Exception $e){
            // return to before state
            $this->state='sent';
            $this->save();

            // log out the exception message

            Log::error("Purchase $this->id :" . $e ->getMessage());

            throw new RequestException('Error happened! Please try again later!');
        }

    }

    /**
     * Returns if the purchase is completable by the Complete Purchase Command
     *
     * @return bool
     */
    private function isCompletable(): bool
    {
        // Purchase type must be finalized Early
        if($this->type != 'fe')
            return false;
        // Purchased
        if($this->state == 'purchased')
            return true;
        // if is sent, and the product of the purchase is digital and autodelivery
        if($this->state == 'sent' && $this->offer->product->isDigital() && $this->offer->product->isAutodelivery())
            return true;

        return false;
    }

    /**
     * Called by command, for Finalize Early purchases if there is enough Balance on the address
     * Transform Purchase from 'purchased' state to 'delivered' state
     *
     * @throws RequestException
     * @throws \Throwable
     */
    public function complete(): void
    {
        // checking if the purchase is FE
        throw_if($this->type!='fe', new RequestException('The purchase you selected is not Finalize Early type!'));
        // checking if it is not in the purchased state
        throw_if(!$this->isCompletable(), new RequestException('The purchase you selected is not in purchased state or not in sent state and product is not autodelivery!'));

        // Marking the purchase as sent
        $this->markingAsSent();

        // Releasing funds to vendor
        $this->markingAsDelivered();

        $this->status_notification = null;
        $this->save();
    }

    /**
     * Runs procedure when the product is marked as disputed
     * Atomic in transaction
     * @throws \Throwable
     */
    public function disputed(): void
    {
        try{
            DB::beginTransaction();
            // Disputed procedure from selected
            $this -> getPayment() -> disputed();

            $this -> state = 'disputed';

            $this -> save();
            DB::commit();

        }
        catch (\Exception $e){
            DB::rollBack();
            throw new RequestException('Error happened! Please try again later!');
        }
    }
    /**
     * Make a dispute and dispute message
     *
     * @throws RequestException|\Throwable
     */
    public function makeDispute($message): void
    {
        if(!$this->canMakeDispute())
            throw new RequestException('You don\' have permission to make dispute!');

        try{
            DB::beginTransaction();
            // Make dispute
            $newDispute = new Dispute();
            $newDispute -> save();
            // Make a message
            $newDisputeMessage = new DisputeMessage();
            $newDisputeMessage -> setDispute($newDispute);
            $newDisputeMessage -> message = $message;
            $newDisputeMessage -> setAuthor(auth() -> user());
            $newDisputeMessage -> save();


            // Mark as disputed
            $this -> state = 'disputed';
            $this -> setDispute($newDispute);

            $this -> save();

            DB::commit();
            event(new ProductDisputed($this,auth()->user()));
        }
        catch (\Exception $e){
            DB::rollBack();
            throw new RequestException('Something went wrong! Please try again!' . $e -> getMessage());
        }

    }

    /**
     * Resolving disputes
     *
     * @param string $winnerId
     * @throws RequestException|\Throwable
     */
    public function resolveDispute(string $winnerId): void
    {
        $winner = User::query()->find($winnerId);
        throw_if($this->isDisputed() && $this->dispute->isResolved(), new RequestException("The dispute is already resolved!"));
        if(is_null($winner)) throw new RequestException('This user can not be winner!');



        // the user is not neither vendor nor buyer
        if(!$this -> isBuyer($winner) && !$this -> isVendor($winner))
            throw new RequestException('User must be vendor or buyer!');

        try{
            DB::beginTransaction();

            // Set the winner
            $this -> dispute -> winner_id = $winner -> id;
            // run resolved procedure
            $this -> getPayment() -> resolved(['receiving_address' => $winner -> coinAddress($this -> getPayment() -> coinLabel()) -> address]);

            $this -> dispute -> save();

            DB::commit();
            event(new ProductDisputeResolved($this));
        }
        catch (\Exception $e){
            DB::rollBack();
            throw new RequestException('Something went wrong, please try again!' . $e -> getMessage());
        }
    }

    /**
     * Cancel the purchase
     * @throws \Throwable
     */
    public function cancel(): void
    {
        throw_if($this -> state == 'canceled', new RequestException("The order is already canceled"));
        try{
            DB::beginTransaction();

            // restore product stock number
            $this->offer->product->quantity+=$this->quantity;
            $this->offer->product->save();

            // Set the state
            $this -> state = 'canceled';
            // run canceled procedure
            $this -> getPayment() -> canceled();
            $this -> save();

            DB::commit();
            event(new CanceledPurchase($this));
        }
        catch (\Exception $e){
            DB::rollBack();
            Log::error($e); // post error to log
            throw new RequestException('Something went wrong, please try again!' . $e -> getMessage());
        }
    }

}
