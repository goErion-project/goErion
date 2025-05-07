<div class="row mt-1 mb-2">
    <div class="col">
        <h4>Featured Products</h4>
    </div>
</div>
<div class="row">
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        @foreach($featuredProducts as $product)
            <div class="col-md-4 my-md-0 my-2 col-12">
                @include('includes.product.card', ['product' => $product])
            </div>
        @endforeach
    @else
        <div class="col-12">
            <p>No featured products available at the moment.</p>
        </div>
    @endif
</div>