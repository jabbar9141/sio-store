    <style>
        .product-img {
            height: unset !important;
        }

        .products-header {
            background: #232f3e21;
            padding: 4px 13px;
            border-radius: 5px;
        }

        #sort_by {
            border-radius: 7px;
            padding: 0px 8px;
        }

        .price-text h5 {
            font-size: 16px;
            font-weight: 700 !important;
        }

        .price-text del {
            font-size: 12px;
            position: absolute;
            top: -2px;
            text-decoration: line-through;
        }

        .product-card-img-1 {
            height: 175px;
            overflow: hidden;
            text-align: center;
        }
        @media only screen and (max-width: 768px) {
            .mobile-css {
                padding-right: 3px !important;
                padding-left: 6px !important; 
            }
        }
    </style>

    <form action="{{ route('store.searchProducts') }}" method="get" id="filter_form">
        @if (isset($vendor))
            <input type="hidden" name="vendors[]" value="{{ $vendor->vendor_id }}">
        @elseif (isset($brand))
            <input type="hidden" name="brands[]" value="{{ $brand->brand_id }}">
        @elseif(isset($category))
            <input type="hidden" name="categories[]" value="{{ $category->category_id }}">
        @endif

        <div class="container-fluid">

            <div class="row px-xl-5">
                <div class="col-12">
                    @if (isset($vendor))
                        <h2>{{ $vendor->shop_name }}</h2>
                    @elseif (isset($brand))
                        <h2>{{ $brand->brand_name }}</h2>
                    @elseif(isset($category))
                        <h2>{{ $category->category_name }}</h2>
                    @endif

                </div>
                <!-- Shop Sidebar Start -->
                <!-- Shop Sidebar Start -->
                <div class="col-lg-3 col-md-4">

                    <div class="bg-light p-4 mb-30">
                        <h6 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Filter
                                by price</span></h6>
                        @php
                            // Extract max and min prices from the products
                            $maxPrice = $products->max('product_price');
                            $minPrice = $products->min('product_price');
                        @endphp

                        <div class="form-group">
                            <label for="minPrice">Min Price</label>
                            <input type="number" class="form-control" id="minPrice" name="min_price"
                                value="{{ request()->min_price ? request()->min_price : $minPrice }}"
                                min="{{ $minPrice }}" max="{{ $maxPrice }}" step="any">
                        </div>

                        <div class="form-group">
                            <label for="maxPrice">Max Price</label>
                            <input type="number" class="form-control" id="maxPrice" name="max_price"
                                value="{{ request()->max_price ? request()->max_price : $maxPrice }}"
                                min="{{ $minPrice }}" max="{{ $maxPrice }}" step="any">
                        </div>

                        <h6 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Filter by brand</span></h6>
                        @foreach ($products->unique('brand') as $product)
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input"
                                    {{ request()->brands && in_array($product->brand->brand_id, request()->brands) ? 'checked' : '' }}
                                    id="brand-{{ $product->brand->brand_id }}" name="brands[]"
                                    value="{{ $product->brand->brand_id }}">
                                <label class="custom-control-label"
                                    for="brand-{{ $product->brand->brand_id }}">{{ $product->brand->brand_name }}</label>
                                <span class="badge border font-weight-normal">{{ $product->count }}</span>
                            </div>
                        @endforeach
                        <!-- Brand Filter End -->

                        <!-- Vendor Filter Start -->
                        <h6 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Filter by Vendor</span></h6>
                        @foreach ($products->unique('vendor') as $product)
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input"
                                    {{ request()->vendors && in_array($product->brand->vendor_id, request()->vendors) ? 'checked' : '' }}
                                    id="vendor-{{ $product->vendor->vendor_id }}" name="vendors[]"
                                    value="{{ $product->vendor->vendor_id }}">
                                <label class="custom-control-label"
                                    for="vendor-{{ $product->vendor->vendor_id }}">{{ $product->vendor->shop_name }}</label>
                                <span class="badge border font-weight-normal">{{ $product->count }}</span>
                            </div>
                        @endforeach
                        <!-- Vendor Filter End -->

                        <!-- Category Filter Start -->
                        <h6 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Filter by category</span></h6>
                        @foreach ($products->unique('category') as $product)
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input"
                                    {{ request()->categories && in_array($product->category->category_id, request()->categories) ? 'checked' : '' }}
                                    id="category-{{ $product->category->category_id ?? '' }}" name="categories[]"
                                    value="{{ $product->category->category_id ?? '' }}">
                                <label class="custom-control-label"
                                    for="category-{{ $product->category->category_id ?? '' }}">{{ $product->category->category_name ?? '' }}</label>
                                <?php
                                $producTs = $product->category->products ?? [];
                                ?>
                                <span class="badge border font-weight-normal">{{ count($producTs) }}</span>
                            </div>
                        @endforeach
                        <!-- Category Filter End -->

                        <hr>
                        <button type="submit" class="btn btn-primary"
                            {{ count($products) < 1 ? 'disabled' : '' }}>Apply
                            Filter</button>
                    </div>

                </div>
                <!-- Shop Sidebar End -->


                <!-- Shop Product Start -->
                <div class="col-lg-9 col-md-8">
                    <div class="row pb-3">
                        <div class="col-12 pb-1 px-0">
                            <div class="d-flex align-items-center justify-content-between mb-3 products-header">
                                <div>
                                    <button class="btn btn-sm btn-light"><i class="fa fa-th-large"></i></button>
                                    <button class="btn btn-sm btn-light ml-2"><i class="fa fa-bars"></i></button>
                                </div>
                                <div class="ml-2">
                                    <div class="btn-group">
                                        <select name="sort_by" id="sort_by" class="form-control"
                                            onchange="submit_filter_form()">
                                            <option value="">--sort by--</option>
                                            <option value="latest">Latest</option>
                                            <option value="price_asc">Price: Lowest First</option>
                                            <option value="price_desc">Price: Highest First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                @if ($products && count($products) > 0)
                                    @foreach ($products as $product)
                                        <div class="col-lg-3 col-md-4 col-6 pb-4 mobile-css"> <!-- Changed col-12 to col-6 for mobile -->
                                            <a class="h6 text-decoration-none text-blue-400"
                                                href="{{ route('store.showProduct', $product->product_slug) }}">
                                                <div class="card rounded px-2 py-2">
                                                    <div class="product-img position-relative">
                                                        <div class="product-card-img-1">
                                                            @if (!empty($product->images) && isset($product->images[0]))
                                                                <img src="/uploads/images/product/{{ $product->images[0]->product_image }}" alt="" class="h-100 w-75">
                                                            @else
                                                                @if ($product->product_thumbnail)
                                                                    <img src="/uploads/images/product/{{ $product->product_thumbnail }}" alt="" class="h-100 w-75">
                                                                @else
                                                                    <img src="/uploads/images/product/0826d25041400d65bf9d7d1221b978ed.jpg" alt="Default Image" class="h-100 w-75">
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="py-2">
                                                        <a class="h6 text-decoration-none text-blue-400" href="{{ route('store.showProduct', $product->product_slug) }}">
                                                            @if (strlen($product->product_name) > 25)
                                                                {{ substr($product->product_name, 0, 20) . '  ...  ' }}
                                                            @else
                                                                {{ $product->product_name }}
                                                            @endif
                                                        </a>
                                                        @php
                                                            $price = 0;
                                                            $firstVariation = $product?->variations?->first();
                                                            if ($firstVariation) {
                                                                $price = $firstVariation->price;
                                                            } else {
                                                                $price = $product->product_price;
                                                            }
                                                            $price = App\MyHelpers::fromEuroView(
                                                                session('currency_id', 0),
                                                                $price,
                                                            );
                                                        @endphp
                                                        <div class="d-flex align-items-center justify-content-start mt-2 position-relative price-text">
                                                            <h5>{{ $price }}</h5>
                                                            <h6 class="text-muted ml-2">
                                                                {{-- <del>{{ $price }}</del> --}}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <h6><i>No products found</i></h6>
                                @endif
                            </div>
                            
                        </div>

                        <div class="col-12 pt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
                <!-- Shop Product End -->

            </div>
        </div>
    </form>
