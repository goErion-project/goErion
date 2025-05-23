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
@endauth
    <div class="m-2 card shadow bg-gray-300 p-3">
        <h3 class="card text-center hs-4 bg-gray-500 fw-bold py-3 rounded-2 mb-4">Quick Search</h3>
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

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
        </form>
    </div>

    <!-- Browse Categories -->
    <div class="m-2 card shadow bg-gray-300 p-3">
        <h3 class="card text-center hs-4 bg-gray-500 fw-bold py-3 rounded-2 mb-4">Browse Categories</h3>
        <ul class="list-group">
            @foreach($categories as $category)
                <li class="card bg-gray-300 border-sm mb-2 py-2 ps-3">
                    <a href="{{ url('/search?category=' . $category->id) }}" class="text-decoration-none text-dark fw-bold">
                        {{ $category->name }}
                        <span class="badge text-bg-secondary">{{ $category -> num_products }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
{{--@endauth--}}
