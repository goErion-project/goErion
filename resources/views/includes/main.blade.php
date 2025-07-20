<div>
    @include('includes.banners')
</div>
    <div class="row">
    </div>
    @isModuleEnabled('FeaturedProducts')
    @include('featuredproducts::frontpagedisplay')
    @endisModuleEnabled

<!-- New Products Section -->
<div class="row mt-1 mb-4">
    <div class="col">
        <h3 class="fw-bold">New Listings</h3>
    </div>
</div>
<div class="row">
    @if(isset($latestProducts) && $latestProducts->isNotEmpty())
        @foreach($latestProducts as $product)
            <div class="col-md-6 my-md-0 my-2 col-12">
                @include('includes.product.card', ['product' => $product])
            </div>
        @endforeach
    @else
        <div class="col-12 fw-bold">
            <p>No New Listings</p>
        </div>
    @endif
</div>

    <div class="row mt-4">

        <div class="col-md-4">
            <h4>
                Top Vendors
            </h4>
            <hr>
                        @foreach(\App\Models\Vendor::topVendors() as $vendor)
            <table class="table table-borderless table-hover">
                <tr>
                    <td>
                        <a href="{{route('vendor.show',$vendor)}}"
                           class="text-gray-100  btn btn-outline-secondary"
                           style="text-decoration: none; ">{{$vendor->user->username}}</a>
                    </td>
                    <td class="text-right">
                                    <span
                                        class="btn btn-sm @if($vendor->vendor->experience >= 0) btn-primary @else btn-danger @endif active"
                                        style="cursor:default">Level {{ $vendor->getLevel() }}</span>

                    </td>
                </tr>
            </table>
                        @endforeach
        </div>
        <div class="col-md-4">
            <h4>
                Latest orders
            </h4>
            <hr>
                        @foreach(\App\Models\Purchase::latestOrders() as $order)
            <table class="table table-borderless table-hover">
                <tr>
                    <td>
                        <img class="img-fluid" height="23px" width="23px"
                             src="{{ asset('storage/'  . $order->offer->product->frontImage()->image) }}"
                             alt="{{ $order->offer->product->name }}">
                    </td>
                    <td>
                        {{Illuminate\Support\Str::limit($order->offer->product->name,50,'...')}}
                    </td>
                    <td class="text-right">
                        $order->getSumLocalCurrency() $order->getLocalSymbol()
                    </td>
                </tr>
            </table>
                        @endforeach
        </div>

        <div class="col-md-4">
            <h4>
                Rising vendors
            </h4>
            <hr>
            @foreach(\App\Models\Vendor::risingVendors() as $vendor)
                <table class="table table-borderless table-hover">
                    <tr>
                        <td>
                            <a href="{{ route('vendor.show',$vendor) }}"
                               style="text-decoration: none; color:#212529">$vendor->user->username </a>
                        </td>
                        <td class="text-right">
                                    <span
                                        class="btn btn-sm @if($vendor->vendor->experience >= 0) btn-primary @else btn-danger @endif active"
                                        style="cursor:default">Level {{$vendor->getLevel()}}</span>
                        </td>
                    </tr>
                </table>
            @endforeach
        </div>
    </div>
