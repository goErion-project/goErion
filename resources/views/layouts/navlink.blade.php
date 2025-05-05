{{--<!-- Navbar -->--}}
<nav id="mainNavbar" class="navbar navbar-expand-lg rounded-3 border border-gray-400 shadow-sm">
    <div class="container-fluid p-0">
{{--        <!-- Categories Button -->--}}
        <div class="btn-group dropdown h-100">
            <a class="nav-link d-flex align-items-center justify-content-center bg-warning text-black
             px-4 px-lg-5 h-100 text-decoration-none border-0 rounded-start fs-5 fw-extrabold"
               href="#" role="button" data-bs-auto-close="outside" aria-expanded="false"
               style="min-height: 64px; min-width: 350px;">
                <i class="fas fa-list-ul me-4 icon-xl"></i> Categories <i class="fas fa-chevron-right ms-4 rotate-icon icon-xl"></i>
            </a>
            @include('includes.subcategories',['categories'=>$categories])
        </div>
{{--        <!-- Mobile Toggle Button -->--}}
        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars text-white"></i>
        </button>

        {{-- Collapsible Content --}}
        <div class="collapse navbar-collapse bg-gray-950 rounded-end-3" id="navbarContent">
{{--            <!-- Nav Links -->--}}

        {{-- home--}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-home me-2 icon-lg"></i> Home

                    </button>
                    <ul class="dropdown-menu rounded p-1">
                        <li>
                            <a class="dropdown-item bg-secondary-subtle rounded mb-2 fw-bold"
                               href="{{ route('home') }}">
                                Home
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item bg-secondary-subtle rounded "
                               href="#">
                                Dashboard
                            </a></li>
                    </ul>
                </div>
            </li>

            {{-- Messages--}}
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-comment-dots me-2 icon-lg"></i> Messages
                        </button>
                        @auth
                        <ul class="dropdown-menu rounded p-1">
                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded fw-bold hover:bg-yellow-500"
                                   href="{{ route("profile.messages") }}">
                                    Messages
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded mt-2 fw-bold hover:bg-yellow-500"
                                   href="{{ route('profile.bitmessage') }}">
                                    Bitmessage
                                </a>
                            </li>

                        </ul>
                        @endauth
                    </div>
                </li>

             {{-- wallets--}}
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-wallet me-2 icon-lg"></i> Wallets

                        </button>
                        @auth
                        <ul class="dropdown-menu  rounded p-1">
                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded mb-2 fw-bold"
                                   href="{{ route('profile.index') }}">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded "
                                   href="#">
                                    Dashboard
                                </a></li>
                        </ul>
                        @endauth
                    </div>
                </li>

                {{-- cart--}}
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-shopping-cart me-2 icon-lg"></i> Cart

                        </button>
                        @auth
                        <ul class="dropdown-menu rounded p-1">
                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded hover:bg-yellow-500 fw-bold"
                                   href="{{ route('profile.cart') }}">
                                    Items
                                </a>
                            </li>
                        </ul>
                        @endauth
                    </div>
                </li>

             {{--account--}}
                <li class="nav-item">
                <div class="dropdown">
                    <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2 icon-lg"></i> Account

                    </button>
                    @auth
                    <ul class="dropdown-menu  rounded p-1">
                        <li>
                            <a class="dropdown-item bg-secondary-subtle rounded mb-2 fw-bold"
                               href="{{ route('profile.index') }}">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item bg-secondary-subtle rounded "
                               href="">
                                Vendor
                            </a></li>
                    </ul>
                    @endauth
                </div>
                </li>
                {{-- Forum--}}
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn py-3 text-white hover:text-yellow-500 px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-comments me-2 icon-lg"></i> Forum

                        </button>
                        @auth
                        <ul class="dropdown-menu rounded p-1">
                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded fw-bold mb-2 hover:bg-yellow-500"
                                   href="{{ route('profile.notifications') }}">
                                    Notification
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item bg-secondary-subtle rounded fw-bold hover:bg-yellow-500"
                                   href="{{ route('profile.tickets') }}">
                                    Tickets
                                </a>
                            </li>
                        </ul>
                        @endauth
                    </div>
                </li>
            </ul>

{{--            <!-- Dark Mode Toggle -->--}}
            <div class="pe-4 theme-switch">
                <button class="theme-toggle btn"
                        onclick="window.location.href = '{{ $theme === 'dark' ? route('theme.light') : route('theme.dark') }}'"
                        title="{{ $theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}">
                    <i class="fas text-gray-100 fs-3xl {{ $theme === 'dark' ? 'fa-sun' : 'fa-moon' }}"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
