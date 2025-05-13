<div class="card p-2 bg-gray-300 mb-3 rounded-2">
    <div class="card border border-gray-400 bg-gray-300 mb-2">
        <h3 class="card-title px-3 py-2 fw-bold">
            {{ $product->name }}
        </h3>
    </div>
<div class="container-fluid">
    <div class="row d-flex flex-row mb-2 mx-0 w-100">
        <!-- Image moved to the start -->
        <div class="card-img rounded border border-gray-400 pt-2 pb-2 " style="max-height:200px; max-width:200px;">
            <img class="card-img-top rounded" src="{{ asset('storage/' . ($product->frontImage()?->image ?? 'default.png')) }}" alt="{{ $product->name ?? 'Product' }}">
        </div>
        <!-- Card body aligned to the left -->
        <div class="card ms-2 border border-gray-400 bg-gray-300" style="max-width: 334px">
            <div class="card-body text-start">
                <p class="card-text text-gray-800">
                    Sold by:
                    <a href="{{ $product->user ? route('vendor.show', $product->user) : '#' }}" class="badge badge-info text-brown-800 pe-5">
                        {{ $product->user?->username ?? 'Unknown Vendor' }}
                    </a>
                Type: <span class="badge badge-info btn btn-secondary pe-3">{{ $product->type ?? 'Unknown' }}</span>
                    Price: <strong class="pe-3 fa fa-sack-dollar">{{ $product->getLocalPriceFrom() ?? '' }} {{ \App\Marketplace\Utility\CurrencyConverter::getLocalSymbol() }}</strong>
                    Category: <span class="btn btn-secondary btn-sm mt-2"> {{ $product->category?->name ?? 'No Category' }}</span>
                    Type: <span class="badge badge-info btn btn-secondary pe-3">{{ $product->types ?? 'Normal' }}</span>
                    <strong class="ps-4 gap-3">{{ $product->quantity ?? '0' }}</strong> left
                    <strong class="ps-4 gap-3 btn btn-warning btn-sm mt-2 px-4">{{ $product->coins }}</strong>
                </p>
                <a href="{{ route('product.show', $product) }}" class="btn btn-primary d-block">Buy now</a>
            </div>
        </div>
    </div>
</div>
        <div class="card border border-gray-400 bg-gray-300 px-4 py-2">
            <p class="card-title text-end">
                    {{ $product->shipFrom() }} <span class="px-2" style="display: inline-block; transform: scaleX(2);">→</span> {{ $product->shipTo() }}
            </p>
        </div>
</div>
