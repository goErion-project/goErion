<nav class="navbar navbar-expand-lg border border-gray-400 bg-gray-300 rounded-3 py-3 shadow-sm mb-4">
    <div class="container-fluid">
        <!-- Brand -->
        <div class="d-flex align-items-center">
            <a class="navbar-brand fs-2 fw-extrabold text-brown-900 ps-2 ps-lg-4 fs-3xl" href="{{ route('home') }}"
               style="letter-spacing: 1px;">
                {{ config('app.name') }}
            </a>
        </div>
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

{{--        <!-- Search Form -->--}}
        @include('layouts.search')
{{--        <!-- Search Form -->--}}

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @admin
                <li class="nav-item @isroute('admin') active @endisroute">
                    <a class="nav-link btn bg-gray-700 text-gray-200" href="{{ route('admin.index') }}">Admin Panel</a>
                </li>
                @endadmin
                @moderator
                <li class="nav-item @isroute('admin') active @endisroute">
                    <a class="nav-link" href="{{ route('admin.index') }}">Moderator Panel</a>
                </li>
                @endmoderator
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item @isroute('profile.index') active @endisroute d-flex align-items-center">
                        <a class="nav-link px-2 d-flex align-items-center gap-2" href="{{ route('profile.index') }}">
                            <div class="rounded-circle bg-brown-600 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <span class="text-white fw-bold" style="font-size: 16px;">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="d-flex flex-column">
                                <div class="">
                                    <span>Logged in as <b>{{ auth()->user()->username }}</b></span>
                                </div>
                                <div>
                                    <span class="fs-sm">BTC:</span> 0.00000000 /
                                    <span class="wallet-label">XMR:</span> 0.00000000
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item pe-5">
                        <form action="{{route('auth.signout.post')}}" method="post">
                            @csrf
                            <button class="btn btn-dark text-white border-danger border d-flex ms-auto" type="submit">Logout</button>
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
