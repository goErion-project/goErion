<?php

namespace App\Http\Controllers\Admin;

use App\Events\Support\TicketClosed;
use App\Exceptions\RequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendMessagesRequest;
use App\Http\Requests\Categories\NewCategoryRequest;
use App\Http\Requests\Purchase\ResolveDisputeRequest;
use App\Models\Category;
use App\Models\Dispute;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Random\RandomException;

class AdminController extends Controller
{
    public function __construct()
    {
        $this -> middleware('admin_panel_access');
    }

    private function categoriesCheck(): void
    {
        if(Gate::denies('has-access', 'categories'))
            abort(403);
    }

    private function messagesCheck(): void
    {
        if(Gate::denies('has-access', 'messages'))
            abort(403);
    }

    private function disputesCheck(): void
    {
        if(Gate::denies('has-access', 'disputes'))
            abort(403);
    }

    private function ticketsCheck(): void
    {
        if(Gate::denies('has-access', 'tickets'))
            abort(403);
    }

    /**
     * Return home view of a category section
     *
     * @return Factory|View
     */
    public function index(): Factory|View
    {
        return view('admin.index',
            [
                'total_products' => Product::count(),
                'total_purchases' => Purchase::count(),
                'total_daily_purchases' => Purchase::query()->where('updated_at', '>', Carbon::now()->subDay())->where('state', 'delivered')->count(),
                'total_users' => User::count(),
                'total_vendors' => Vendor::count(),
                'avg_product_price' => Offer::averagePrice(),
                'total_spent' => Purchase::totalSpent(),
                'total_earnings_coin' => Purchase::totalEarningPerCoin()
            ]
        );
    }

    /**
     * Return view with the category list
     *
     * @return View
     */
    public function categories(): View
    {
        $this -> categoriesCheck();

        return view('admin.categories',
            [
                'rootCategories' => Category::roots(),
                'categories' => Category::nameOrdered(),
            ]
        );
    }

    /**
     * Accepts the request for the new Category
     *
     * @param NewCategoryRequest $request
     * @return RedirectResponse
     */
    public function newCategory(NewCategoryRequest $request): RedirectResponse
    {
        $this -> categoriesCheck();
        try{
            $request -> persist();
            session() -> flash('success', 'You have added new category!');
        }
        catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }
        return redirect() -> back();
    }

    /**
     * Remove category
     *
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function removeCategory($id): RedirectResponse
    {
        try {
            $this -> categoriesCheck();
            $catToDelete = Category::query()->findOrFail($id);
            $catToDelete -> delete();

            session() -> flash('success', 'You have successfully deleted category!');
        } catch (\Exception $e){
            session() -> flash('errormessage', $e -> getMessage());
        }

        return redirect() -> back();
    }

    /**
     * Show form for editing category
     *
     * @param $id
     * @return Factory|View
     */
    public function editCategoryShow($id): Factory|View
    {
        $this -> categoriesCheck();
        $categoryToShow = Category::query()->findOrFail($id);


        return view('admin.category', [
            'category' => $categoryToShow,
            'categories' => Category::nameOrdered(),
        ]);

    }

    /**
     * Accepts request for editing category
     *
     * @param $id
     * @param NewCategoryRequest $request
     * @return RedirectResponse
     */
    public function editCategory($id, NewCategoryRequest $request): RedirectResponse
    {
        $this -> categoriesCheck();

        try{
            $request -> persist($id);
            session() -> flash('success', 'You have changed category!');
        }
        catch (RequestException $e){
            session() -> flash('errormessage', $e -> getMessage());
        }
        return redirect() -> route('admin.categories');
    }



    /**
     * Form for the new message
     *
     * @return Factory|View
     */
    public function massMessage(): Factory|View
    {
        $this -> messagesCheck();

        return view('admin.messages');
    }

    /**
     * Send a mass message to a group of users
     *
     * @param SendMessagesRequest $request
     * @return RedirectResponse
     * @throws RandomException
     */
    public function sendMessage(SendMessagesRequest $request): RedirectResponse
    {
        $this -> messagesCheck();
        try{
            $noMessages = $request -> persist();
            session() -> flash('success', "$noMessages messages has been sent!");
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }

    /**
     * Return view with the table of disputes
     *
     * @return Factory|View
     */
    public function disputes(): Factory|View
    {
        $this -> disputesCheck();

        return view('admin.disputes', [
            'allDisputes' => Dispute::query()->paginate(config('marketplace.products_per_page')),
        ]);
    }

    /**
     * Resolve dispute of the purchase
     *
     * @param ResolveDisputeRequest $request
     * @param Purchase $purchase
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function resolveDispute(ResolveDisputeRequest $request, Purchase $purchase): RedirectResponse
    {
        $this -> disputesCheck();

        try{
            $purchase -> resolveDispute($request -> input('winner'));
            session() -> flash('success', 'You have successfully resolved this dispute!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> back();
    }


    /**
     * Single Purchase view for admin
     *
     * @param Purchase $purchase
     * @return Factory|View
     */
    public function purchase(Purchase $purchase): Factory|View
    {
        return view('admin.purchase', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Displayed all paginated tickets
     *
     * @return Factory|View
     */
    public function tickets(): Factory|View
    {
        return view('admin.tickets', [
            'tickets' => Ticket::query()->orderByDesc('created_at') -> paginate(config('marketplace.posts_per_page'))
        ]);
    }


    /**
     * Single ticket Admin view
     *
     * @param Ticket $ticket
     * @return Factory|View
     */
    public function viewTicket(Ticket $ticket): Factory|View
    {
        return view('admin.ticket', [
            'ticket' => $ticket,
            'replies' => $ticket -> replies() -> orderByDesc('created_at') -> paginate(config('marketplace.posts_per_page')),
        ]);
    }

    /**
     * Solve ticket request
     *
     * @param Ticket $ticket
     * @return RedirectResponse
     */
    public function solveTicket(Ticket $ticket): RedirectResponse
    {
        $ticket -> solved = !$ticket -> solved;
        $ticket -> save();
        session() -> flash('successmessage', 'The ticket has been solved!');

        event(new TicketClosed($ticket));

        return redirect() -> back();
    }

    /**
     * List of vendor purchases
     *
     * @return Factory|View
     */
    public function vendorPurchases(): Factory|View
    {
        return view('admin.vendorpurchases', [
            'vendors' => Vendor::orderByDesc('created_at')->paginate(24),
        ]);
    }


    public function removeTickets(Request $request): RedirectResponse
    {
        $type = $request->get('type');
        if ($type == 'all'){
            foreach (Ticket::all() as $ticket){
                $ticket->delete();
            }
        }
        if ($type == 'solved'){
            foreach (Ticket::query()->where('solved',1)->get() as $ticket){
                $ticket->delete();
            }
        }

        if ($type == 'orlder_than_days'){
            foreach (Ticket::query()->where('created_at', '<' ,Carbon::now()->subDays($request->get('days')))->get() as $ticket){
                $ticket->delete();
            }
        }

        return redirect()->back();


    }
}
