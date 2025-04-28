<nav class="navbar navbar-expand-lg bg-dark-subtle rounded py-4 shadow mb-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Left side - Brand -->

            <a
                class="navbar-brand fs-0 fw-bold font-monospace ps-4" href="{{ route('home') }}">{{ config('app.name') }}
            </a>


            <!-- Center - Search -->
            <div class="col-md-4">
                <form action="" method="" class="d-flex" role="search">
                    @csrf
                    <div class="input-group w-100">
                            <span class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">All</span>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                <li><a class="dropdown-item" href="#">Separated link</a></li>
                            </ul>


                        <input class="form-control form-control" type="text" placeholder="Search" id="search" name="Search"
                               value="{{app('request')->input('query')}}">
                        <div class="input-group-append">
                            <button class="btn btn-warning px-4" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right side - Navigation -->
            <div>
                <label for="navbar-toggler" class="navbar-toggler" data-bs-toggle="collapse"
                       data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                       aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </label>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
{{--                    <ul class="navbar-nav mr-auto">--}}
{{--                        @admin--}}
{{--                        <li class="nav-item @isroute('admin') active @endisroute">--}}
{{--                            <a class="nav-link" href="{{ route('admin.index') }}">Admin Panel</a>--}}
{{--                        </li>--}}
{{--                        @endadmin--}}
{{--                        @moderator--}}
{{--                        <li class="nav-item @isroute('admin') active @endisroute">--}}
{{--                            <a class="nav-link" href="{{ route('admin.index') }}">Moderator panel</a>--}}
{{--                        </li>--}}
{{--                        @endmoderator--}}
{{--                        @auth--}}
{{--                            <li class="nav-item @isroute('profile.tickets') active @endisroute">--}}
{{--                                <a class="nav-link" href="{{ route('profile.tickets') }}">Support</a>--}}
{{--                            </li>--}}
{{--                        @endauth--}}

{{--                    </ul>--}}
                    <ul class="navbar-nav">
                        @auth
                            <li class="nav-item @isroute('profile.notifications') active @endisroute">
                                <a href="{{-- {{route('profile.notifications')}}--}}" class="nav-link">
                                    <span><i class="fa fa-bell"></i></span>
                                </a>
                            </li>
                            <li class="nav-item text-center @isroute('profile.cart') active @endisroute">
                                <a class="nav-link w-100 text-black-50" href="#">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                </a>
                            </li>
                            <li class="nav-item @isroute('profile.index') active @endisroute">
                                <a class="nav-link" href="#">Username</a>
                            </li>
                            <li class="nav-item">
                                <form class="form-inline" action="{{route('auth.signout.post')}}" method="post">
                                    @csrf
                                    <button class="btn btn-dark text-muted my-0" type="submit" style="text-decoration: none;">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item d-flex pe-5">
                                <a class="btn btn-outline-primary me-3" href="{{route('auth.signin')}}" role="button">Sign In</a>
                                <a class="btn btn-outline-primary" href="{{route('auth.signup')}}" role="button">Sign Up</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
