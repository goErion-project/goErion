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
                <p class="card-subtitle">
                    From: <strong>{{ $product->getLocalPriceFrom() ?? 'N/A' }} {{ \App\Marketplace\Utility\CurrencyConverter::getLocalSymbol() }}</strong>
                    - {{ $product->category?->name ?? 'No Category' }}
                    - <span class="badge badge-info">{{ $product->type ?? 'Unknown' }}</span>
                </p>
                <p class="card-text">
                    Posted by
                    <a href="{{ $product->user ? route('vendor.show', $product->user) : '#' }}" class="badge badge-info">
                        {{ $product->user?->username ?? 'Unknown Vendor' }}
                    </a>,
                    <strong>{{ $product->quantity ?? '0' }}</strong> left
                </p>
                <a href="{{ route('product.show', $product) }}" class="btn btn-primary d-block">Buy now</a>
            </div>
        </div>
    </div>
</div>
        <div class="card border border-gray-400 bg-gray-300 px-4 py-2">
            <p class="card-title text-end">
                    {{ $product->shipFrom() }} ---> {{ $product->shipTo() }}
            </p>
        </div>
</div>
