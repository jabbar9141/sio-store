<div class="container-fluid bg-dark mb-30 d-none">
    <div class="row px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a class="d-flex align-items-center justify-content-between cat-btn w-100" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; padding: 0 30px;">
                <h6 class="text-dark m-0"><i class="fa fa-bars mr-2"></i>Categories</h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>
            <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-light" id="navbar-vertical" style="width: calc(100% - 30px); z-index: 999;">
                <div class="navbar-nav w-100">
                    @php
                    $categories = App\Models\CategoryModel::orderBy('category_id', 'desc')->limit(50)->get();
                    @endphp
                    @if (isset($categories))
                    @foreach ($categories as $category)
                    <a href="{{ route('store.showCategory', $category->category_slug) }}" class="nav-item nav-link">{{ $category->category_name }}</a>
                    @endforeach
                    @endif
                </div>
            </nav>
        </div>
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 py-lg-0 px-0">
                <a href="/" class="text-decoration-none d-block d-lg-none">
                    <img src="{{ asset('backend_assets') }}/images/siostore_logo.png" width="180" alt="" />
                </a>
                <div class="row align-items-center">
                    <button type="button" class="navbar-toggler" style="outline: none;" data-toggle="collapse" data-target="#searchbarCollapse">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="navbar-toggler" style="outline: none;" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse justify-content-between" id="searchbarCollapse">
                    <form action="{{ route('store.searchProducts') }}" method="GET">
                        <div class="d-block d-lg-none mt-2">
                            <div class="input-group mt-3">
                                <input type="text" name="keyword" class="form-control search-inp" placeholder="Search for products">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-transparent text-primary search-btn"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="d-block d-lg-none mt-2">

                        <div class=" mx-2 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" id="search_location_m" class="form-control location-picker" placeholder="Ship To..." value="{{ session('ship_to_str') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-left-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                </div>
                            </div>
                            <div id="location_suggestions_m"></div>
                        </div>



                        <div class="d-inline-flex justify-content-end w-100 mt-2">
                            <div id="location_suggestions"></div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light rounded text-light bg-dark dropdown-toggle" data-toggle="dropdown">My
                                    Account</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @auth
                                    <button class="dropdown-item" type="button"> <a href="{{ url('/dashboard') }}">Dashboard</a></button>
                                    @endauth
                                    @guest
                                    <button class="dropdown-item" type="button"><a href="{{ url('/login') }}">Sign
                                            in</a></button>
                                    <button class="dropdown-item" type="button"><a href="{{ url('/register') }}">Sign
                                            up</a></button>
                                    @endguest
                                </div>
                            </div>
                            <div class="btn-group mx-2">
                                <button type="button" class="btn btn-sm btn-light rounded text-light bg-dark dropdown-toggle" data-toggle="dropdown">EUR</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <button class="dropdown-item" type="button">EUR</button>
                                    {{-- <button class="dropdown-item" type="button">GBP</button>
                                    <button class="dropdown-item" type="button">CAD</button> --}}
                                </div>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn rounded btn-sm btn-light text-light bg-dark dropdown-toggle" data-toggle="dropdown">EN</button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <button class="dropdown-item" type="button">IT</button>
                                </div>
                            </div>
                            <div class="navbar-nav py-0 d-block px-2">
                                @auth
                                <a href="{{ route('wishlist.index') }}" class="btn px-0">
                                    <i class="fas fa-heart text-primary"></i>
                                    <span class="badge text-secondary border border-secondary rounded-circle" style="padding-bottom: 2px;">
                                        @php
                                        echo \App\MyHelpers::userLikesItemCount();
                                        @endphp
                                    </span>
                                </a>
                                @endauth
                                <a href="{{ route('store.my-cart') }}" class="btn px-0 ml-3 position-relative">
                                    <i class="fas fa-shopping-cart text-primary"></i>
                                    <span class="cart-count text-primary border border-secondary rounded-circle" style="padding-bottom: 2px;">
                                        @if (null != session('cart') && count(session('cart')) > 0)
                                        {{ count(session('cart')) }}
                                        @else
                                        0
                                        @endif
                                    </span>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div id="location_suggestions"></div>
                    <div class="navbar-nav mr-auto py-0">
                        <a href="/" class="nav-item nav-link active">Home</a>
                        <a href="{{ route('store.showAllCategory') }}" class="nav-item nav-link">Categories</a>
                        <a href="{{ route('store.showAllBrand') }}" class="nav-item nav-link">Brands</a>
                        <a href="{{ route('store.showAllVendor') }}" class="nav-item nav-link">Vendor</a>
                        {{-- <a href="detail.html" class="nav-item nav-link">Shop Detail</a> --}}
                        <div class="d-block d-lg-none" style="border-top: white 2px !important;">
                            <hr class="custom-divider">
                            <a href="/about" class="nav-item nav-link">About</a>
                            <a href="/contact" class="nav-item nav-link">Contact</a>
                            <a href="/help" class="nav-item nav-link">Help</a>
                            <a href="/faq" class="nav-item nav-link">FAQ</a>
                        </div>
                    </div>
                    <div class="navbar-nav ml-auto py-0 d-none d-lg-block">
                        <div class="row py-1 px-xl-5">
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="d-inline-flex align-items-center h-100">
                                    <a class="text-body mr-3 link-text" href="/about">About</a>
                                    <a class="text-body mr-3 link-text" href="/contact">Contact</a>
                                    <a class="text-body mr-3 link-text" href="/help">Help</a>
                                    <a class="text-body mr-3 link-text" href="/faq">FAQs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>

<style>
    /* Custom CSS for location picker input */
    .location-picker {
        width: 200px;
        /* Adjust width as needed */
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 8px 12px;
        /* Adjust padding as needed */
        font-size: 14px;
        /* Adjust font size as needed */
        color: black;
    }

    .navbar-toggler {
        outline: none;
        border: none;
    }

    .location-picker:focus {
        outline: none;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .search-btn {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        background-color: #495057;
    }

    .search-inp {
        border: 1px solid gray;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        background: transparent;
    }

    .acty {
        display: inline-flex !important;
    }

    .custom-divider {
        border-color: white !important;
    }

    .search-inp:focus {
        border: 2px solid #1575b8;
        background: transparent;
        color: white;
    }

    .search-btn {
        border: 1px solid gray;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        outline: none;
    }

    .container-fluid.bg-dark .cat-btn {
        background-color: #1575b8;
        color: white !important;
        text-decoration: none;
    }

    .container-fluid.bg-dark .cat-btn:hover {
        background-color: lightblue;
        text-decoration: none;
        color: white
    }

    .link-text {
        color: lightblue !important;
    }

    .link-text:hover {
        color: white !important;
    }
</style>