<?php

namespace App\Http\Controllers;

use App\Models\PhysicalProduct;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return View
     * @throws AuthorizationException
     */
    public function show(Product $product): View
    {
        // If a user is logged in
        if (!auth()->guest())
            $this->authorize('view', $product);
        elseif (!$product->active)
            abort(404);


        return view('product.index', [
            'product' => $product,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function showRules(Product $product): View|Application
    {
        // If a user is logged in
        if (!auth()->guest())
            $this->authorize('view', $product);
        elseif (!$product->active)
            abort(404);


        return view('product.rules', [
            'product' => $product,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function showFeedback(Product $product): View
    {
        // If a user is logged in
        if (!auth()->guest())
            $this->authorize('view', $product);
        elseif (!$product->active)
            abort(404);

        return view('product.feedback', [
            'product' => $product,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function showDelivery(PhysicalProduct $product): View|Factory
    {
        // If a user is logged in
        if (auth()->check())
            $this->authorize('view', $product->product);
        elseif (!$product->product->active)
            abort(404);

        return view('product.delivery', [
            'product' => $product->product,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function showVendor(Product $product): View
    {
        // If a user is logged in
        if (!auth()->guest())
            $this->authorize('view', $product);
        elseif (!$product->active)
            abort(404);

        return view('product.vendor', [
            'product' => $product,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product $product
     * @return void
     */
    public function edit(Product $product) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Product $product
     * @return void
     */
    public function update(Request $request, Product $product) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return void
     */
    public function destroy(Product $product) {
        //
    }

    public function cloneProductShow(Product $product): View|Application|Factory
    {

        return view('profile.product.confirmclone')->with([
            'product' => $product
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function cloneProductPost(Product $product): RedirectResponse
    {

        DB::beginTransaction();
        try {
            /**
             * Product cloning
             */
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Clone)';
            $newProduct->save();
            /**
             * Relations
             */

            if ($product->isDigital()) {
                $newDigitalProduct = $product->digital->replicate();
                $newDigitalProduct->id = $newProduct->id;
                $newDigitalProduct->save();
            }
            if ($product->isPhysical()) {
                $newPhysicalProduct = $product->physical->replicate();
                $newPhysicalProduct->id = $newProduct->id;
                $newPhysicalProduct->save();

                // shipping's
                foreach ($product->physical->shippings as $shipping) {
                    $newShipping = $shipping->replicate();
                    $newShipping->product_id = $newProduct->id;
                    $newShipping->save();
                }
            }

            /**
             * Offers
             */
            foreach ($product->offers as $offer) {
                $newOffer = $offer->replicate();
                $newOffer->product_id = $newProduct->id;
                $newOffer->save();
            }

            /**
             * Images
             */
            foreach ($product->images as $image){
                $newImage = $image->replicate();
                $newImage->product_id = $newProduct->id;


                $content = Storage::disk('public')->get($image->image);

                //$destination =  storage_path('app/public/products').lowermost(str::random(32));
                $randomName = strtolower(str::random(32));
                $name = "products/{$randomName}.jpg";

                Storage::disk('public')->put($name,$content);
                $newImage->image = $name;
                $newImage->save();
            }


            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            dd($e);
        }

        return redirect()->route('profile.vendor');
    }
}
