<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\RequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteProductRequest;
use App\Http\Requests\Admin\DisplayProductsRequest;
use App\Http\Requests\Admin\RemoveProductFromFeaturedReuqest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Check if this admin/moderator has access to edit/remove products
     */
    private function checkProducts(): void
    {
        if(Gate::denies('has-access', 'products'))
            abort(403);
    }


    public function __construct() {
        $this->middleware('admin_panel_access');
    }

    /**
     * Displaying a list of all products in the Admin Panel
     *
     * @param DisplayProductsRequest $request
     * @return Factory|View
     */
    public function products(DisplayProductsRequest $request): Factory|View
    {
        $this -> checkProducts();

        $request->persist();
        $products = $request->getProducts();
        return view('admin.products')->with([
            'products' => $products
        ]);
    }
    public function productsPost(Request $request): RedirectResponse
    {
        $this -> checkProducts();

        return redirect()->route('admin.products',[
            'order_by' => $request->get('order_by'),
            'user' => $request->get('user'),
            'product' => $request ->get(' product')
        ]);
    }

    /**
     * Deleteing a product from an Admin panel
     *
     * @param DeleteProductRequest $request
     * @return RedirectResponse
     */
    public function deleteProduct(DeleteProductRequest $request): RedirectResponse
    {
        $this -> checkProducts();

        try{
            $request->persist();
        } catch (RequestException $e){
            Log::warning($e);
            $e->flashError();
        }
        return redirect()->back();
    }


    /**
     * Method for showing all editing forms for the product
     *
     * //     *
     * @param $id
     * @param string $section
     * @return RedirectResponse|mixed
     *
     * @throws AuthorizationException
     */
    public function editProduct($id, string $section = 'basic'): mixed
    {

        $myProduct = Product::findOrFail($id);
        $this -> authorize('update', $myProduct);


        // if the product is not vendor's
        if($myProduct == null)
            return redirect() -> route('admin.products');

        // digital product can't have a delivery section
        if($myProduct -> isDigital() && $section == 'delivery')
            return redirect() -> route('admin.index');

        // physical product cant have digtial section
        if($myProduct -> isPhysical() && $section == 'digital')
            return redirect() -> route('admin.index');

        // set a product type section
        session() -> put('product_type', $myProduct -> type);

        // string to view a map to retrive which view
        $sectionMap = [
            'basic' =>
                view('admin.product.basic',
                    [
                        'type' => $myProduct -> type,
                        'allCategories' => Category::nameOrdered(),
                        'basicProduct' => $myProduct,]),
            'offers' =>
                view('admin.product.offers',
                    [
                        'basicProduct' => $myProduct,
                        'productsOffers' => $myProduct -> offers() -> get()
                    ]),
            'images' =>
                view('admin.product.images',
                    [
                        'basicProduct' => $myProduct,
                        'productsImages' => $myProduct -> images() -> get(),
                    ]),
            'delivery' =>
                view('admin.product.delivery', [
                    'productsShipping' => $myProduct -> isPhysical() ? $myProduct -> specificProduct() -> shippings() -> get() : null,
                    'physicalProduct' => $myProduct -> specificProduct(),
                    'basicProduct' => $myProduct,
                ]),
            'digital' =>
                view('admin.product.digital', [
                    'digitalProduct' => $myProduct -> specificProduct(),
                    'basicProduct' => $myProduct,
                ]),

        ];

        // if the section is not allowed strings
        if(!in_array($section, array_keys($sectionMap)))
            $section = 'basic';

        return $sectionMap[$section];
    }

    /**
     * List of all purchases
     *
     * @return Factory|View
     */
    public function purchases(): Factory|View
    {
        return view('admin.purchases', [
            'purchases' => Purchase::query()->orderByDesc('created_at')->paginate(config('marketplace.products_per_page')),
        ]);
    }

    public function featuredProductsShow(): View
    {

        $products = Product::query()->where('featured',1)->paginate(25);

        return view('admin.featuredproducts')->with([
            'products' => $products
        ]);
    }

    /**
     * Deleteing a product from an Admin panel
     *
     * @param RemoveProductFromFeaturedReuqest $request
     * @return RedirectResponse
     */
    public function removeFromFeatured(RemoveProductFromFeaturedReuqest $request): RedirectResponse
    {
        $this -> checkProducts();

        try{
            $request->persist();
        } catch (RequestException $e){
            Log::warning($e);
            $e->flashError();
        }
        return redirect()->back();
    }


    public function markAsFeatured(Product $product): RedirectResponse
    {
        $this -> checkProducts();
        $product->featured = 1;
        $product->save();
        return redirect()->back();
    }
}
