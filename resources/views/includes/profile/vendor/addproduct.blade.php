<h3 class="card rounded bg-gray-800 text-gray-300 fw-bold p-4 mb-4 text-center">Add product</h3>

    <div class="card rounded bg-gray-800 text-gray-300 fw-bold p-4 mb-4">
    <div class="card mb-4">
        <h5 class="card rounded bg-gray-800 text-gray-300 fw-bold p-2 text-center">Physical product<i class="fas fa-shopping-bag"></i></h5>
        <div class="card text-center bg-gray-500">
            <p class="text-center text-gray-700">Physical products includes shipping options.</p>
            <a href="{{ route('profile.vendor.product.add') }}" class="btn btn-dark hover:bg-yellow-600">Add physical product</a>
        </div>
    </div>
    </div>

<div class="card rounded bg-gray-800 text-gray-300 fw-bold p-4 mb-4">
    <div class="card mb-4">
        <h5 class="card rounded bg-gray-800 text-gray-300 fw-bold p-2 text-center"><i class="fas fa-compact-disc"></i>
            Digital product</h5>
        <div class="card text-center text-gray-700 bg-gray-500">
            <p class="card-text">Digital products can be delivered automatically and they doesn't have shipping options.</p>
            <a href="{{ route('profile.vendor.product.add', 'digital') }}" class="btn btn-dark hover:bg-yellow-600">Add digital product</a>
        </div>
    </div>
</div>

