<style>
    .goog-te-gadget-simple {
        padding: 4px;
    }

    .skiptranslate iframe {
        display: none !important;
    }

    body {
        top: 0px !important;
    }

    .cart-count {
        position: absolute;
        top: 0px;
        right: -19px;
        background: white;
        font-size: 13px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .location-box {
        flex: 1;
    }

    .sidebar-nav {
        flex-direction: unset;
    }
</style>
@php
    use App\Models\Currency;
@endphp
<div clas="container-fluid">
    <div style="color: white; display: none;"
        class="row bg-custom align-items-center justify-content-between text-primary py-3 px-xl-5 d-none d-lg-flex">

        <div class="col-lg-4 d-inline-flex">
            <a href="{{ url('/') }}" class="text-decoration-none">
                <img src="{{ asset('backend_assets') }}/images/siostore_logo.png" width="180" alt="" />
            </a>
            <div class=" mx-2 location-box text-center">
                <div class="align-items-center" id="deliveryModalToggle">
                    <div style="margin-bottom: 0;">
                        <div>
                            <span style="margin-bottom: 0;">Deliver to</span>
                        </div>
                        <div class="d-inline-flex">
                            <i class="fas fa-map-marker-alt text-primary" data-bs-toggle="modal"
                                data-bs-target="#deliveryModal"></i>
                            <h6
                                style="display: inline-block; margin-bottom: 0; padding-top: 0; padding-bottom: 0; color: #1575b8;">
                                {{ session('ship_to_str') }}
                            </h6>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="deliveryModal" tabindex="-1" aria-labelledby="deliveryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deliveryModalLabel">Delivery Details</h5>
                                <button type="button" class="btn-close" id="modalCloseButton">X</button>
                            </div>
                            <div class="modal-body">
                                <form id="delivery_details">
                                    <div class="col-12">
                                        @php
                                            $countries = App\Models\Country::get();
                                        @endphp
                                        <select class="form-control w-100" name="delivery_country" id="delivery_country"
                                            required>
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ (int) session('country_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name ?? $c->iso2 }}</option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="text" id="city_address" class="form-control"
                                            placeholder="City Ship To..." value="" autocomplete="off" autofocus> --}}
                                    </div>
                                    <div class="my-2 col-12">
                                        <select name="delivery_city" id="delivery_city" required
                                            class="w-100 form-control">
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <button class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                                <div id="location_suggestions"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--
                <div class="input-group input-group-sm">
                    <input type="text" id="search_location" class="form-control location-picker"
                        placeholder="Ship To..." value="{{ session('ship_to_str') }}">
                <div class="input-group-append">
                    <span class="input-group-text bg-white border-left-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                </div>
            </div>
            --}}
                {{-- <div id="location_suggestions"></div> --}}
            </div>
        </div>

        <div class="col-lg-4 col-6 text-left">
            <form action="{{ route('store.searchProducts') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control search-inp"
                        placeholder="Search for products">
                    <div class="input-group-append">
                        <button type="submit" class="input-group-text text-primary search-btn"
                            style="background-color: #1575b8; border-top-right-radius: 5px; border-bottom-right-radius: 5px;"><i
                                class="fa fa-search" style="color: white"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-4">
            <div class="d-inline-flex align-items-center">
                <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
                </script>
                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'en,it,fr,nl,pt',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                        }, 'google_translate_element');
                    }
                    googleTranslateElementInit()
                </script>


                <!--<div class="btn-group">-->
                <!--    <button type="button" class="btn btn-sm btn-light dropdown-toggle"-->
                <!--        data-toggle="dropdown">EN</button>-->
                <!--    <div class="dropdown-menu dropdown-menu-right">-->
                <!--        <button class="dropdown-item" type="button">IT</button>-->
                <!--    </div>-->
                <!--</div>-->
                @php
                    $currencies = Currency::all();
                @endphp

                <div class="btn-group mx-2">
                    {{-- <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">{{ session()->get('session_currency') ?? 'EUR' }}</button> --}}
                    <form action="" id="currencyForm">
                        <select name="currency_id" id="" onchange="form.submit()">
                            @if (session('currency_id') && session('currency_id') > 0)
                                <option value="0">EUR</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected($currency->id == session('currency_id') ? true : false)>
                                        {{ $currency->country_code }}</option>
                                @endforeach
                            @else
                                <option value="0" @selected(true)>EUR</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">
                                        {{ $currency->country_code }}</option>
                                @endforeach
                            @endif

                        </select>
                    </form>

                    {{-- <button type="button" class="btn btn-sm btn-light dropdown-toggle"
                        data-toggle="dropdown">EUR</button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <button class="dropdown-item currencyLocalization" data-currency="USD"
                            type="button">USD</button>
                        <button class="dropdown-item currencyLocalization" data-currency="EUR"
                            type="button">EUR</button>
                        <button class="dropdown-item currencyLocalization" data-currency="NGN"
                            type="button">NGN</button>
                        <button class="dropdown-item currencyLocalization" data-currency="GHS"
                            type="button">GHS</button>
                        <button class="dropdown-item currencyLocalization" data-currency="GBP"
                            type="button">GBP</button>
                        <button class="dropdown-item currencyLocalization" data-currency="XAF"
                            type="button">XAF</button>
                    </div> --}}
                </div>
                <div class="btn-group ml-2">
                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">My
                        Account</button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @auth
                            <button class="dropdown-item" type="button"> <a
                                    href="{{ route('dashboard') }}">Dashboard</a></button>
                        @endauth
                        @guest
                            <button class="dropdown-item" type="button"><a href="{{ url('/login') }}">Sign
                                    in</a></button>
                            <button class="dropdown-item" type="button"><a href="{{ url('/register') }}">Sign
                                    up</a></button>
                        @endguest
                    </div>
                </div>
                <div class="navbar-nav ml-auto py-0 d-none d-lg-block">
                    @auth
                        <a href="{{ route('wishlist.index') }}" class="btn px-0 ml-3 position-relative">
                            <i class="fas fa-heart text-primary"></i>
                            <span class="cart-count text-primary border border-secondary rounded-circle">
                                @php
                                    echo \App\MyHelpers::userLikesItemCount();
                                @endphp
                            </span>
                        </a>
                    @endauth
                    <a href="{{ route('store.my-cart') }}" class="btn px-0 ml-3 position-relative">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <span class="cart-count text-primary border border-secondary rounded-circle">
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

    </div>
</div>

<div class="container-fluid px-0">
    <header class="category-header d-flex align-items-center px-2">
        <div class="left-bar">

            <div class="d-flex justify-content-between">
                <div class="mobile-logo d-lg-none">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('backend_assets') }}/images/siostore_logo.png" width="180"
                            alt="" />
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="d-flex align-items-center" id="openButton">
                        <i class="ri-menu-line"></i>
                    </a>

                    <div class="row align-items-center d-lg-none">
                        <button type="button" class="navbar-toggler" style="outline: none;" data-toggle="collapse"
                            data-target="#searchbarCollapse">
                            <i class="fas fa-search text-white"></i>
                        </button>

                    </div>
                </div>
            </div>

            <div class="collapse navbar-collapse justify-content-between" id="searchbarCollapse">
                <form action="{{ route('store.searchProducts') }}" method="GET">
                    <div class="d-block d-lg-none mt-2">
                        <div class="input-group mt-3">
                            <input type="text" name="keyword" class="form-control search-inp"
                                placeholder="Search for products">
                            <div class="input-group-append">
                                <button type="submit"
                                    class="input-group-text bg-transparent text-primary search-btn"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

        </div>
        <nav class="nav-list d-none d-lg-block">
            <ul class="d-flex mb-0">

                <li>
                    <a href="{{ url('/') }}" class="nav-item nav-link active">Home</a>
                </li>
                <li>
                    <a href="{{ url('/store/category') }}" class="nav-item nav-link">Categories</a>
                </li>
                <li>
                    <a href="{{ url('/store/brand') }}" class="nav-item nav-link">Brands</a>
                </li>
                <li>
                    <a href="{{ url('/store/vendor') }}" class="nav-item nav-link">Vendor</a>
                </li>
                <li>
                    <a href="{{ url('/about') }}" class="nav-item nav-link">About</a>
                </li>
                <li>
                    <a href="{{ url('/contact') }}" class="nav-item nav-link">Contact</a>
                </li>
                <li>
                    <a href="{{ url('/help') }}" class="nav-item nav-link">Help</a>
                </li>
                <li>
                    <a href="{{ url('/faq') }}" class="nav-item nav-link">FAQ</a>
                </li>

            </ul>
        </nav>
    </header>
