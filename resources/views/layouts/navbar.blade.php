<nav class="navbar navbar-expand-lg bg-dark-subtle rounded py-3 shadow mb-4">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fs-2 fw-bold font-monospace ps-2 ps-lg-4" href="{{ route('home') }}"
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
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            All
                        </button>
                        <ul class="dropdown-menu">
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
                    <li class="nav-item">
                        <a class="nav-link px-2" href="#">Username</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{route('auth.signout.post')}}" method="post">
                            @csrf
                            <button class="btn btn-dark text-white border-danger boder me-4" type="submit">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item d-flex gap-2 pe-5">
                        <a class="btn btn-outline-primary" href="{{route('auth.signin')}}">Sign In</a>
                        <a class="btn btn-outline-primary" href="{{route('auth.signup')}}">Sign Up</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
