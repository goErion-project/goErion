<div class="row mt-1 mb-2">
    <div class="col mb-4">
        <h3 class="fw-bold">Featured Products</h3>
    </div>
</div>
<div class="row">
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        @foreach($featuredProducts as $product)
            <div class="col-md-6 my-md-0 my-2 col-12">
                @include('includes.product.card', ['product' => $product])
            </div>
        @endforeach
    @else
        <div class="col-12 fw-bold">
            <p>No Featured Products</p>
        </div>
    @endif
</div>
