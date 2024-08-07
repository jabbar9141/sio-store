@php
    $firstVariation = $product->variations->first();
    $variationImages = $firstVariation ? json_decode($firstVariation->image_url) : null;
    if ($firstVariation) {
        $variationVideo = json_decode($firstVariation->video_url);
    }
@endphp
@extends('user.layout.app')

@section('page_name', $product->product_name)

@section('content')

    <style>
        .cart-submit {

            max-width: 200px;
            padding: 10px;

            width: 100%;

        }

        .cart-submit:hover,
        .cart-submit:not(:disabled):not(.disabled):active,
        .cart-submit:focus {
            background-color: #232f3e;
            border-color: #232f3e;
            box-shadow: none !important;
        }

        .product-nav-tabs {

            justify-content: center;

            gap: 20px;

            border-bottom: none;

        }



        .product-nav-tabs a.nav-item.nav-link.text-dark {

            background-color: #dee2e6;

        }





        .product-nav-tabs .nav-link.active {

            background-color: #1575b8 !important;

            color: #ffffff !important;

        }





        .quantity-btn {

            border: 1px solid #1476b8 !important;

        }



        .quantity-btn button {

            padding: 10px 15px;

        }



        .quantity-btn .form-control {

            padding-block: 11px;

            height: 100%;

        }



        .quantity-btn button:hover,

        .quantity-btn button:focus {

            background-color: #232f3e;

            border-color: #232f3e;

            box-shadow: none;

        }






        .videoModel {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }



        .quantity-btn .btn-primary:not(:disabled):not(.disabled):active {

            background-color: #232f3e;

            border-color: #232f3e;

            box-shadow: none;

        }



        .social-icon-grp i {

            background-color: #232f3e;

            color: #ffffff;

            height: 30px;

            width: 30px;

            display: flex;

            justify-content: center;

            align-items: center;



        }

        .related-img {
            height: 220px !important;
            object-fit: cover;
        }

        /*.productimg{*/

        /*    max-height: 300px !important;*/

        /*}*/



        @media screen and (max-width: 700px) {

            .disflexx {

                display: flex !important;

                flex-direction: column;

                gap: 10px;

            }

        }





            {}
    </style>

    <style>
        .fa-star {
            font-size: 1.5em;
            cursor: pointer;
            color: gray;
        }

        .fa-star.checked {
            color: gold;
        }
    </style>



    <!-- Breadcrumb Start -->

    <div class="container-fluid px-0">

        <div class="row">

            <div class="col-12">

                <nav class="breadcrumb bg-light mb-30">

                    <a class="breadcrumb-item text-dark" href="{{ url('/') }}">Home</a>

                    <a class="breadcrumb-item text-dark" href="#">Shop</a>

                    <span class="breadcrumb-item active">{{ $product->product_name }}</span>

                </nav>

            </div>

        </div>

    </div>

    <!-- Breadcrumb End -->





    <!-- Shop Detail Start -->

    <div class="container-fluid pb-5">

        <div class="row px-xl-5">

            <div class="col-lg-5 mb-30">

                {{-- <div id="product-carousel" class="carousel slide h-100" data-ride="carousel"> --}}

                <!-- Carousel Inner -->

                {{-- <div class="carousel-inner bg-light h-100"> --}}

                <!-- Carousel Items -->

                {{-- @if ($product->images) --}}
                {{-- <div class="productimg h-100">
                        @foreach ($product->images as $imagees)
                            <img class="w-25 h-25" id="ProductImage"
                                src="/uploads/images/product/{{ $imagees->product_image }}"
                                alt="{{ $product->product_name }}">

                        @endforeach
                        <video class="class_map_parking_lot_page w-25 h-25"
                                src="{{ url('uploads/images/product/' . $product->video_link) }}" id="video"
                                width="100%" controls></video>
                    </div> --}}

                <div class="container-fluid">
                    <div class="row">
                        <!-- Left column for thumbnails -->
                        <div class="col-md-2"
                            style=" overflow-y: auto; padding: 10px; box-shadow: 2px 0 5px rgba(0,0,0,0.1);">
                            <div id="productImagesDiv">

                                @if ($firstVariation && $variationImages && count($variationImages) > 0)
                                    @foreach ($variationImages as $image)
                                        <img class="hover-effect img-thumbnail hover-effect mb-2" id=" hover-effect"
                                            src="/uploads/images/product/{{ $image }}"
                                            alt="{{ $product->product_name }}"
                                            style="cursor: pointer; width: 100px; height: 60px;"
                                            data-full="/uploads/images/product/{{ $image }}">
                                    @endforeach
                                @else
                                    @foreach ($product->images as $imagees)
                                        <img class="hover-effect img-thumbnail hover-effect mb-2" id=" hover-effect"
                                            src="/uploads/images/product/{{ $imagees->product_image }}"
                                            alt="{{ $product->product_name }}"
                                            style="cursor: pointer; width: 100px; height: 60px;"
                                            data-full="/uploads/images/product/{{ $imagees->product_image }}">
                                    @endforeach
                                @endif

                            </div>
                            <div id="productVideoDiv">
                                @if (isset($variationVideo))
                                    @if (count($variationVideo) > 0)
                                        <video class="hover-effect img-thumbnail hover-effect mb-2" id="hover-effect"
                                            src="{{ url('uploads/images/product/' . $variationVideo[0]) }}"
                                            style="cursor: pointer; width: 100px; height: 100px;"
                                            data-full="{{ url('uploads/images/product/' . $variationVideo[0]) }}"
                                            width="100%" controls></video>
                                    @endif

                                @endif

                            </div>

                        </div>
                        <!-- Right column for preview -->
                        <div class="col-md-10" style="padding: 10px;">
                            @if ($firstVariation && $variationImages && count($variationImages) > 0)
                                <img style="width: auto; height:500px" id="ProductImage" class="img-fluid"
                                    src="/uploads/images/product/{{ $variationImages[0] }}"
                                    alt="{{ $product->product_name }}">
                                <video style="display:none;width: auto; height:500px" id="ProductVideo" class="img-fluid"
                                    width="100%" controls></video>
                            @elseif($product->images->first())
                                <img style="width: auto; height:500px" id="ProductImage" class="img-fluid"
                                    src="/uploads/images/product/{{ $product->images->first()->product_image }}"
                                    alt="{{ $product->product_name }}">
                                <video style="display:none;width: auto; height:500px" id="ProductVideo" class="img-fluid"
                                    width="100%" controls></video>
                            @else
                                <img style="width: auto; height:500px" id="ProductImage" class="img-fluid"
                                    src="/uploads/images/product/{{ $product->product_thumbnail }}"
                                    alt="{{ $product->product_name }}">
                                <video style="display:none;width: auto; height:500px" id="ProductVideo" class="img-fluid"
                                    width="100%" controls></video>
                            @endif

                        </div>
                    </div>
                </div>
                {{-- <div class="carousel-item h-100 {{ $r == 0 ? 'active' : '' }}"> --}}



                <!--<img src="https://images.pexels.com/photos/90946/pexels-photo-90946.jpeg?cs=srgb&dl=pexels-madebymath-90946.jpg&fm=jpg" alt="" class="w-100 h-100">-->

                {{-- </div> --}}



                {{-- @endif --}}

                {{-- </div> --}}



                <!-- Carousel Controls -->

                {{-- <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">

                        <i class="fa fa-2x fa-angle-left text-dark"></i>

                    </a>

                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">

                        <i class="fa fa-2x fa-angle-right text-dark"></i>

                    </a> --}}

                {{-- </div> --}}



                <!-- Fullscreen Modal -->

                <div class="modal fade" id="fullscreenModal" tabindex="-1" role="dialog"
                    aria-labelledby="fullscreenModalLabel" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-lg">

                        <div class="modal-content">

                            <div class="modal-body">

                                <video src=""></video>

                            </div>

                        </div>

                    </div>

                </div>



            </div>



            <div class="col-lg-7 h-auto mb-30">

                <div class=" bg-light p-30">

                    <form method="POST" action="{{ route('store.addItem') }}" id="addToCartForm">

                        @csrf
                        <div class="d-flex justify-content-between">
                            <h3>{{ $product->product_name }}</h3>
                            <a href="#" class="btn btn-primary rounded-pill" data-toggle="modal"
                                data-target="#reviewModal">Review</a>
                        </div>

                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <input type="hidden" name="weight" value="{{ $product?->variations[0]?->weight ?? 1 }}">
                        @if ($product->variations->count() > 0)
                            <input type="hidden" name="variation_id" value="{{ $product?->variations[0]?->id }}"
                                id="productVariationId">
                        @else
                            <input type="hidden" name="variation_id" value="0" id="productVariationId">
                        @endif


                        <div class="d-flex mb-3">

                            @php

                                $max_stars = 5;

                                $rating_avg = $product->getProductReviewsAvg() ?? 0;

                                $rating_avg_rounded = round($rating_avg * 2) / 2; // Round the rating average to the nearest half

                            @endphp



                            <div class="text-primary mr-2">

                                @for ($i = 1; $i <= $max_stars; $i++)
                                    @if ($i <= $rating_avg_rounded)
                                        <i class="fas fa-star text-success"></i>
                                        <!-- Full star for whole numbers and half stars -->
                                    @elseif ($i - 0.5 == $rating_avg_rounded)
                                        <i class="fas fa-star-half-alt"></i> <!-- Half star -->
                                    @else
                                        <i class="fas fa-star text-secondary"></i> <!-- Gray star for unrated stars -->
                                    @endif
                                @endfor

                            </div>

                            <small class="pt-1">({{ round($rating_avg, 1) }} Stars from

                                {{ $product->getProductReviewsCount() }}

                                Reviews)</small>

                        </div>


                        <h3 class="font-weight-semi-bold mb-4">
                            @php
                                $price = 0;
                                if ($firstVariation) {
                                    $price = $firstVariation->price;
                                } else {
                                    $price = $product->product_price;
                                }
                                $price = App\MyHelpers::fromEuroView(session('currency_id', 0), $price);
                            @endphp
                            <span id="productPrice">{{ $price ?? 0 }}</span>
                        </h3>
                        <div>



                            <h6 id="shipping_cost_view" data-euro-shipping="{{ $shipping_cost }}">Shipping Fees:
                                <b>{{ $shipping_cost > 0 ? App\MyHelpers::fromEuroView(session('currency_id', 0), $shipping_cost) : 'Shipping Cost not avilable for your sellected location' }}</b>
                            </h6>

                            {{-- @php

                                $shipping_cost = ((array) json_decode($shipping_cost));

                                $all_variations = $product?->variations;

                            @endphp

                            @if ($shipping_cost)

                                @foreach ($shipping_cost as $company => $cost)
                                    @if (!empty($cost))
                                        <p>

                                            <small>

                                                &euro; <b>{{ $cost }}</b> To ship to

                                                <b>{{ session('ship_to_str') }}</b>

                                                Via

                                                <b>{{ strtoupper($company) }}</b>

                                            </small>

                                        </p>
                                    @endif
                                @endforeach
                            @else
                                Shipping Cost not avilable for your sellected location

                            @endif --}}



                        </div>

                        <p class="mb-4">{{ $product->product_short_description }}</p>

                        <div class="mb-3 row">

                            <div class="col-md-2 mt-2">
                                <label for="color">Color<span class="text-danger">*</span></label>
                                <select class="form-control" id="color">
                                    <option value="">Select Color</option>
                                    @foreach ($product?->variations as $color)
                                        @empty(!$color->color_name)
                                            <option value="{{ $color->color_name }}">
                                                {{ $color->color_name }}</option>
                                        @endempty
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" value="{{ $product->product_id }}" class="variation" id="ProductID">
                            <div class="col-md-2 mt-2">
                                <label for="sizes">Sizes<span class="text-danger">*</span></label>
                                <select class="form-control" id="SizeID">
                                    <option value="">Select size</option>
                                    <!--@if (count($sizes))-->
                                    @foreach ($product?->variations as $size)
                                        @empty(!$size->size_name)
                                            <option value="{{ $size->size_name }}">
                                                {{ $size->size_name }}</option>
                                        @endempty
                                    @endforeach
                                    <!--@endif-->

                                </select>
                            </div>
                            <div class="col-md-2 mt-2">
                                <label for="dimentionId">Dimensions<span class="text-danger">*</span></label>
                                <select class="form-control" id="dimentionId">
                                    <option value="">Select Dimension</option>
                                    @foreach ($product?->variations as $dimension)
                                        @empty(!$dimension->width || !$dimension->height || !$dimension->length || !$dimension->weight)
                                            <option
                                                value="{{ json_encode(['width' => $dimension->width, 'height' => $dimension->height, 'length' => $dimension->length, 'weight' => $dimension->weight]) }}">
                                                W: {{ $dimension->width }}; H: {{ $dimension->height }}; L:
                                                {{ $dimension->length }}
                                                ; W: {{ $dimension->weight }} Kg
                                            </option>
                                        @endempty
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="d-flex align-items-center disflexx mb-4 pt-2">

                            <div class="input-group quantity mr-3 quantity-btn" style="width: 130px;">

                                <div class="input-group-btn">

                                    <button class="btn btn-primary btn-minus" type="button">

                                        <i class="fa fa-minus"></i>

                                    </button>

                                </div>

                                <input type="text" class="form-control bg-secondary border-0 text-center"
                                    value="1" min="10" max="20" name="qty" readonly>

                                <div class="input-group-btn">
                                    <button class="btn btn-primary btn-plus" type="button">

                                        <i class="fa fa-plus"></i>

                                    </button>

                                </div>

                            </div>

                            <button class="btn btn-primary px-3 cart-submit" type="submit">
                                <i class="fa fa-shopping-cart mr-1"></i> Add To Cart</button>

                            @auth

                                @php

                                    $t = \App\MyHelpers::userLikesItem($product->product_id);

                                @endphp

                                @if (!$t)
                                    <button class="btn btn-primary mx-3" type="button"
                                        onclick="likeItem('{{ $product->product_id }}')"><i class="fa fa-heart mr-1"></i> Add
                                        To Wishlist</button>
                                @else
                                    <button class="btn btn-primary mx-3" type="button"
                                        onclick="likeItem('{{ $product->product_id }}')"><i class="fa fa-heart mr-1"></i>
                                        Remove From Wishlist</button>
                                @endif

                            @endauth



                        </div>

                    </form>
                    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog"
                        aria-labelledby="reviewModal" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Review</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="reviewForm">
                                        <div class="form-group">
                                            <label for="username">User Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                required style="border-radius: 20px;">
                                        </div>
                                        <div class="form-group">
                                            <label for="rating">Rating</label>
                                            <div id="starRating" class="text-center">
                                                <span class="fa fa-star" data-rating="1"></span>
                                                <span class="fa fa-star" data-rating="2"></span>
                                                <span class="fa fa-star" data-rating="3"></span>
                                                <span class="fa fa-star" data-rating="4"></span>
                                                <span class="fa fa-star" data-rating="5"></span>
                                                <input type="hidden" id="rating" name="rating" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="comment">Comment</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="3" required style="border-radius: 20px;"></textarea>
                                        </div>
                                    </form>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary rounded-pill"
                                            id="saveReview">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="like-item-{{ $product->product_id }}" action="{{ route('wishlist.store') }}"
                        method="POST" style="display: none;">

                        @csrf

                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                    </form>

                    <div class="d-flex pt-2">

                        <strong class="text-dark mr-2">Share on:</strong>

                        <div class="d-inline-flex social-icon-grp">

                            <a class="text-dark px-2" href="">

                                <i class="fab fa-facebook-f"></i>

                            </a>

                            <a class="text-dark px-2" href="">

                                <i class="fab fa-twitter"></i>

                            </a>

                            <a class="text-dark px-2" href="">

                                <i class="fab fa-linkedin-in"></i>

                            </a>

                            <a class="text-dark px-2" href="">

                                <i class="fab fa-pinterest"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row px-xl-5">

            <div class="col">

                <div class="bg-light p-30">

                    <div class="nav nav-tabs mb-4 product-nav-tabs">

                        <a class="nav-item nav-link text-dark active" data-toggle="tab"
                            href="#tab-pane-1">Description</a>

                        {{-- <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-2">Information</a> --}}

                        <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-3">Reviews

                            ({{ $product->getProductReviewsCount() }})</a>
                        <a class="btn btn-primary" data-toggle="modal" data-target="#contactWithVendor">Contact With
                            Vendor</a>
                    </div>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="tab-pane-1">

                            <h4 class="mb-3">Product Description</h4>

                            <p>{!! $product->product_long_description !!}</p>



                        </div>

                        {{-- <div class="tab-pane fade" id="tab-pane-2">

                            <h4 class="mb-3">Additional Information</h4>

                            <p></p>

                        </div> --}}

                        <div class="tab-pane fade" id="tab-pane-3">

                            <div class="row">

                                <div class="col-md-6">

                                    <h4 class="mb-4">{{ $product->getProductReviewsCount() }} review for

                                        {{ $product->product_name }}

                                    </h4>

                                    @foreach ($reviews as $r)
                                        <div class="media mb-4">

                                            <img src="{{ !empty($r->user->photo)
                                                ? url('uploads/images/profile/' . $r->user->photo)
                                                : url('uploads/images/user_default_image.png') }}"
                                                alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">

                                            <div class="media-body">
                                                @if ($r->user_id > 0)
                                                    <h6><small> {{ '@' . $r?->user?->username }}-
                                                            <i>{{ $r->created_at }}</i></small></h6>
                                                @else
                                                    <h6><small>{{ '@' . $r->username }}-
                                                            <i>{{ $r->created_at }}</i></small></h6>
                                                @endif


                                                @php

                                                    $max_stars = 5;

                                                    $rating_avg = $r->rating;

                                                    $rating_avg_rounded = round($rating_avg * 2) / 2; // Round the rating average to the nearest half

                                                @endphp



                                                <div class="text-primary mr-2">

                                                    @for ($i = 1; $i <= $max_stars; $i++)
                                                        @if ($i <= $rating_avg_rounded)
                                                            <i class="fas fa-star text-success"></i>

                                                            <!-- Full star for whole numbers and half stars -->
                                                        @elseif ($i - 0.5 == $rating_avg_rounded)
                                                            <i class="fas fa-star-half-alt"></i> <!-- Half star -->
                                                        @else
                                                            <i class="fas fa-star text-secondary"></i>

                                                            <!-- Gray star for unrated stars -->
                                                        @endif
                                                    @endfor

                                                </div>

                                                <p>{{ $r->comment }}</p>

                                            </div>

                                        </div>
                                    @endforeach

                                    {{ $reviews->links() }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        {{-- modal contat --}}
        <!-- Modal -->
        <div class="modal fade" id="contactWithVendor" tabindex="-1" aria-labelledby="contactWithVendor"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Contact With Vendor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="contactWithVendorForm">
                            @csrf
                            <div class="row mb-3">
                                <input type="hidden" name="product_id" id=""
                                    value="{{ $product->product_id }}">
                                <div class="form-group col-md-6">
                                    <input type="text" class="form-control" id="name" name="first_name"
                                        placeholder="first_name" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="text" class="form-control" id="last-name" name="last_name"
                                        placeholder="last_name" required>
                                </div>
                                <div class="form-group col-lg-6">
                                    <input type="tel" class="form-control" id="review" name="phone_number"
                                        placeholder="phone_number" required>
                                </div>
                                <div class="form-group col-lg-6">
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="email" required="">
                                </div>
                                <div class="form-group col-md-12">
                                    <textarea class="form-control" name="message" placeholder="Write Your Message" id="exampleFormControlTextarea1"
                                        rows="6" required></textarea>
                                </div>

                            </div>
                        </form>
                        <div class="col-md-12 submit-btn mt-2">
                            <button class="btn btn-solid" id="contactWithVendorFormBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                                Send Your Message</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- modal cantact --}}
    </div>

    <div class="container-fluid pb-5">

        <div class="row px-xl-5">

            <div class="col">

                <div class="bg-light p-30">

                    <p>Seller Info:</p>

                    <div class="row">

                        <div class="col-sm-2">

                            <img src="{{ !empty($product->vendor->user->photo)
                                ? url('uploads/images/profile/' . $product->vendor->user->photo)
                                : url('uploads/images/user_default_image.png') }}"
                                alt="Image" class="img-fluid mr-3 mt-1" style="width: 80px;">

                        </div>

                        <div class="col-sm-10">

                            <h5>{{ $product->vendor->shop_name }} <a
                                    href="{{ route('store.showVendor', $product->vendor_id) }}">Visit Store</a></h5>

                        </div>

                    </div>



                    <hr>

                    <p>{!! $product->vendor->shop_description !!}</p>

                </div>

            </div>

        </div>

    </div>

    <!-- Shop Detail End -->







    <!-- Products Start -->

    <div class="container-fluid py-5">

        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">You May
                Also Like</span></h2>

        <div class="row px-xl-5">

            <div class="col">

                <div class="owl-carousel related-carousel">

                    @if ($similar)
                        {{-- @php
                        dd($similar);
                        @endphp --}}

                        @foreach ($similar as $s)
                            <div class="card rounded px-2 h-100 d-flex flex-column">
                                <a href="{{ route('store.showProduct', $s->product_slug) }}" class="d-block h-100">

                                    <div class="related-img position-relative overflow-hidden">
                                        {{-- @if ($firstVariation && $variationImages && count($variationImages) > 0)
                                            <img id="" class="img-fluid w-100 img-thumbnail h-100"
                                                src="/uploads/images/product/{{ $variationImages[0] }}"
                                                alt="{{ $s->product_name }}">
                                        @elseif($s->images->first())
                                            <img id="" class="img-fluid w-100 img-thumbnail h-100"
                                                src="/uploads/images/product/{{ $s->images->first()->product_image }}"
                                                alt="{{ $s->product_name }}">
                                        @else --}}
                                        <img id="" class="img-fluid w-100 img-thumbnail h-100"
                                            src="/uploads/images/product/{{ $s->product_thumbnail }}"
                                            alt="{{ $s->product_name }}">
                                        {{-- @endif --}}

                                        <div class="product-action">

                                            {{-- <img src="" alt="" id="variantImage> --}}
                                        </div>

                                    </div>

                                    <div class="text-center py-4">

                                        <a class="h6 text-decoration-none text-blue-400"
                                            href="{{ route('store.showProduct', $s->product_slug) }}">

                                            @if (strlen($s->product_name) > 25)
                                                {{ substr($s->product_name, 0, 20) . '  ...  ' }}
                                            @else
                                                {{ $s->product_name }}
                                            @endif

                                        </a>

                                        <div class="d-flex align-items-center justify-content-center mt-2">

                                            {{-- <h5 id="productPrice">&euro; {{ $s->product_price }}</h5> --}}

                                            {{-- <h6 class="text-muted ml-2"><del>$123.00</del></h6> --}}

                                        </div>

                                        <div class="d-flex align-items-center justify-content-center mb-1">

                                            @php

                                                $max_stars = 5;

                                                $rating_avg = $s->getProductReviewsAvg() ?? 0;

                                                $rating_avg_rounded = round($rating_avg * 2) / 2; // Round the rating average to the nearest half

                                            @endphp



                                            <div class="text-primary mr-2">

                                                @for ($i = 1; $i <= $max_stars; $i++)
                                                    @if ($i <= $rating_avg_rounded)
                                                        <i class="fas fa-star text-success"></i>

                                                        <!-- Full star for whole numbers and half stars -->
                                                    @elseif ($i - 0.5 == $rating_avg_rounded)
                                                        <i class="fas fa-star-half-alt"></i> <!-- Half star -->
                                                    @else
                                                        <i class="fas fa-star text-secondary"></i>

                                                        <!-- Gray star for unrated stars -->
                                                    @endif
                                                @endfor

                                            </div>

                                            <br>

                                            <div>

                                                <small>{{ round($rating_avg, 1) }} Stars,
                                                    {{ $s->getProductReviewsCount() }}

                                                    Reviews</small>

                                            </div>

                                        </div>

                                    </div>

                                </a>
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

            /* Set a fixed height for the wrapper */

            overflow: hidden;

            /* Ensure any overflow is hidden */

        }



        .product-img-wrapper img {

            width: 100%;

            /* Allow the image to fill the width of the wrapper */

            height: auto;

            /* Maintain aspect ratio */

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
    </style>

    <!-- Products End -->



@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <script>
        let imageBaseUrl = '/uploads/images/product/';



        $('#color, #ProductID, #SizeID, #dimentionId').change(function(e) {
            // e.preventDefault();
            let currency_id = '{{ session('currency_id', 0) }}';
            let color_name = $('#color').find('option:selected').val();
            let productID = $("#ProductID").val();
            let size_name = $("#sizeID").find('option:selected').val();
            let selectedOption = $("#dimentionId").find('option:selected').val();
            let height = null;
            let width = null;
            let length = null;
            let weight = null;

            if (selectedOption) {
                let dimension = JSON.parse(selectedOption);
                let height = dimension.height;
                let width = dimension.width;
                let length = dimension.length;
                let weight = dimension.weight;
            }


            $.ajax({
                type: "POST",
                url: "{{ route('getVariationDetails') }}",
                data: {
                    color_name: color_name,
                    product_id: productID,
                    size_name: size_name,
                    width: width,
                    height: height,
                    length: length,
                    weight: weight,
                    currency_id: currency_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        console.log(response.product_variation.id);
                        $("#productVariationId").val(response.product_variation.id);

                        $('#ProductImage').show();
                        if (response.product_images) {
                            $("#ProductImage").attr("src", imageBaseUrl + response.product_images[0]);

                            $('#productImagesDiv').empty();
                            response.product_images.forEach(function(image) {
                                $('#productImagesDiv').append(`

                                    <img class="hover-effect mb-2"
                                            src="${imageBaseUrl + image}"
                                            alt="{{ $product->product_name }}"
                                            style="cursor: pointer; width: 100px; height: 60px;"
                                            data-full="${imageBaseUrl + image}">

                                `);
                            });


                        } else {
                            $("#ProductImage").attr("src", '');
                            $("#ProductImage").attr("alt", 'Not Available');

                        }
                        if (response.video_url) {
                            $('#productVideoDiv').empty();
                            response.video_url.forEach(function(video) {
                                $('#productVideoDiv').append(`<video class="hover-effect img-thumbnail hover-effect mb-2" id="hover-effect"
                                src="${imageBaseUrl + video}"
                                style="cursor: pointer; width: 100px; height: 100px;"
                                data-full="${imageBaseUrl + video}" width="100%"
                                controls></video>`);
                            });
                        }

                        $('.hover-effect').hover(function() {
                            let src = $(this).data('full');
                            if ($(this).is('img')) {
                                $('#ProductImage').attr('src', src).show();
                                $('#ProductVideo').hide();
                            } else if ($(this).is('video')) {
                                $('#ProductVideo').attr('src', src).show();
                                $('#ProductImage').hide();
                            }
                        });
                        if (response.product_variation.product_quantity > 0) {
                            $("#productPrice").empty();
                            let price = 0;
                            $("#productPrice").text(response.formatedPrice);
                        } else {
                            $("#productPrice").empty();
                            $("#productPrice").text("Quantity Not Available");
                        }
                        $('#shipping_cost_view').data('euro-shipping', response.shipping_cost_in_euro);
                        $('#shipping_cost_view').text(response.shipping_cost);
                        $('#ProductVideo').hide();
                    } else {
                        $("#ProductImage").attr("src", '');
                        $("#ProductImage").attr("alt", 'Product Not Available');
                        $("#productPrice").empty();
                        $("#productPrice").text('Product Not Available');
                    }
                }
            });


        });

        $(document).ready(function() {
            var stars = $('#starRating .fa-star');

            stars.on('click', function() {
                var rating = $(this).data('rating');
                $('#rating').val(rating);
                stars.removeClass('checked');
                for (var i = 0; i < rating; i++) {
                    $(stars[i]).addClass('checked');
                }
            });

            $('#saveReview').click(function() {
                var formData = {
                    username: $('#username').val(),
                    rating: $('#rating').val(),
                    comment: $('#comment').val(),
                    product_id: "{{ $product->product_id }}",
                };
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('store.product.review') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.msg,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Something went wrong",
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: "Something went wrong",
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }
                });
            });

            $("#contactWithVendorFormBtn").on('click', function() {
                var formData = $('#contactWithVendorForm').serializeArray();

                var $submitButton = $('#contactWithVendorFormBtn');
                var $spinner = $submitButton.find('.spinner-border');
                // Show spinner and disable button
                $spinner.removeClass('d-none');
                $submitButton.prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('store.product.contactWithVendor') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.msg,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                            // Show spinner and disable button
                            $spinner.addClass('d-none');
                            $submitButton.prop('disabled', false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Something went wrong",
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: "Something went wrong",
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });

                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            // Handle click event on carousel items

            $('#product-carousel .carousel-item').on('click', function() {

                var imageUrl = $(this).find('img').attr('src');



                // Set the clicked image in the fullscreen modal

                $('#fullscreenImage').attr('src', imageUrl);



                // Show the fullscreen modal

                $('#fullscreenModal').modal('show');

            });

        });
    </script>

    <script>
        $('.hover-effect').hover(function() {
            let src = $(this).data('full');

            if ($(this).is('img')) {
                $('#ProductImage').attr('src', src).show();
                $('#ProductVideo').hide();
            } else if ($(this).is('video')) {
                $('#ProductVideo').attr('src', src).show();
                $('#ProductImage').hide();
            }
        });
    </script>

    <script>
        function likeItem(id) {

            event.preventDefault();

            let y = 1; //confirm('Are you sure you want to remove this item form your wishlist?');

            if (y) {

                document.getElementById('like-item-' + id).submit();

            }

        }

        // $().c('change', function() {
        //     alert('fsda')
        //     $.ajax({
        //         type: 'POST',
        //         data:[
        //             'colorId'=>$('#color'.val()),
        //         ]
        //         url: "{{ route('getVariationDetails') }}",
        //         success: function(response) {

        //         },
        //         error: function(error) {

        //         },
        //     })
        // });
    </script>

@endsection
