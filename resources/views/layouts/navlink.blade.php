<!-- Navbar -->
<nav id="mainNavbar" class="navbar navbar-expand-lg rounded">
    <div class="container-fluid p-0">
        <!-- Categories Button -->
        <div class="btn-group dropdown h-100">
            <a class="nav-link d-flex align-items-center justify-content-center bg-warning text-black
             px-4 px-lg-5 h-100 text-decoration-none border-0 rounded-start fs-5 fw-extrabold"
               href="#" role="button" data-bs-auto-close="outside" aria-expanded="false"
               style="min-height: 64px; min-width: 350px;">
                <i class="fas fa-list-ul me-4 icon-xl"></i> Categories <i class="fas fa-chevron-right ms-4 rotate-icon icon-xl"></i>
            </a>


            <ul class="dropdown-menu list-group-flush rounded-bottom rounded mt-5"
                style="min-width: 350px;">
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Electronics</span>
                        <i class="fas fa-chevron-right ms-2 "></i>
                    </div>
                    <ul class="dropdown-menu dropdown-submenu rounded-3">
                        <li><a class="dropdown-item py-3" href="#">Computers</a></li>
                        <li><a class="dropdown-item py-3" href="#">Smartphones</a></li>
                        <li><a class="dropdown-item py-3" href="#">Cameras</a></li>
                    </ul>
                </li>
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Clothing</span>
                        <i class="fas fa-chevron-right ms-2"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-submenu rounded-3">
                        <li><a class="dropdown-item py-3" href="#">Men's</a></li>
                        <li><a class="dropdown-item py-3" href="#">Women's</a></li>
                        <li><a class="dropdown-item py-3" href="#">Kids</a></li>
                    </ul>
                </li>
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Home & Kitchen</span>
                        <i class="fas fa-chevron-right ms-2"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-submenu rounded-3">
                        <li><a class="dropdown-item py-3" href="#">Appliances</a></li>
                        <li><a class="dropdown-item py-3" href="#">Furniture</a></li>
                        <li><a class="dropdown-item py-3" href="#">Cookware</a></li>
                    </ul>
                </li>
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Books</span>
                        <i class="fas fa-chevron-right ms-2"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-submenu rounded-3">
                        <li><a class="dropdown-item py-3" href="#">Fiction</a></li>
                        <li><a class="dropdown-item py-3" href="#">Non-Fiction</a></li>
                        <li><a class="dropdown-item py-3" href="#">Educational</a></li>
                    </ul>
                </li>
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Toys</span>
                        <i class="fas fa-chevron-right ms-2"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-submenu rounded-3">
                        <li><a class="dropdown-item py-3" href="#">Action Figures</a></li>
                        <li><a class="dropdown-item py-3" href="#">Board Games</a></li>
                        <li><a class="dropdown-item py-3" href="#">Educational Toys</a></li>
                    </ul>
                </li>
            </ul>
        </div>


        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars text-white"></i>
        </button>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Nav Links -->
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
                               href="#">
                                Dashboard
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
            </ul>

{{--            <!-- Dark Mode Toggle -->--}}
            <div class="pe-4 theme-switch">
                <button class="theme-toggle btn"
                        onclick="window.location.href = '{{ $theme === 'dark' ? route('theme.light') : route('theme.dark') }}'"
                        title="{{ $theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}">
                    <i class="fas {{ $theme === 'dark' ? 'fa-sun' : 'fa-moon' }}"></i>
                </button>
            </div>




        </div>
    </div>
</nav>
