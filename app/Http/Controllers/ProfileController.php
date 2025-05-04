<?php

namespace App\Http\Controllers;

use App\Events\Purchase\ProductDisputeNewMessageSent;
use App\Exceptions\RedirectException;
use App\Exceptions\RequestException;
use App\Http\Requests\Cart\MakePurchasesRequest;
use App\Http\Requests\Cart\NewItemRequest;
use App\Http\Requests\PGP\NewPGPKeyRequest;
use App\Http\Requests\PGP\StorePGPRequest;
use App\Http\Requests\Profile\BecomeVendorRequest;
use App\Http\Requests\Profile\ChangeAddressRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\NewTicketMessageRequest;
use App\Http\Requests\Profile\NewTicketRequest;
use App\Http\Requests\Purchase\LeaveFeedbackRequest;
use App\Http\Requests\Purchase\MakeDisputeRequest;
use App\Http\Requests\Purchase\NewDisputeMessageRequest;
use App\Marketplace\Cart;
use App\Models\Dispute;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\Wishlist;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Middleware that says the user must be authenticated and 2fa verified
     *
     * ProfileController constructor.
     */
    public function __construct()
    {
        $this -> middleware('auth');
        $this -> middleware('verify_2fa');
    }

    public function index(): View
    {
        return view('profile.index');
    }

    /**
     * Banned view
     *
     * @return RedirectResponse|View
     */
    public function banned(): RedirectResponse|View
    {
        if(auth()->user()->isBanned())
            $until = auth() -> user() -> bans() -> orderByDesc('until') -> first() -> until;
        else
            return redirect()->route('profile.index');

        return view('profile.banned', [
            'until' => $until
        ]);
    }

    /**
     * Displays the page with the current pgp and the form to change pgp
     *
     * @return View
     */
    public function pgp(): View
    {
        return view('profile.pgp');
    }

    /**
     * Accepts the request for the new PGP key and generates data to confirm pgp
     *
     * @param NewPGPKeyRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function pgpPost(NewPGPKeyRequest $request): RedirectResponse
    {
        try{
            $request -> persist();
        }
        catch(RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }

        return redirect() -> route('profile.pgp.confirm');
    }

    /**
     * Displays the page to confirm new PGP request
     *
     * @return View
     */
    public function pgpConfirm(): View
    {
        return view('profile.confirmpgp');
    }

    /**
     * Saves old key and sets a new pgp key
     *
     * @param StorePGPRequest $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function storePGP(StorePGPRequest $request): RedirectResponse
    {
        try{
            $request -> persist();
            session() -> flash('success', 'You have successfully changed you PGP key.');
        }
        catch(RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
            return redirect() -> back();
        }

        return redirect() -> route('profile.pgp');
    }

    /**
     * Page that displays old pgp keys
     *
     * @return Factory|View
     */
    public function oldpgp(): Factory|View
    {
        return view('profile.oldpgp',
            [
                'keys' => auth() -> user() -> pgpKeys() -> orderByDesc('created_at') -> get(),
            ]
        );
    }

    /**
     * Accepts request for changing password
     *
     * @param ChangePasswordRequest $request
     * @return RedirectResponse
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        try{
            $request -> persist();
        }
        catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }

        return redirect() -> back();
    }

    /**
     * Turn 2fa on or off
     *
     * @param $turn
     * @return RedirectResponse
     *
     */
    public function change2fa($turn): RedirectResponse
    {
        try{
            auth() -> user() -> set2fa($turn);
            session() -> flash('success', 'You have changed you 2FA setting.');
        }
        catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }
        return redirect() -> back();
    }

    /**
     * Become a Vendor page that has a link to become a Vendor request
     *
     * @return Factory|View
     */
    public function become(): Factory|View
    {
        return view('profile.become',[
            'vendorFee' => config('marketplace.vendor_fee'),
            'depositAddresses' => auth() -> user() -> vendorPurchases()
        ]);
    }

    /**
     * Make Vendor from the user
     *
     * @param BecomeVendorRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function becomeVendor(BecomeVendorRequest $request): RedirectResponse
    {
        try{
            auth() -> user() -> becomeVendor( $request ->input('address'));
            return redirect() -> route('profile.vendor');
        }
        catch (RedirectException $e){
            $e -> flashError();
            return redirect($e -> getRoute());
        }
        catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }
        return redirect() -> back();
    }

    /**
     * Add a product to the users wishlist
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function addRemoveWishlist(Product $product): RedirectResponse
    {
        // Remove if it is added
        if(Wishlist::added($product, auth() -> user())){
            // removing
            Wishlist::getWish($product) -> delete();
        }
        // add if it is not added
        else {
            $newWhish = new Wishlist([
                'product_id' => $product -> id,
                'user_id' => auth() -> user() -> id,
            ]);

            $newWhish -> save();
        }

        return redirect() -> back();
    }

    /**
     * Returns the page with the product wishlist
     *
     * @return Factory|View
     */
    public function wishlist(): Factory|View
    {
        return view('profile.wishlist');
    }

    /**
     * Show the cart page
     *
     * @return Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function cart(): Factory|View
    {
        return view('cart.index',[
            'items' => Cart::getCart() -> items(),
            'numberOfItems' => Cart::getCart()->numberOfItems(),
            'totalSum' => Cart::getCart() -> total(),
        ]);
    }

    /**
     * Add or edit item to the cart
     *
     * @param NewItemRequest $request
     * @param Product $product
     * @return RedirectResponse
     * @throws Throwable
     */
    public function addToCart(NewItemRequest $request, Product $product): RedirectResponse
    {
        try{
            $request -> persist($product);
            session() -> flash('success', 'You have added/changed an item!');

            return redirect() -> route('profile.cart');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }

    /**
     * Clear the cart and return back
     *
     * @return RedirectResponse
     */
    public function clearCart(): RedirectResponse
    {
        session() -> forget(Cart::SESSION_NAME);
        session() -> flash('success', 'You have cleared your cart!');

        return redirect() -> back();
    }

    /**
     * Remove $product from the cart
     *
     * @param Product $product
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function removeProduct(Product $product): RedirectResponse
    {
        Cart::getCart() -> removeFromCart($product);
        session() -> flash('You have removed a product.');

        return redirect() -> back();
    }

    /**
     * Return table with checkout
     *
     * @return View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkout(): View
    {
        return view('cart.checkout', [
            'items' => Cart::getCart() -> items(),
            'totalSum' => Cart::getCart() -> total(),
            'numberOfItems' => Cart::getCart()->numberOfItems(),

        ]);
    }

    /**
     * Commit purchases from the cart
     *
     * @param MakePurchasesRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function makePurchases(MakePurchasesRequest $request): RedirectResponse
    {
        try{
            $request -> persist();
        }
        catch (RequestException $e){
            $e -> flashError();
            return redirect() -> back();
        }

        return redirect() -> route('profile.purchases');
    }

    /**
     * Return all user's purchases
     *
     * @param string $state
     * @return Factory|View
     */
    public function purchases(string $state = ''): Factory|View
    {
        $purchases = auth() -> user() -> purchases() -> orderByDesc('created_at') -> paginate(20);

        if(array_key_exists($state, Purchase::$states))
            $purchases = auth() -> user() -> purchases() -> where('state', $state) -> orderByDesc('created_at') -> paginate(20);

        return view('profile.purchases.index', [
            'purchases' => $purchases,
            'state' => $state,
        ]);
    }

    /**
     * Return view for an encrypted message
     *
     * @param Purchase $purchase
     * @return Factory|View
     */
    public function purchaseMessage(Purchase $purchase): Factory|View
    {
        return view('profile.purchases.viewmessage', [
            'purchase' => $purchase
        ]);
    }

    /**
     * See purchase details
     *
     * @param Purchase $purchase
     * @return Factory|View
     */
    public function purchase(Purchase $purchase): Factory|View
    {
        return view('profile.purchases.purchase', [
            'purchase' => $purchase
        ]);
    }

    /**
     * Show the delivered confirmation page
     *
     * @param Purchase $purchase
     * @return Factory|View
     */
    public function deliveredConfirm(Purchase $purchase): Factory|View
    {
        return view('profile.purchases.confirmdelivered', [
            'backRoute' => redirect() -> back() -> getTargetUrl(),
            'purchase' => $purchase,
        ]);
    }

    /**
     * Mark Purchase as Delivered
     *
     * @param Purchase $purchase
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function markAsDelivered(Purchase $purchase): RedirectResponse
    {
        try{
            $purchase -> delivered();
        }
        catch(RequestException $e){
            $e -> flashError();
        }

        return redirect() -> route('profile.purchases.single', $purchase);
    }

    /**
     * Returns view for confirming canceled
     *
     * @param Purchase $purchase
     * @return Factory|
     */
    public function confirmCanceled(Purchase $purchase): Factory
    {
        return view('profile.purchases.confirmcanceled', [
            'backRoute' => redirect() -> back() -> getTargetUrl(),
            'sale' => $purchase
        ]);
    }

    /**
     * Make purchase as canceled
     *
     * @param Purchase $purchase
     * @return RedirectResponse
     * @throws Throwable
     */
    public function markAsCanceled(Purchase $purchase): RedirectResponse
    {
        try{
            $purchase -> cancel();
            session() -> flash('success', 'You have successfully marked sale as canceled!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }
        // if this logged user is a vendor
        if($purchase->isVendor())
            return redirect() -> route('profile.sales.single', $purchase);
        return redirect() -> route('profile.purchases.single', $purchase);
    }

    /**
     * Make Dispute for the given purchase
     *
     * @param MakeDisputeRequest $request
     * @param Purchase $purchase
     * @return RedirectResponse
     * @throws Throwable
     */
    public function makeDispute(MakeDisputeRequest $request, Purchase $purchase): RedirectResponse
    {
        try{
            $purchase -> makeDispute($request ->input( 'message'));
            session() -> flash('success', 'You have made a dispute for this purchase!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }

    /**
     * Send a new dispute message to the dispute
     *
     * @param NewDisputeMessageRequest $request
     * @param Dispute $dispute
     * @return RedirectResponse
     */
    public function newDisputeMessage(NewDisputeMessageRequest $request, Dispute $dispute): RedirectResponse
    {
        try{
            $dispute -> newMessage($request ->input( 'message'));
            event(new ProductDisputeNewMessageSent($dispute->purchase,auth()->user()));
            session() -> flash('success', 'You have successfully posted new message for dispute!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }


    /**
     * Leaving feedback
     *
     * @param LeaveFeedbackRequest $request
     * @param Purchase $purchase
     * @return RedirectResponse
     * @throws Throwable
     */
    public function leaveFeedback(LeaveFeedbackRequest $request, Purchase $purchase): RedirectResponse
    {
        try{
            $request -> persist($purchase);
            session() -> flash('success', 'You have left your feedback!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> route('profile.purchases.single', $purchase);
    }

    /**
     * Change vendor's address
     *
     * @param ChangeAddressRequest $request
     * @return RedirectResponse
     */
    public function changeAddress(ChangeAddressRequest $request): RedirectResponse
    {
        try{
            auth() -> user() -> setAddress($request ->input( 'address'), $request ->input( 'coin'));
            session() -> flash('success', 'You have successfully changed your address!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }

    /**
     * Remove the address of the logged user with the given $ id
     *
     * @param $id
     * @return RedirectResponse
     */
    public function removeAddress($id): RedirectResponse
    {
        try{
//            $address = Address::findOrFail($id);
            // Check for number of addresses for coin
//            if(auth() -> user() -> numberOfAddresses($address -> coin) <= 1)
//                throw new RequestException('You must have at least one address for each coin!');
//
            auth() -> user() -> addresses() -> where('id', $id) -> delete();
            session() -> flash('success', 'You have successfully removed your address!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }

    /**
     * Showing all tickets
     *
     * @param Ticket|null $ticket
     * @return Factory|View
     */
    public function tickets(Ticket $ticket = null): Factory|View
    {
        // Tickets
        if(!is_null($ticket)){
            $replies = $ticket -> replies() -> orderByDesc('created_at') -> paginate( config('marketplace.products_per_page'));
        }
        else {
            $replies = collect(); // empty collection
        }


        return view('profile.tickets', [
            'replies' => $replies,
            'ticket' => $ticket
        ]);
    }

    /**
     * Opens new Ticket form
     *
     * @param NewTicketRequest $request
     * @return RedirectResponse|void
     * @throws Throwable
     */
    public function newTicket(NewTicketRequest $request)
    {
        try {
            $newTicket = Ticket::openTicket($request ->input( 'title'));
            TicketReply::postReply($newTicket, $request ->input( 'message'));

            return redirect() -> route('profile.tickets', $newTicket);
        }
        catch(RequestException $e){
            Log::error($e -> getMessage());
            session() -> flash('errormessage', $e -> getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function newTicketMessage(Ticket $ticket, NewTicketMessageRequest $request): RedirectResponse
    {
        try{
            TicketReply::postReply($ticket, $request ->input( 'message'));
        }
        catch (RequestException $e){
            Log::error($e);
            session() -> flash('errormessage', $e -> getMessage());
        }
        return redirect() -> back();
    }
}
