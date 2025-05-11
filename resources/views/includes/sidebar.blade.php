@auth
    <!-- User Info Card -->
    <div class="m-2">
        <div class="d-flex align-items-start gap-3 p-3 rounded-3">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover;">
                @else
                    <span class="text-white fw-bold" style="font-size: 16px;">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <h6 class="mb-0 fw-bold">{{ auth()->user()->username }}</h6>
                <div>
                    <span>Member since {{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <div class="mb-3 ps-5">
        <a class="btn btn-primary btn-sm d-inline me-4 mb-3 px-5 py-1 rounded-4">BTC:</a>
        <a class="btn btn-primary btn-sm d-inline gap-2 mb-3 px-5 py-1 rounded-4">XMR:</a>
    </div>

    <hr>
    
    <div class="row ps-4">
        <ul>
        <li class="d-inline gap-2 mb-3">
            <a href="" class="btn btn-outline-dark px-5 py-2">My Order</a>
        </li>
        <li class="d-inline gap-2 p-2">
            <a href="" class="btn btn-outline-dark px-5 py-2">Favorites</a>
        </li>
        </ul>
    </div>
    <div class="row d-inline-flex gap-1 ps-5">
        <ul>
        <li class="d-inline gap-2 mb-3">
            <a href="" class="btn btn-outline-dark px-4 py-2">My Settings</a>
        </li>
        <li class="d-inline gap-2 p-2">
            <form class="d-inline" action="{{route('auth.signout.post')}}" method="post">
                @csrf               
                <button class="btn btn-outline-dark px-5 py-2" type="submit">Logout</button>
            </form>
        </li>
        </ul>
    </div>
    {{-- <form action="{{route('auth.signout.post')}}" method="post">
        @csrf
        <button class="btn btn-dark text-white border-danger border d-flex ms-auto" type="submit">Logout</button>
    </form> --}}
    <!-- Detailed Search Form -->
    <div class="m-2 card shadow bg-gray-300 p-3">
        <h6 class="fw-bold">Quick Search</h6>
        <form action="{{ url('/search') }}" method="GET">
            <!-- Search Terms -->
            <div class="form-group mb-2">
                <label for="query" class="form-label">Search terms:</label>
                <input
                    type="text"
                    name="query"
                    id="query"
                    class="form-control"
                    placeholder="Enter keywords..."
                    value="{{ request('query') }}"
                />
            </div>

            <!-- User -->
            <div class="form-group mb-2">
                <label for="user" class="form-label">User:</label>
                <input
                    type="text"
                    name="user"
                    id="user"
                    class="form-control"
                    placeholder="Enter username..."
                    value="{{ request('user') }}"
                />
            </div>

            <!-- Category -->
            <div class="form-group mb-2">
                <label for="category" class="form-label">Category:</label>
                <select name="category" id="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Type -->
            <div class="form-group mb-2">
                <label for="type" class="form-label">Product type:</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical</option>
                    <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital</option>
                </select>
            </div>

            <!-- Price Range -->
            <div class="form-group mb-2">
                <label for="price_min" class="form-label">Price range:</label>
                <div class="d-flex gap-2">
                    <input
                        type="number"
                        name="price_min"
                        id="price_min"
                        class="form-control"
                        placeholder="Min"
                        value="{{ request('price_min') }}"
                    />
                    <input
                        type="number"
                        name="price_max"
                        id="price_max"
                        class="form-control"
                        placeholder="Max"
                        value="{{ request('price_max') }}"
                    />
                </div>
            </div>

            <!-- Order By -->
            <div class="form-group mb-3">
                <label for="order_by" class="form-label">Order By:</label>
                <select name="order_by" id="order_by" class="form-control">
                    <option value="newest" {{ request('order_by') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('order_by') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('order_by') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
        </form>
    </div>

    <!-- Browse Categories -->
    <div class="m-2 card shadow bg-gray-300 p-3">
        <h6 class="fw-bold">Browse Categories</h6>
        <ul class="list-group">
            <li class="list-group-item bg-gray-500">
                <a href="{{ url('/search?category=') }}" class="text-decoration-none text-white">All Categories</a>
            </li>
            @foreach($categories as $category)
                <li class="list-group-item bg-gray-500">
                    <a href="{{ url('/search?category=' . $category->id) }}" class="text-decoration-none text-white">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endauth
