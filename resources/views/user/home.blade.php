@extends('user.layout.app')
@section('page_name', 'Home')
@section('content')

    <style>
        .services {
            border-radius: 6px;
            box-shadow: rgb(0 100 175 / 30%) 0px 1px 2px 0px, rgb(24 97 152 / 15%) 0px 1px 3px 1px;
        }

        .services:hover {
            background-color: #1575b8 !important;
        }

        .services:hover h1 {
            color: #FFF !important;
        }

        .services:hover h5 {
            color: #FFF !important;
        }

        .services h5 {
            color: #3d464d !important;
        }

        .services .phone {
            transform: rotate(-28deg);
        }

        .cat-item:hover h6 {
            color: #FFF;
        }

        .sliderwidth .slider {
            width: 100% !important;
        }


        .sliderwidth .slider .slick-prev.slick-arrow {
            display: none !important;
        }

        .sliderwidth .slider button.slick-next.slick-arrow {
            display: none !important;
        }

        button.slick-prev.slick-arrow {
            background-image: none !important;
        }

        button.slick-next.slick-arrow {
            background-image: none !important;
            right: -10px !important;
        }

        .slick-prev:before,
        .slick-next:before {
            font-size: 36px !important;
            left: -10px !important;
        }

        .cat-item {
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        }

        .brands .cat-item:hover {
            background-color: #000;
        }

        .brands h6 {
            font-size: 20px;
            margin: 0;
        }

        .brands .cat-item:hover h6 {
            color: #fff;
            transition: none;
        }

        .brands .cat-item:hover img {
            transform: none !important;
        }

        .common-sec {
            background-color: #fff;
            padding-inline: 12px;
        }

        .Categories-title {
            font-size: 16px;
            font-weight: 600 !important;
            color: #1476b8 !important;
        }

        .top-deal-content {
            font-size: 16px;
        }

        .cat-item p {

            display: none;
        }

        @media only screen and (max-width: 768px) {
            .mobile {
                height: 150px;
                display: block;
            }
        }
    </style>
    <div class="container-fluid px-0">
        <section class="home-banner">
            <div class="home-banner-slider owl-carousel owl-theme position-relative">
                @if ($announcements)
                    @foreach ($announcements as $announcement)
                        <div class="item">
                            <a href="#" class="banner-img mobile">
                                <img src="{{ asset('uploads/images/announcements/' . $announcement->image) }}" alt="">
                            </a>
                        </div>
                    @endforeach
                @endif



            </div>
        </section>
    </div>
    <section class="top-deals-container py-3 mb-4">
        <div class="container-fluid">
            <h2 class="top-deals-title mb-3">Recent Products<a href="{{ url('store/category') }}">See all Products</a>
            </h2>
            <div class="swiper recent-products position-relative">
                <div class="swiper swiper-wrapper">

                    @if ($recent)
                        @foreach ($recent as $r)
                            <div class="swiper-slide">
                                <a href="{{ route('store.showProduct', $r->product_slug) }}">
                                    <div class="top-deal-card">
                                        <div class="top-deal-img text-center py-2">
                                            @php
                                                $variation = $r?->variations->first();
                                                $variationImages = $variation
                                                    ? json_decode($variation->image_url)
                                                    : null;

                                            @endphp

                                            @if ($variation && $variationImages && count($variationImages) > 0)
                                                <img class="img-fluid w-100 h-100 img-thumbnail"
                                                    src="uploads/images/product/{{ $variationImages[0] }}" alt="">
                                            @else
                                                @if ($r->images && count($r->images) > 0)
                                                    <img class="img-fluid w-100 h-100 img-thumbnail"
                                                        src="uploads/images/product/{{ $r->images[0]->product_image }}"
                                                        alt="">
                                                @elseif($r->product_thumbnail)
                                                    <img class="img-fluid w-100 h-100 img-thumbnail"
                                                        src="uploads/images/product/{{ $r->product_thumbnail }}"
                                                        alt="">
                                                @else
                                                    <img class="img-fluid w-100 h-100 img-thumbnail"
                                                        src="uploads/images/product/6a51e6b5a1f5ef3ad2754973d6b5eede.png"
                                                        alt="">
                                                    {{-- <img src="{{ asset('user_assets/img/top-deal.jpg') }}" class="img-fluid w-100 img-thumbnail" alt="{{ $r->product_name }}"> --}}
                                                @endif
                                            @endif


                                            <!-- <img src="{{ asset('user_assets/img/top-deal.jpg') }}" alt="" class="h-100"> -->
                                        </div>
                                        <div class="top-deal-content mt-3">
                                            <div class="d-flex justify-content-between">
                                                <p class="badges px-1">Up to 67 % off</p>
                                                <span class="limited-offer">Limited time deal</span>
                                            </div>
                                            <span class="text-black">
                                                @if (strlen($r->product_name) > 25)
                                                    {{ substr($r->product_name, 0, 20) . '  ...  ' }}
                                                @else
                                                    {{ $r->product_name }}
                                                @endif
                                            </span>
                                            <!-- {{ session()->get('session_symbol') ?? '€' }} {{ App\MyHelpers::getPrice($r->product_price) }} -->
                                            <h5>
                                                {{-- {{ session()->get('session_symbol') ?? '€' }} --}}
                                                @php
                                                    $price = 0;
                                                    if ($r?->variations->first()) {
                                                        $price = $r?->variations->first()?->price;
                                                    } else {
                                                        $price = App\MyHelpers::getPrice($r->product_price);
                                                    }

                                                    $price = App\MyHelpers::fromEuroView(
                                                        session('currency_id', 0),
                                                        $price,
                                                    );
                                                @endphp
                                                {{ $price }}

                                            </h5>


                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>






    @php
        $categories = App\Models\CategoryModel::orderBy('category_id', 'desc')->get();
    @endphp

    <!--end-->
    <section class="product-section mt-5" style="margin-top: 0px !important;">
        <div class="container-fluid">
            <div class="category-container">
                <!--<h1>HArsht</h1>-->
                @if ($categories)
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <div class="product-cards pb-3">
                                <h2 class="px-3">{{ $category->category_name }}</h2>
                                <div class="product-grid px-3">
                                    <?php
                               $products = DB::table('product')->where('category_id', $category->category_id)->where('admin_approved', 1)->where('product_status', 1)->orderBy('product_id', 'DESC')->limit(4)->get();
                                if ($products->count() > 0) {
                                    foreach ($products as $product) {
                                        // dd($product);
                                ?>
                                    <a href="{{ route('store.showProduct', $product->product_slug) }}"
                                        class="product-card">
                                        <div class="category-img">
                                            @if ($product->product_thumbnail)
                                                <img src="{{ asset('uploads/images/product/' . $product->product_thumbnail) }}"
                                                    class="h-100 w-100" alt="{{ $category->category_name }}">
                                            @else
                                                <img class="h-100 w-100" alt="no-image"
                                                    src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}">
                                            @endif

                                        </div>
                                        <span><?= $product->product_name ?></span>
                                    </a>

                                    <?php }
                         } else {

                        ?>
                                    <a href="#" class="product-card">
                                        <div class="category-img">
                                            <img class="h-100 w-100" alt="no-image"
                                                src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}">

                                        </div>
                                        <span>no product</span>
                                    </a>
                                    <a href="#" class="product-card">
                                        <div class="category-img">
                                            <img class="h-100 w-100" alt="no-image"
                                                src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}">

                                        </div>
                                        <span>no product</span>
                                    </a>
                                    <a href="#" class="product-card">
                                        <div class="category-img">
                                            <img class="h-100 w-100" alt="no-image"
                                                src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}">

                                        </div>
                                        <span>no product</span>
                                    </a>
                                    <a href="#" class="product-card">
                                        <div class="category-img">
                                            <img class="h-100 w-100" alt="no-image"
                                                src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}">

                                        </div>
                                        <span>no product</span>
                                    </a>
                                    <?php

                         }
                       ?>

                                    <a href="{{ route('store.showCategory', $category->category_slug) }}""
                                        class="more-btn d-block px-3 mt-2">Show more</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- <div class="col-md-3 d-none">
                    <div class="product-cards pb-3">
                        <h2 class="px-3">Appliances for your home | Up to 55% off</h2>
                        <div class="product-grid px-3">
                            <a href="#" class="product-card">
                                <div class="product-img">
                                    <img src="{{ asset('user_assets/img/product1.jpg') }}" alt="" class="h-100">
        </div>
        <span>Air conditioners</span>
        </a>
        <a href="#" class="product-card">
            <div class="product-img">
                <img src="{{ asset('user_assets/img/product2.jpg') }}" alt="" class="h-100">
            </div>
            <span>Air conditioners</span>
        </a>
        <a href="#" class="product-card">
            <div class="product-img">
                <img src="{{ asset('user_assets/img/product3.jpg') }}" alt="" class="h-100">
            </div>
            <span>Air conditioners</span>
        </a>
        <a href="#" class="product-card">
            <div class="product-img">
                <img src="{{ asset('user_assets/img/Appliances-QC-PC-186x116--B08RDL6H79._SY116_CB667322346_.jpg') }}" alt="" class="h-100">
            </div>
            <span>Air conditioners</span>
        </a>
    </div>
    <a href="#" class="more-btn d-block px-3 mt-3">Show more</a>
    </div>
    </div>
    <div class="col-md-3 d-none">
        <div class="product-cards pb-3">
            <h2 class="px-3">Appliances for your home | Up to 55% off</h2>
            <div class="product-grid px-3">
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product1.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product2.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product3.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/Appliances-QC-PC-186x116--B08RDL6H79._SY116_CB667322346_.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
            </div>
            <a href="#" class="more-btn d-block px-3 mt-3">Show more</a>
        </div>
    </div>
    <div class="col-md-3 d-none">
        <div class="product-cards pb-3">
            <h2 class="px-3">Appliances for your home | Up to 55% off</h2>
            <div class="product-grid px-3">
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product1.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product2.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/product3.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
                <a href="#" class="product-card">
                    <div class="product-img">
                        <img src="{{ asset('user_assets/img/Appliances-QC-PC-186x116--B08RDL6H79._SY116_CB667322346_.jpg') }}" alt="" class="h-100">
                    </div>
                    <span>Air conditioners</span>
                </a>
            </div>
            <a href="#" class="more-btn d-block px-3 mt-3">Show more</a>
        </div>
    </div> --}}
            </div>
        </div>
    </section>
    <section class="top-deals-container py-3 mb-4">
        <div class="container-fluid">
            <h2 class="top-deals-title mb-3">Today’s Deals <a href="{{ url('store/category') }}">See all deals</a></h2>
            <div class="swiper top-deals-slider position-relative">
                <div class="swiper swiper-wrapper">
                    @if ($today_deals)
                        @foreach ($today_deals as $today_deal)
                            <div class="swiper-slide">
                                <a href="{{ route('store.showProduct', $today_deal->product_slug) }}">
                                    <div class="top-deal-card">
                                        <div class="top-deal-img text-center py-2">
                                            @if ($today_deal != null)
                                                <img src="uploads/images/product/{{ $today_deal->images[0]->product_image }}"
                                                    alt="" class="h-100">
                                            @else
                                                <img src="{{ asset('user_assets/img/top-deal.jpg') }}" alt="images"
                                                    class="h-100">
                                            @endif

                                        </div>
                                        <div class="top-deal-content mt-3">
                                            <div class="d-flex justify-content-between">
                                                <p class="badges px-1">Up to 67 % off</p>
                                                <span class="limited-offer">Limited time deal</span>
                                            </div>
                                            <span class="text-black">{{ $today_deal->product_name ?? '' }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>
    </div>
    <!-- <div class="container-fluid mb-3">
                                                <div class="row px-xl-5">
                                                    <div class="col-lg-8">
                                                        <div id="header-carousel" class="carousel slide carousel-fade mb-30 mb-lg-0" data-ride="carousel">
                                                            <ol class="carousel-indicators">
                                                                @if ($categories)
                                                                @php
                                                                    $t = 0;
                                                                @endphp
                                                                @foreach ($categories as $category)
    <li data-target="#header-carousel" data-slide-to="{{ $t }}" class="{{ $t == 0 ? ' active' : '' }}"></li>
                                                                @php
                                                                    $t++;
                                                                @endphp
    @endforeach

                                                                @endif
                                                            </ol>
                                                            <div class="carousel-inner">
                                                                @if ($categories)
                                                                @php
                                                                    $t = 0;
                                                                @endphp
                                                                @foreach ($categories as $category)
    <div class="carousel-item position-relative{{ $t == 0 ? ' active' : '' }}" style="height: 430px;">
                                                                    <img class="position-absolute w-100 h-100" src="/uploads/images/category/{{ $category->category_image }}" style="object-fit: cover;">
                                                                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                                                        <div class="p-3" style="max-width: 700px;">
                                                                            <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">
                                                                                {{ $category->category_name }}
                                                                            </h1>
                                                                            {{-- <p class="mx-md-5 px-5 animate__animated animate__bounceIn">Lorem rebum magna amet lorem
                                        magna erat diam stet. Sadips duo stet amet amet ndiam elitr ipsum diam</p> --}}
                                                                            <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp" href="{{ route('store.showCategory', $category->category_slug) }}">Shop
                                                                                Now</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    $t++;
                                                                @endphp
    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="product-offer mb-30" style="height: 200px;">
                                                            <img class="img-fluid" src="user_assets/img/offer-1.jpg" alt="">
                                                            <div class="offer-text">
                                                                <h6 class="text-white text-uppercase">Save 20%</h6>
                                                                <h3 class="text-white mb-3">Special Offer</h3>
                                                                <a href="" class="btn bg-blue">Shop Now</a>
                                                            </div>
                                                        </div>
                                                        <div class="product-offer mb-30" style="height: 200px;">
                                                            <img class="img-fluid" src="user_assets/img/offer-2.jpg" alt="">
                                                            <div class="offer-text">
                                                                <h6 class="text-white text-uppercase">Save 20%</h6>
                                                                <h3 class="text-white mb-3">Special Offer</h3>
                                                                <a href="" class="btn bg-blue">Shop Now</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
    <!-- Carousel End -->

    <section class="top-deals-container py-3 mb-4">
        <div class="container-fluid">
            <h2 class="top-deals-title mb-3">Categories <a href="{{ url('store/category') }}">See all Categories</a></h2>
            <div class="swiper categories-slider position-relative">
                <div class="swiper swiper-wrapper">
                    @if ($categories)
                        @foreach ($categories as $category)
                            <div class="swiper-slide">
                                <div class="top-deal-card">
                                    <a href="{{ route('store.showCategory', $category->category_slug) }}"
                                        class="top-deal-img text-center py-2">
                                        <img src="uploads/images/category/{{ $category->category_image }}" alt=""
                                            class="h-100 w-100">
                                    </a>
                                    <div class="top-deal-content mt-1">
                                        <h5 class="Categories-title">{{ $category->category_name }}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>




    <!-- Categories Start -->
    <!-- <div class="container-fluid pt-5" style=" background: #4cc2fb;">
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-2 col-12">
                                                        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                                                            <span class="text-white pr-3">Categories</span>
                                                        </h2>
                                                    </div>
                                                    <div class="col-lg-10 col-md-10 col-12">
                                                        <div class="row px-xl-5 pb-3 sliderwidth">
                                                            @if ($categories)
                                                            <div class="slider">
                                                                @foreach ($categories as $category)
    <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                                                                    <a class="text-decoration-none" href="{{ route('store.showCategory', $category->category_slug) }}">
                                                                        <div class="cat-item mb-4">
                                                                            <div class="overflow-hidden" style=" height: 150px;">
                                                                                <img class="img-fluid h-100 w-100" src="uploads/images/category/{{ $category->category_image }}" alt="">
                                                                            </div>
                                                                            <div class="flex-fill d-flex justify-content-center py-3">
                                                                                <h6>{{ $category->category_name }}</h6>
                                                                                <small class="text-body"></small>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
    @endforeach
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div> -->

    <!-- <div class="row px-xl-5 pb-3">
                                                        @if ($categories)
                                                            @foreach ($categories as $category)
    <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                                                                    <a class="text-decoration-none" href="{{ route('store.showCategory', $category->category_slug) }}">
                                                                        <div class="cat-item d-flex align-items-center mb-4">
                                                                            <div class="overflow-hidden" style="width: 100px; height: 100px;">
                                                                                <img class="img-fluid" src="uploads/images/category/{{ $category->category_image }}"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="flex-fill pl-3">
                                                                                <h6>{{ $category->category_name }}</h6>
                                                                                <small class="text-body"></small>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
    @endforeach
                                                        @endif
                                                    </div> -->



    <!-- </div> -->
    <!-- Categories End -->

    <!-- Featured Start -->
    <div class="container-fluid pt-2">
        <div class="row px-xl-5 pb-3">
            <div class="col-lg-3 col-sm-6 pb-1 ">
                <div class="d-flex align-items-center flex-column gap-4 bg-light mb-4 services"
                    style="padding: 50px 40px; gap:10px;">
                    <h1 class="fa fa-check text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0">Quality Product</h5>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 pb-1">
                <div class="d-flex align-items-center flex-column gap-4 bg-light mb-4 services"
                    style="padding: 50px  40px; gap:10px;">
                    <h1 class="fa fa-shipping-fast text-primary m-0 mr-2"></h1>
                    <h5 class="font-weight-semi-bold m-0">Free Shipping</h5>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 pb-1 ">
                <div class="d-flex align-items-center flex-column gap-4 bg-light mb-4 services"
                    style="padding: 50px 40px; gap:10px;">
                    <h1 class="fas fa-exchange-alt text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0">14-Day Return</h5>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 pb-1">
                <div class="d-flex align-items-center flex-column gap-4 bg-light mb-4 services"
                    style="padding: 50px 40px; gap:10px;">
                    <h1 class="fa fa-phone-volume text-primary m-0 mr-3 phone"></h1>
                    <h5 class="font-weight-semi-bold m-0">24/7 Support</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured End -->



    <!-- <section class="product-section mt-1">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="product-cards pb-3">
                                                                <h2 class="px-3">Featured Products test</h2>
                                                                <div class="product-grid px-3">
                                                                    @foreach ($featured as $item)
    @foreach ($item->products as $i)
    <a href="#" class="product-card">
                                                                        <div class="product-img">
                                                                            @if ($i->images && count($i->images) > 0)
    <img class="img-fluid w-100 img-thumbnail" src="uploads/images/product/{{ $i->images[0]->product_image }}" alt="">
@else
    <img class="img-fluid w-100 img-thumbnail" style="height: 250px" alt="{{ $i->product_name }}">
    @endif

                                                                        </div>
                                                                        <span>
                                            @if (strlen($i->product_name) > 25)
    {{ substr($i->product_name, 0, 20) . '  ...  ' }}
@else
    {{ $i->product_name }}
    @endif
                                            </span>
                                                                        <h2>{{ session()->get('session_symbol') ?? '€' }} {{ App\MyHelpers::getPrice($i->product_price) }}</h2>
                                                                    </a>
    @endforeach
    @endforeach
                                                                </div>
                                                                <a href="#" class="more-btn d-block px-3 mt-3">Show more</a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </section> -->


    <!-- Products Start -->


    <div class="container-fluid pt-5 pb-3" style=" background: #4cc2fb;">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-12">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span
                        class="text-white pr-3">Featured Products</span></h2>
            </div>
            <div class="col-lg-10 col-md-12 col-12">
                @if ($featured)

                    <div class="owl-carousel owl-theme position-relative product-slider">
                        @foreach ($featured as $item)
                            @foreach ($item->products as $i)
                                <div class="pb-4 items">
                                    <a class="" href="{{ route('store.showProduct', $i->product_slug) }}">
                                        <div class="card rounded px-2 py-2 h-100 d-flex flex-column">
                                            <div class="product-img position-relative overflow-hidden">
                                                @if ($i->images && count($i->images) > 0)
                                                    <img class="img-fluid w-100 h-100"
                                                        src="uploads/images/product/{{ $i->images[0]->product_image }}"
                                                        alt="">
                                                @elseif($i->product_thumbnail)
                                                    <img class="img-fluid w-100 h-100"
                                                        src="uploads/images/product/{{ $i->product_thumbnail }}"
                                                        alt="">
                                                @else
                                                    <img class="img-fluid w-100 h-100" style="height: 250px"
                                                        alt="{{ $i->product_name }}">
                                                @endif

                                                <div class="product-action">

                                                </div>
                                            </div>
                                            <div class="py-2 px-3">
                                                <a class="text-decoration-none text-blue-400"
                                                    href="{{ route('store.showProduct', $i->product_slug) }}"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ $i->product_name }}">
                                                    @if (strlen($i->product_name) > 25)
                                                        {{ substr($i->product_name, 0, 20) . '  ...  ' }}
                                                    @else
                                                        {{ $i->product_name }}
                                                    @endif
                                                </a>
                                                <div class="d-flex align-items-center mt-2">
                                                    <h5 class="mb-0">
                                                        @php
                                                            $price = 0;
                                                            if ($r?->variations->first()) {
                                                                $price = $r?->variations->first()?->price;
                                                            } else {
                                                                $price = App\MyHelpers::getPrice($r->product_price);
                                                            }

                                                            $price = App\MyHelpers::fromEuroView(
                                                                session('currency_id', 0),
                                                                $price,
                                                            );
                                                        @endphp
                                                        {{ $price }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                @endif

            </div>
        </div>


        <!-- @if ($featured)
                                                <div class="container">
                                                    <div class="row">
                                                        @foreach ($featured as $item)
    @foreach ($item->products as $i)
    <div class="col-lg-3 w-full col-md-4 col-6 col-sm-4 pb-4">
                                                            <div class="card rounded px-2 py-2 h-100 d-flex flex-column">
                                                                <div class="product-img position-relative overflow-hidden">
                                                                    @if ($i->images && count($i->images) > 0)
    <img class="img-fluid w-100 img-thumbnail" src="uploads/images/product/{{ $i->images[0]->product_image }}" alt="">
@else
    <img class="img-fluid w-100 img-thumbnail" style="height: 250px" alt="{{ $i->product_name }}">
    @endif

                                                                    <div class="product-action">
                                                                        <a class="btn btn-outline-dark btn-square" href="{{ route('store.showProduct', $i->product_slug) }}"><i class="fa fa-eye"></i></a>
                                                                    </div>
                                                                </div>
                                                                <div class="py-4 px-3">
                                                                    <a class="text-decoration-none text-blue-400" href="{{ route('store.showProduct', $i->product_slug) }}" data-toggle="tooltip" data-placement="top" title="{{ $i->product_name }}">
                                                                        @if (strlen($i->product_name) > 25)
    {{ substr($i->product_name, 0, 20) . '  ...  ' }}
@else
    {{ $i->product_name }}
    @endif
                                                                    </a>
                                                                    <div class="d-flex align-items-center mt-2">
                                                                        <h5>{{ session()->get('session_symbol') ?? '€' }} {{ App\MyHelpers::getPrice($i->product_price) }}</h5>
                                                                        {{-- <h6 class="text-muted ml-2"><del>$123.00</del></h6> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
    @endforeach
    @endforeach
                                                    </div>
                                                </div>
                                                @endif -->

    </div>
    <!-- Products End -->

    <!-- brands Start -->
    <section class="container-fluid pt-5">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">Brands</span>
        </h2>
        <div class="brands-sliders owl-carousel owl-theme position-relative">
            @if ($brands)
                @foreach ($brands as $brand)
                    <div class="items pb-1 brands">
                        <a class="text-decoration-none" href="{{ route('store.showBrand', $brand->brand_slug) }}">
                            <div class="cat-item d-flex align-items-center mb-4">
                                <div class="overflow-hidden" style="width: 100px; height: 100px; padding:10px">
                                    @if (isset($brand->brand_image))
                                        <img class="img-fluid h-100"
                                            src="{{ asset('uploads/images/brand/' . $brand->brand_image) }}"
                                            alt="">
                                    @else
                                        <img class="img-fluid h-100"
                                            src="{{ asset('uploads/images/brand/c3aa514869752e1219a6eefa728e7edb.png') }}"
                                            alt="">
                                    @endif
                                </div>
                                <div class="flex-fill pl-sm-3">
                                    <h6>{{ $brand->brand_name }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif

        </div>
    </section>




    <!--<div class="container-fluid pt-5">-->
    <!--    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span-->
    <!--            class="bg-secondary pr-3">Brands</span></h2>-->
    <!--    <div class="row px-xl-5 pb-3">-->
    <!--        @if ($brands)-->
    <!--            @foreach ($brands as $brand)
    -->
    <!--                <div class="col-lg-3 col-md-4 col-sm-6 pb-1 brands">-->
    <!--                    <a class="text-decoration-none" href="{{ route('store.showBrand', $brand->brand_slug) }}">-->
    <!--                        <div class="cat-item d-flex align-items-center mb-4">-->
    <!--                            <div class="overflow-hidden" style="width: 100px; height: 100px; padding:10px">-->
    <!--                                <img class="img-fluid h-100"-->
    <!--                                    src="{{ asset('uploads/images/brand/' . $brand->brand_image) }}" alt="">-->
    <!--                                <p>{{ asset('uploads/images/brand/' . $brand->brand_image) }}</p>-->
    <!--                            </div>-->
    <!--                            <div class="flex-fill pl-3">-->
    <!--                                <h6>{{ $brand->brand_name }}</h6>-->
    <!--                                <small class="text-body"></small>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </a>-->
    <!--                </div>-->
    <!--
    @endforeach-->
    <!--        @endif-->
    <!--    </div>-->
    <!--</div>-->
    <!-- brands End -->


    {{-- <!-- Offer Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="row px-xl-5">
            <div class="col-md-6">
                <div class="product-offer mb-30" style="height: 300px;">
                    <img class="img-fluid" src="img/offer-1.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase">Save 20%</h6>
                        <h3 class="text-white mb-3">Special Offer</h3>
                        <a href="" class="btn bg-blue">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-offer mb-30" style="height: 300px;">
                    <img class="img-fluid" src="img/offer-2.jpg" alt="">
                    <div class="offer-text">
                        <h6 class="text-white text-uppercase">Save 20%</h6>
                        <h3 class="text-white mb-3">Special Offer</h3>
                        <a href="" class="btn bg-blue">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Offer End -->






    <!-- Products Start -->
    <!-- <div class="container-fluid vender-container common-sec py-3 mb-4">
                                                <h2 class="top-deals-title mb-3">Recent Products<a href="#">See all Products</a></h2>
                                                <div class="row pb-3">
                                                    @if ($recent)
                                                    @foreach ($recent as $r)
    <div class="col-lg-2 w-full col-md-2 col-6 col-sm-2 pb-4 px-3">
                                                        <div class="card rounded px-2 h-100 d-flex flex-column">
                                                            <div class="product-img position-relative overflow-hidden">
                                                                @if ($r->images && count($r->images) > 0)
    <img class="img-fluid w-100 img-thumbnail" src="uploads/images/product/{{ $r->images[0]->product_image }}" alt="">
@else
    <img class="img-fluid w-100 img-thumbnail" alt="{{ $r->product_name }}">
    @endif

                                                                <div class="product-action">
                                                                    <a class="btn btn-outline-dark btn-square" href="{{ route('store.showProduct', $r->product_slug) }}"><i class="fa fa-eye"></i></a>
                                                                </div>
                                                            </div>
                                                            <div class="py-4">
                                                                <a class="h6 text-decoration-none text-blue-400" href="{{ route('store.showProduct', $r->product_slug) }}" data-toggle="tooltip" data-placement="top" title="{{ $r->product_name }}">
                                                                    @if (strlen($r->product_name) > 25)
    {{ substr($r->product_name, 0, 20) . '  ...  ' }}
@else
    {{ $r->product_name }}
    @endif
                                                                </a>
                                                                <div class="d-flex align-items-center mt-2">
                                                                    <h5>{{ session()->get('session_symbol') ?? '€' }} {{ App\MyHelpers::getPrice($r->product_price) }}</h5>
                                                                    {{-- <h6 class="text-muted ml-2"><del>$123.00</del></h6> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
    @endforeach
                                                    @endif
                                                </div>
                                            </div> -->
    <!-- Products End -->


    <!-- Vendor Start -->
    <div class="container-fluid vender-container common-sec py-3 mb-4">
        <h2 class="top-deals-title mb-3">Our Vendors<a href="{{ url('store/vendor') }}">See all venders</a></h2>
        <div class="row">
            <div class="col">
                <div class="owl-carousel vendor-carousel">
                    @if (isset($vendors))
                        @foreach ($vendors as $vendor)
                            <div>
                                <div class="bg-light p-2">
                                    <div style="height: 105px;">
                                        <img src="{{ !empty($vendor->user->photo)
                                            ? url('uploads/images/profile/' . $vendor->user->photo)
                                            : url('uploads/images/user_default_image.png') }}"
                                            alt="{{ $vendor->shop_name }} - Image" style="width: 100%;  height:100%">
                                    </div>

                                    <h6 style="font-size: 14px;" class="mt-2">
                                        <a
                                            href="{{ route('store.showVendor', $vendor->vendor_id) }}">{{ $vendor->shop_name }}</a>
                                    </h6>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>
        .product-img-wrapper {
            height: 250px;
            overflow: hidden;
        }

        .product-img-wrapper img {
            width: 100%;
            height: auto;
        }

        .product-image {
            height: 250px;
        }

        .text-blue-400 {
            color: #1575b8;
        }

        .text-blue-400:hover {
            color: lightblue;
        }

        .bg-blue {
            color: white;
            border-radius: 5px;
            background-color: #1575b8;
        }

        .bg-blue:hover {
            color: white;
            border-radius: 5px;
            background-color: lightblue;
        }

        .vendor-carousel {
            display: flex;
            flex-wrap: wrap;
        }



        .vendor-carousel .owl-nav.disabled {
            display: flex !important;
        }
    </style>
@endsection
@section('scripts')

@endsection
