<?php

namespace App\Http\Controllers;

use App\Marketplace\FeaturedProducts;
use App\Marketplace\ModuleManager;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class IndexController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function home(): View
    {
        if (!ModuleManager::isEnabled('FeaturedProducts'))
            $featuredProducts = null;
        else
            $featuredProducts = FeaturedProducts::get();

        return view('welcome', [
            'productsView' => session() -> get('products_view'),
            'products' => Product::frontPage(),
            'categories' => Category::roots(),
            'featuredProducts' => $featuredProducts
        ]);
    }

    public function login(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('auth.signin');
    }
}
