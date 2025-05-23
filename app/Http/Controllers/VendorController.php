<?php

namespace App\Http\Controllers;

use App\Exceptions\RedirectException;
use App\Exceptions\RequestException;
use App\Http\Requests\Product\NewBasicRequest;
use App\Http\Requests\Product\NewDigitalRequest;
use App\Http\Requests\Product\NewImageRequest;
use App\Http\Requests\Product\NewOfferRequest;
use App\Http\Requests\Product\NewProductRequest;
use App\Http\Requests\Product\NewShippingOptionsRequest;
use App\Http\Requests\Product\NewShippingRequest;
use App\Http\Requests\Profile\UpdateVendorProfileRequest;
use App\Models\Category;
use App\Models\DigitalProduct;
use App\Models\Image;
use App\Models\PhysicalProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Vendor;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property vendor
 */
class VendorController extends Controller
{
    /**
     * VendorController constructor.
     * Logged-in user must be a vendor to use this controller
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can_edit_products');
    }

    /**
     * Return vendor profile page
     *
     * @return Factory|\Illuminate\View\View
     */
    public function vendor(): Factory|\Illuminate\View\View
    {
        return view(
            'profile.vendor',
            [
                'myProducts' => auth()->user()->products()->paginate(config('marketplace.products_per_page')),
                'vendor' => auth()->user()->vendor
            ]
        );
    }

    /**
     * Show form for basic adding
     *
     * @param string $type
     * @return Factory|\Illuminate\View\View
     */
    public function addBasicShow(string $type = 'physical'): Factory|\Illuminate\View\View
    {
        if ($type == null) {
            $type = 'physical';
        }
        // save type
        session()->put('product_type', $type);

        return view('profile.vendor.addbasic', [
            'type' => $type,
            'allCategories' => Category::nameOrdered(),
            'basicProduct' => session('product_adding'),
        ]);
    }

    /**
     * Authorizing, editing or creating a product
     *
     * @param Product|null $product
     * @throws AuthorizationException
     */
    private function authorizeEditOrCreate(?Product $product): void
    {
        // authorization for editing or creating
        if (!is_null($product)) {
            $this->authorize('update', $product);
        } else {
            auth()->user()->can('create', Product::class);
        }
    }

    /**
     * Accepts POST request for adding new product or editing old one
     *
     * @param NewBasicRequest $request
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function addShow(NewBasicRequest $request, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            return $request->persist($product);
        } catch (RequestException $e) {
            $e->flashError();
        } catch (Exception $e) {
            Log::error($e);
            session()->flash('errormessage', 'Something went wrong try again!');
        }


        return redirect()->back();
    }

    /**
     * Form for adding offers
     *
     * @return Factory|\Illuminate\View\View
     */
    public function addOffersShow(): Factory|\Illuminate\View\View
    {
        return view(
            'profile.vendor.addoffers',
            [
                'productsOffers' => session('product_offers'),
                'basicProduct' => null,
            ]
        );
    }

    /**
     * Generates offer and saves them to a session
     *
     * @param NewOfferRequest $request
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addOffer(NewOfferRequest $request, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            $request->persist($product);
            session()->flash('success', 'You have added new offer!');
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        } catch (Exception $e) {
            session()->flash('errormessage', 'Something went wrong, try again!');
        }

        return redirect()->back();
    }

    /**
     * Remove offer from the product or from db
     *
     * @param $quantity
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function removeOffer($quantity, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            // old offers on products
            if ($product && $product->exists()) {
                if ($product->offers()->count() == 1) {
                    throw new RequestException('You must have at least one offer!');
                }

                $product->offers()->where('min_quantity', $quantity)->update(['deleted' => 1]);
                session()->flash('success', 'You have deleted offer!');
            } // new offers
            else {
                $offers = session('product_offers') ?? collect();

                $offers = $offers->reject(function ($offer, $keys) use ($quantity) {
                    return $offer->quantity != $quantity;
                });

                session()->put('product_offers', $offers);
            }
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Showing form for adding shipping and delivery options
     *
     * @return Factory|\Illuminate\View\View
     */
    public function addDeliveryShow(): Factory|\Illuminate\View\View
    {
        // get physical product from the session
        $physicalProduct = session('product_details');
        if (!($physicalProduct instanceof PhysicalProduct)) {
            $physicalProduct = new PhysicalProduct();
        }

        return view('profile.vendor.adddelivery',
            [
                'physicalProduct' => $physicalProduct ?? new PhysicalProduct,
                'productsShipping' => session('product_shippings'),
            ]
        );
    }

