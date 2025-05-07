<?php

namespace Modules\FeaturedProducts\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Product; // Assuming you have a Product model

class FeaturedProductsController extends Controller
{
   /**
     * Display a listing of the resource.
     * @return Response
     */
   public function index()
    {    
        // Fetch featured products
        $featuredProducts = Product::where('featured', true)->get();

        // Pass the data to the view
        return view('featuredproducts::frontpagedisplay', compact('featuredProducts'));
        
    }
    /**
     * Show the form for creating a new resource.
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('featuredproducts::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Factory|View|Application
     */
    public function store(Request $request): Factory|View|Application
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Factory|View|Application
     */
    public function show(int $id): Factory|View|Application
    {
        return view('featuredproducts::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Factory|View|Application
     */
    public function edit(int $id): Factory|View|Application
    {
        return view('featuredproducts::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Factory|View|Application
     */
    public function update(Request $request, int $id): Factory|View|Application
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Factory|View|Application
     */
    public function destroy(int $id): Factory|View|Application
    {
        //
    }
}