</div>

<!-- offCanvas  -->
<div class="overlay-offcanvas">
    <div id="offcanvas" class="offcanvas" style="right: 0px;">
        <div id="closeButton">&#10006;</div>
        <div class="d-flex align-items-center gap-3 cmntBorder">
            <div class="customer-profile d-flex">
                <div class="user-icon">
                    <img src="./SioStore - Home_files/profile-user.png" alt="">
                </div>
                <div class="sign-in-text">
                    <b class="text-white">Hello</b>
                </div>
            </div>
        </div>
        <div class="cmt-body h-100 ">
            <div class="side-menu-content  h-100 overflow-y-scroll pb-4">


                <div class="d-inline-flex justify-content-between w-100 mt-2 px-2 pr-3">
                    <div>
                        <div class="btn-group">
                            <button type="button"
                                class="btn btn-sm btn-light rounded text-light bg-dark dropdown-toggle"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">My
                                Account</button>
                            <div class="dropdown-menu dropdown-menu-right" class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton">
                                @auth
                                    <button class="dropdown-item" type="button"> <a
                                            href="{{ route('dashboard') }}">Dashboard</a></button>
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
                            <form action="" id="currencyForm">
                                <select name="currency_id" id="" onchange="form.submit()">
                                    @if (session('currency_id') && session('currency_id') > 0)
                                        <option value="0">EUR</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" @selected($currency->id == session('currency_id') ? true : false)>
                                                {{ $currency->country_code }}</option>
                                        @endforeach
                                    @elseif (session('currency_id') && session('currency_id') == 0)
                                        <option value="0" selected>EUR</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}">
                                                {{ $currency->country_code }}</option>
                                        @endforeach
                                    @else
                                        <option value="0">EUR</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" @selected($currency->status == true ? true : false)>
                                                {{ $currency->country_code }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </form>
                            {{-- <button type="button"
                                class="btn btn-sm btn-light rounded text-light bg-dark dropdown-toggle"
                                data-toggle="dropdown">USD</button> --}}




                            {{-- <div class="dropdown-menu dropdown-menu-right">
                                <button class="dropdown-item" type="button">EUR</button>
                                <button class="dropdown-item" data-currency="USD" type="button">USD</button>
                                <button class="dropdown-item" data-currency="EUR" type="button">EUR</button>
                                <button class="dropdown-item" data-currency="NGN" type="button">NGN</button>
                                <button class="dropdown-item" data-currency="GHS" type="button">GHS</button>
                                <button class="dropdown-item" data-currency="GBP" type="button">GBP</button>
                                <button class="dropdown-item" data-currency="XAF" type="button">XAF</button> --}}
                            {{-- <button class="dropdown-item" type="button">GBP</button>
                                    <button class="dropdown-item" type="button">CAD</button> --}}
                            {{-- </div> --}}
                        </div>
                    </div>

                    <div class="navbar-nav py-0 d-flex sidebar-nav px-2">
                        @auth
                            <a href="{{ route('wishlist.index') }}" class="btn px-0 ml-3 position-relative">
                                <i class="fas fa-heart text-primary"></i>
                                <span class="cart-count text-primary border border-secondary rounded-circle"
                                    style="padding-bottom: 2px;">
                                    @php
                                        echo \App\MyHelpers::userLikesItemCount();
                                    @endphp
                                </span>
                            </a>
                        @endauth
                        <a href="{{ route('store.my-cart') }}" class="btn px-0 ml-3 position-relative">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span class="cart-count text-primary border border-secondary rounded-circle"
                                style="padding-bottom: 2px;">
                                @if (null != session('cart') && count(session('cart')) > 0)
                                    {{ count(session('cart')) }}
                                @else
                                    0
                                @endif
                            </span>
                        </a>
                    </div>
                </div>
                <div class="mx-2 mt-3">
                    <script type="text/javascript">
                        function googleTranslateElementInit2() {
                            new google.translate.TranslateElement({
                                pageLanguage: 'en',
                                includedLanguages: 'en,it,fr,nl,pt',
                                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                            }, 'google_translate_element_mobile');
                        }
                        googleTranslateElementInit2()
                    </script>
                    <div id="google_translate_element_mobile"></div>
                </div>
                <div class=" mx-2 mt-3">
                    <div class="input-group input-group-sm">
                        <input type="text" id="search_location_m" class="form-control location-picker"
                            placeholder="Ship To..." value="{{ session('ship_to_str') }}">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0"><i
                                    class="fas fa-map-marker-alt text-primary"></i></span>
                        </div>
                    </div>
                    <div id="location_suggestions_m"></div>
                </div>

                <ul>

                    <li>
                        <a href="{{ url('/') }}" class="hmenu-item">Home</a>
                    </li>
                    <li>
                        <a href="{{ url('/store/category') }}" class="hmenu-item">Categories</a>
                    </li>
                    <li>
                        <a href="{{ url('/store/brand') }}" class="hmenu-item">Brands</a>
                    </li>
                    <li>
                        <a href="{{ url('/store/vendor') }}" class="hmenu-item">Vendor</a>
                    </li>
                    <li>
                        <a href="{{ url('/about') }}" class="hmenu-item">About</a>
                    </li>
                    <li>
                        <a href="{{ url('/contact') }}" class="hmenu-item">Contact</a>
                    </li>
                    <li>
                        <a href="{{ url('/help') }}" class="hmenu-item">Help</a>
                    </li>
                    <li>
                        <a href="{{ url('/faq') }}" class="hmenu-item">FAQ</a>
                    </li>
                    <hr>

                </ul>
            </div>
        </div>
    </div>
</div>
<!-- offCanvas  -->


<script>
    const btn = document.querySelectorAll('.side-menu-content .btn-group');
    btn.forEach(element => {
        const dropdown = element.querySelector('.dropdown-menu');
        element.addEventListener('click', () => {
            dropdown.classList.toggle('show');
        })
    });
</script>


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
        color: #495057;
    }

    .category-select {
        font-size: 14px;
        /* Adjust font size as needed */
        padding: 0.375rem 0.75rem;
        /* Adjust padding as needed */
        height: auto;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        /* Allow height to adjust based on content */
    }

    .location-picker:focus {
        outline: none;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .search-btn {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }


    .search-inp {
        border: 1px solid gray;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        color: white !important;
    }

    .search-inp:focus {
        border: 2px solid #1575b8;
        color: black !important;
    }

    .search-btn {
        border: 1px solid gray;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
</style>
<script>
    document.getElementById('deliveryModalToggle').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('deliveryModal'), {
            keyboard: false
        });
        modal.show();
        document.getElementById('search_location').focus();
    });

    document.getElementById('modalCloseButton').addEventListener('click', function() {
        document.getElementById('deliveryModal').classList.remove('show');
        document.getElementById('deliveryModal').setAttribute('aria-hidden', 'true');
        document.getElementById('deliveryModal').style.display = 'none';
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
        var backdrop = document.querySelector('.modal-backdrop');
        backdrop.parentNode.removeChild(backdrop);
    });
</script>