    /**
     * New Delivery option request added
     *
     * @param NewShippingRequest $request
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function newShipping(NewShippingRequest $request, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            $request->persist($product);
            session()->flash('success', 'You have added shipping options!');
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Remove shipping request, removes from session with index
     *
     * @param $index
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function removeShipping($index, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            // changing product
            if ($product && $product->exists()) {
                if ($product->specificProduct()->shippings()->count() <= 1) {
                    throw new RequestException('You must have at least one delivery option!');
                }

                $product->specificProduct()->shippings()->where('id', $index)->update(['deleted' => 1]);
                session()->flash('success', 'You have removed selected shipping!');
            } // for new product
            else {
                $shippingsArray = session('product_shippings') ?? [];
                $shippingsCollection = collect();
                foreach ($shippingsArray as $ship) {
                    $shippingsCollection->push($ship);
                }
                $shippingsCollection = $shippingsCollection->reject(function ($shipping, $key) use ($index) {
                    return $index == $key;
                });

                session()->put('product_shippings', $shippingsCollection);
            }
        } catch (RequestException $e) {
        }

        return redirect()->back();
    }

    /**
     * Shipping settings added
     *
     * @param NewShippingOptionsRequest $request
     * @param PhysicalProduct $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function newShippingOption(NewShippingOptionsRequest $request, PhysicalProduct $product): RedirectResponse
    {
        $this->authorizeEditOrCreate(optional($product)->product);

        try {
            return $request->persist($product);
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }
        return redirect()->back();
    }

    /**
     * Page that displays digital form
     *
     * @return Factory|\Illuminate\View\View
     */
    public function addDigitalShow(): Factory|\Illuminate\View\View
    {
        return view(
            'profile.vendor.adddigital',
            [
                'digitalProduct' => session('product_details') ?? new DigitalProduct()
            ]
        );
    }

    /**
     * Add digital parameters
     *
     * @param NewDigitalRequest $request
     * @param DigitalProduct|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addDigital(NewDigitalRequest $request, DigitalProduct $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate(optional($product)->product);

        try {
            return $request->persist($product);
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Returns form for adding the images
     *
     * @return Factory|\Illuminate\View\View
     */
    public function addImagesShow(): Factory|\Illuminate\View\View
    {
        return view(
            'profile.vendor.addimages',
            [
                'basicProduct' => null,
                'productsImages' => session('product_images'),
            ]
        );
    }

    /**
     * Add image request
     *
     * @param NewImageRequest $request
     * @param Product|null $product
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function addImage(NewImageRequest $request, Product $product = null): RedirectResponse
    {
        $this->authorizeEditOrCreate($product);

        try {
            $request->persist($product);
            session()->flash('success', 'You have added new image!');
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Remove image form Database or session and delete a file from the server
     *
     * @param $id
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function removeImage($id): RedirectResponse
    {
        $removingImage = Image::find($id);
        $this->authorizeEditOrCreate(optional($removingImage)->product);

        try {
            if ($removingImage && $removingImage->exists()) {
                if ($removingImage->product->images()->count() <= 1) {
                    throw new RequestException('You must have at least one image!');
                }
                $imagesProduct = $removingImage->product;
                $removingImage->delete();

                // new default
                if (!$imagesProduct->images()->where('first', 1)->exists()) {
                    $newDefaultImage = $imagesProduct->images()->first();
                    $newDefaultImage->first = 1;
                    $newDefaultImage->save();
                }
            } else {
                $sessionImagesArray = session('product_images') ?? [];
                $sessionImages = collect();
                // fill the collection
                foreach ($sessionImagesArray as $image) {
                    // delete image from storage
                    if ($image->id == $id) {
                        //asset('storage/' . $image -> image)
                        // delete file from sever
                        File::delete('storage/' . $image->image);
                    }
                    $sessionImages->push($image);
                }

                $sessionImages = $sessionImages->reject(function ($image) use ($id) {
                    return $image->id == $id;
                });

                // save an updated collection
                session(['product_images' => $sessionImages]);
            }

            session()->flash('success', 'You have removed the image');
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Make image default for the product
     *
     * @param $id
     * @return RedirectResponse
     */
    public function defaultImage($id): RedirectResponse
    {
        $newDefaultImage = Image::find($id);

        if ($newDefaultImage && $newDefaultImage->exists()) {
            $newDefaultImage->product->images()->update(['first' => 0]);
            $newDefaultImage->first = 1;
            $newDefaultImage->save();
        } else {
            $images = collect();
            $imagesArray = session('product_images') ?? [];
            // fill the collection with images
            foreach ($imagesArray as $image) {
                $images->push($image);
            }

            // change a collection to have only one front image
            $images->transform(function ($image) use ($id) {
                if ($image->id == $id) {
                    $image->first = 1;
                } else {
                    $image->first = 0;
                }

                return $image;
            });

            // save session
            session()->put('product_images', $images);
        }

        session()->flash('success', 'You have set new default image!');

        return redirect()->back();
    }

    /**
     * New Product request
     *
     * @param NewProductRequest $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedirectException
     * @throws \Throwable
     */
    public function newProduct(NewProductRequest $request): RedirectResponse
    {
        auth()->user()->can('create', Product::class);

        try {
            $request->persist();
            session()->flash('success', 'You have added new product!');
        } catch (RequestException $e) {
            session()->flash('errormessage', $e->getMessage());
        } catch (RedirectException $e) {
            session()->flash('errormessage', $e->getMessage());
            return redirect()->to($e->getRoute());
        }

        // Return to Vendor page
        return redirect()->route('profile.vendor');
    }

