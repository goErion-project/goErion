<div class="card">
    <img class="card-img-top" src="{{ asset('storage/' . ($product->frontImage()?->image ?? 'default.png')) }}" alt="{{ $product->name ?? 'Product' }}">
    <div class="card-body">
        <a href="{{ route('product.show', $product) }}">
            <h4 class="card-title">{{ $product->name ?? 'Unnamed Product' }}</h4>
        </a>
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