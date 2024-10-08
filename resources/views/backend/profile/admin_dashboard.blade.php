@extends('backend.layouts.app')
@section('PageTitle', 'Dashboard')


@section('content')
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 bg-gradient-deepblue">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">{{ $total_orders }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-cart fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-light-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Total Orders</p>
                        {{-- <p class="mb-0 ms-auto">+4.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 bg-gradient-orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">&euro;{{ number_format($total_revenue, 2) }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-dollar fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-light-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Total Revenue (Orders)</p>
                        {{-- <p class="mb-0 ms-auto">+1.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 bg-gradient-orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">&euro;{{ number_format($total_sales_pos_online, 2) }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-dollar fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-info-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Total Sales POS/Orders</p>
                        {{-- <p class="mb-0 ms-auto">+1.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 bg-gradient-orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">&euro;{{ number_format($total_daily_profit, 2) }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-dollar fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-info-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Today Total Sales Profit</p>
                        {{-- <p class="mb-0 ms-auto">+1.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 bg-gradient-orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">&euro;{{ number_format($total_inventory_cost, 2) }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-dollar fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-info-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Total Inventory Cost</p>
                        {{-- <p class="mb-0 ms-auto">+1.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 bg-gradient-orange">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">&euro;{{ number_format($total_inventory_profit, 2) }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-dollar fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-info-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Total Inventory Profit</p>
                        {{-- <p class="mb-0 ms-auto">+1.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 bg-gradient-ohhappiness">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">{{ $users_total }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-group fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-light-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Customers</p>
                        {{-- <p class="mb-0 ms-auto">+5.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 bg-gradient-ibiza">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-white">{{ $latest_reviews->total() }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-star fs-3 text-white'></i>
                        </div>
                    </div>
                    <div class="progress my-3 bg-light-transparent" style="height:3px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <p class="mb-0">Reviews</p>
                        {{-- <p class="mb-0 ms-auto">+2.2%<span><i class='bx bx-up-arrow-alt'></i></span></p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->
    <div class="row">
        <div class="col-12 col-lg-6 col-xl-4 d-flex">
            <div class="card radius-10 overflow-hidden w-100">
                <div class="card-body">
                    <p>Total Vendors Earning</p>
                    <h4 class="mb-0">&euro;{{ number_format($total_revenue, 2) }}</h4>
                    {{-- <small>1.4% <i class="zmdi zmdi-long-arrow-up"></i> Since Last Month</small> --}}
                    <hr>
                    <p>Total Payouts Requests</p>
                    <h4 class="mb-0">&euro;{{ number_format($cash_out_pending, 2) }}</h4>
                    <small><a href="{{ route('payout-list') }}">All Payout Requests</a></small>
                    <hr>
                    <p>Total Payouts Approved</p>
                    <h4 class="mb-0">&euro;{{ number_format($cash_out, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header border-bottom bg-transparent">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Latest Customer Reviews</h6>
                        </div>
                        <div class="font-22 ms-auto"><i class="bx bx-dots-horizontal-rounded"></i>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($latest_reviews as $r)
                        <li class="list-group-item bg-transparent">

                            <div class="row d-flex justify-content-between">
                                <div class="col-sm-1">
                                    <img src="{{ !empty($r->user->photo)
                                        ? url('uploads/images/profile/' . $r->user->photo)
                                        : url('uploads/images/user_default_image.png') }}"
                                        alt="Image" class="img-fluid mr-3 mt-1" alt="user avatar"
                                        class="rounded-circle" width="55" height="55">
                                </div>
                                <div class="col-sm-8">
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $r->product->product_name }} <small
                                                class="ms-4">{{ $r->created_at }}</small></h6>
                                        @if ($r->user_id > 0)
                                            <p class="mb-0 small-font">{{ '@' . $r->user->username }} :
                                                {{ $r->comment }}
                                            </p>
                                        @else
                                            <p class="mb-0 small-font">{{ '@' . $r->username }} : {{ $r->comment }}
                                            </p>
                                        @endif


                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    @php
                                        $max_stars = 5;
                                        $rating_avg = $r->rating;
                                        $rating_avg_rounded = round($rating_avg * 2) / 2; // Round the rating average to the nearest half
                                    @endphp
                                    <div class="ms-auto star">
                                        @for ($i = 1; $i <= $max_stars; $i++)
                                            @if ($i <= $rating_avg_rounded)
                                                <i class="fas fa-star text-warning"></i>
                                                <!-- Full star for whole numbers and half stars -->
                                            @elseif ($i - 0.5 == $rating_avg_rounded)
                                                <i class="fas fa-star-half-alt text-warning"></i> <!-- Half star -->
                                            @else
                                                <i class="fas fa-star text-light"></i>
                                                <!-- Gray star for unrated stars -->
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="m-2">
                    {{ $latest_reviews->links() }}
                </div>
            </div>
        </div>
    </div><!--End Row-->


    <div class="card radius-10">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">Latest Orders Summary</h5>
                </div>
                <div class="font-22 ms-auto"><i class="bx bx-dots-horizontal-rounded"></i>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order id</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($orders_list) && count($orders_list) > 0)
                            @foreach ($orders_list as $order)
                                <tr>
                                    <td>{{ $order->order->order_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="recent-product-img">
                                                @if ($order->item->images && count($order->item->images) > 0)
                                                    <img src="{{ asset('/uploads/images/product/' . $order->item->images[0]->product_image) }}"
                                                        alt="">
                                                @else
                                                    <img src="{{ asset('/uploads/images/product/' . $order->item->product_thumbnail) }}"
                                                        alt="">
                                                @endif

                                            </div>
                                            <div class="ms-2">
                                                <h6 class="mb-1 font-14">{{ $order->item->product_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order->variation->product->vendor->shop_name }}</td>
                                    <td>{{ $order->order->user->name }}</td>
                                    <td>{{ date('h:i d-m-Y', $order->created_at->timestamp) }}</td>
                                    <td>&euro; {{ $order->price }}</td>
                                    <td>
                                        <div class="badge rounded-pill bg-secondary text-info w-100">{{ $order->status }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2">No items to show</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
