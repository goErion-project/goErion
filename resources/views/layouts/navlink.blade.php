<!-- Navbar -->
<nav id="mainNavbar" class="navbar navbar-expand-lg rounded">
    <div class="container-fluid p-0">
        <!-- Categories Button -->
        <div class="btn-group dropdown h-100">
            <a class="nav-link d-flex align-items-center justify-content-center bg-warning text-black
             px-4 px-lg-5 h-100 text-decoration-none border-0 rounded-start fs-5 fw-semibold"
               href="#" role="button" data-bs-auto-close="outside" aria-expanded="false"
               style="min-height: 64px; min-width: 350px;">
                <i class="fas fa-list-ul me-4"></i> Categories <i class="fas fa-chevron-right ms-4 rotate-icon"></i>
            </a>


            <ul class="dropdown-menu list-group-flush rounded-bottom rounded mt-5"
                style="min-width: 350px;">
                <li class="dropdown-item-group">
                    <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item">
                        <span>Electronics</span>
                        <i class="fas fa-chevron-right ms-2"></i>
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
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-home me-2"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-comment-dots me-2"></i> Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-wallet me-2"></i> Wallets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-shopping-cart me-2"></i> Cart
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-user me-2"></i> Account
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2" href="#">
                        <i class="fas fa-comments me-2"></i> Forum
                    </a>
                </li>
            </ul>

            <!-- Dark Mode Toggle -->
            <div class="form-check form-switch pe-4">
                <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                <label class="form-check-label text-white" for="darkModeSwitch">
                    <i class="fas fa-moon"></i>
                </label>
            </div>
        </div>
    </div>
</nav>