    /**
     * Modal to confirm to delete the product
     *
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function confirmProductRemove($id): Factory|\Illuminate\View\View
    {
        $product = Product::query()->findOrFail($id);

        return view('profile.product.confirmdelete', [
            'product' => $product
        ]);
    }

    /**
     * Remove product
     *
     * @param $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function removeProduct($id): RedirectResponse
    {
        $productToDelete = Product::findOrFail($id);
        $productToDelete->deactivate();

        session()->flash('success', 'You have successfully deleted product!');
        return redirect()->route('profile.vendor');
    }

    /**
     * Method for showing all editing forms for the product
     *
     * @param $id
     * @param string $section
     * @return RedirectResponse|mixed
     *
     * @throws AuthorizationException
     */
    public function editProduct($id, string $section = 'basic'): mixed
    {
        $myProduct = auth()->user()->products()->where('id', $id)->first();
        $this->authorize('update', $myProduct);


        // if the product is not vendor's
        if ($myProduct == null) {
            return redirect()->route('profile.vendor');
        }

        // digital product can't have a delivery section
        if ($myProduct->isDigital() && $section == 'delivery') {
            return redirect()->route('profile.vendor');
        }

        // physical product cant have a digital section
        if ($myProduct->isPhysical() && $section == 'digital') {
            return redirect()->route('profile.vendor');
        }

        // set a product type section
        session()->put('product_type', $myProduct->type);

        // string to view a map to retrieve which view
        $sectionMap = [
            'delivery' =>
                view('profile.vendor.adddelivery', [
                    'productsShipping' => $myProduct->isPhysical() ? $myProduct->specificProduct()->shippings()->get(
                    ) : null,
                    'physicalProduct' => $myProduct->specificProduct(),
                    'basicProduct' => $myProduct,
                ]),
            'digital' =>
                view('profile.vendor.adddigital', [
                    'digitalProduct' => $myProduct->specificProduct(),
                    'basicProduct' => $myProduct,
                ]),
            'basic' =>
                view(
                    'profile.vendor.addbasic',
                    [
                        'type' => $myProduct->type,
                        'allCategories' => Category::nameOrdered(),
                        'basicProduct' => $myProduct,
                    ]
                ),
            'offers' =>
                view(
                    'profile.vendor.addoffers',
                    [
                        'basicProduct' => $myProduct,
                        'productsOffers' => $myProduct->offers()->get()
                    ]
                ),
            'images' =>
                view(
                    'profile.vendor.addimages',
                    [
                        'basicProduct' => $myProduct,
                        'productsImages' => $myProduct->images()->get(),
                    ]
                )
        ];

        // if the section is not allowed strings
        if (!in_array($section, array_keys($sectionMap))) {
            $section = 'basic';
        }
        return $sectionMap[$section];
    }


    /**
     * Table with the sales
     */
    public function sales($state = ''): View|Application|Factory
    {
        $sales = auth() -> user() -> vendor -> sales() -> with('offer') -> paginate(20);
        if(array_key_exists($state, Purchase::$states))
            $sales = auth() -> user() -> vendor -> sales() -> where('state', $state) -> paginate(20);

        // update unvisited sales
        auth() -> user() -> vendor -> sales() -> where('read', false) -> update(['read' => true]);

        return view('profile.vendor.sales', [
            'sales' => $sales,
            'state' => $state
        ]);
    }

    /**
     * Return view for the sale
     *
     * @param Purchase $sale
     * @return Factory|\Illuminate\View\View
     */
    public function sale(Purchase $sale): Factory|\Illuminate\View\View
    {
        if (!$sale->isAllowed()) {
            abort(404, 'Sale not found or not allowed.');
        }

        return view('profile.vendor.sale', [
            'purchase' => $sale,
        ]);
    }

    /**
     * Returns view for confirming sent
     *
     * @param Purchase $sale
     * @return Factory|\Illuminate\View\View
     */
    public function confirmSent(Purchase $sale): Factory|View
    {
        return view('profile.purchases.confirmsent', [
            'backRoute' => redirect() -> back() -> getTargetUrl(),
            'sale' => $sale
        ]);
    }

    /**
     * Runs procedure for marking sale as sent
     *
     * @param Purchase $sale
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function markAsSent(Purchase $sale): RedirectResponse
    {
        try{
            $sale -> sent();
            session() -> flash('success', 'You have successfully marked sale as sent!');
        }
        catch (RequestException $e){
            $e -> flashError();
        }

        return redirect() -> route('profile.sales.single', $sale);
    }

    /**
     * Update profile description and background for vendor
     */
    public function updateVendorProfilePost(UpdateVendorProfileRequest $request): RedirectResponse
    {

        try{
            $request->persist();
        } catch (RequestException $e){
            $e -> flashError();
        }
        return redirect()->back();
    }


}
