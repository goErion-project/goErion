<?php

namespace App\Http\Controllers;

use App\Marketplace\FeaturedProducts;
use App\Marketplace\ModuleManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class IndexController extends Controller
{
    /**
     * Handles the index page request
     *
     * @return Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function home(): Factory|View
    {

        if (!ModuleManager::isEnabled('FeaturedProducts'))
            $featuredProducts = null;
        else
            $featuredProducts = FeaturedProducts::get();

            //fetch latest products
        $latestProducts = Product::orderBy('created_at','desc')->limit(10)->get();

        return view('welcome', [
            'productsView' => session() -> get('products_view'),
            'products' => Product::frontPage(),
            'categories' => Category::roots(),
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,//pass new products to the view
        ]);
    }

    /**
     * Redirection to sing in
     *
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {

        return redirect()->route('auth.signin');
    }

    public function confirmation(Request $request): \Illuminate\Contracts\View\View|Application
    {
        return view('confirmation');
    }

    /**
     * Show category page
     *
     * @param Category $category
     * @return Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function category(Category $category): Factory|View
    {
        return view('category', [
            'productsView' => session() -> get('products_view'),
            'category' => $category,
            'products' => $category->childProducts(),
            'categories' => Category::roots(),
        ]);
    }

    /**
     * Show the vendor page, 6 products, and 10 feedbacks
     *
     * @param Vendor $user
     * @return Factory|View
     */
    public function vendor(Vendor $user): Factory|View
    {
        return view('vendor.index',[
            'vendor' => $user->user
        ]);

    }
    /**
     * Show a page with vendor feedbacks
     *
     * @param Vendor $user
     * @return Factory|View
     */
    public function vendorsFeedbacks(Vendor $user): Factory|View
    {
        return view('vendor.feedback', [
            'vendor' => $user->user,
            'feedback' => $user->feedback()->orderByDesc('created_at')->paginate(20),
        ]);
    }


    /**
     * Sets in session which view we are using
     *
     * @param $list
     * @return RedirectResponse
     */
    public function setView($list): RedirectResponse
    {
        session() -> put('products_view', $list);
        return redirect() -> back();
    }
}
