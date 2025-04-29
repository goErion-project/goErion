<nav class="navbar navbar-expand-lg border border-gray-300 bg-gray-300 rounded-3 py-3 shadow mb-4">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fs-2 fw-extrabold text-brown-900 ps-2 ps-lg-4 fs-3xl" href="{{ route('home') }}"
           style="letter-spacing: 1px;">
            {{ config('app.name') }}
        </a>


        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Search Form -->
            <div class="d-flex justify-content-center flex-grow-1 mx-lg-4">
                <form action="" method="" class="d-flex" role="search" style="max-width: 600px; width: 100%;">
                    @csrf
                    <div class="input-group">
                        <button class="btn btn-outline-dark bg-white hover:text-yellow-500 px-4" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            All
                        </button>
                        <ul class="dropdown-menu bg-white">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                            <li><a class="dropdown-item" href="#">Separated link</a></li>
                        </ul>
                        <input class="form-control" type="text" placeholder="Search" id="search"
                               name="Search" value="{{app('request')->input('query')}}">
                        <button class="btn btn-warning px-4" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Navigation Items -->
            <ul class="navbar-nav">
                @auth
                   {{-- username: {{ auth()->user()->username }}--}}
                    <li class="nav-item @isroute('profile.index') active @endisroute d-flex align-items-center">
                        <a class="nav-link px-2 d-flex align-items-center" href="{{ route('profile.index') }}">
                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <span class="text-gray-800 fw-bold" style="font-size: 14px;">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</span>
                                @endif
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <form action="{{route('auth.signout.post')}}" method="post">
                            @csrf
                            <button class="btn btn-dark text-white border-danger border me-4" type="submit">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item d-flex gap-2 pe-5">
                        <a class="btn btn-outline-dark" href="{{route('auth.signin')}}">Sign In</a>
                        <a class="btn btn-outline-dark" href="{{route('auth.signup')}}">Sign Up</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
