@php
    use App\Models\ProductVariation;
@endphp
@extends('user.layout.app')
@section('page_name', 'Shopping Cart')
@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ url('/') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ url('store/my-cart') }}">Shop</a>
                    <span class="breadcrumb-item active">Shopping Cart</span>
                </nav>
            </div>
        </div>
    </div>



    <!-- Cart Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @if (null != session('cart') && count(session('cart')) > 0)
                            @php
                                $session_cart = session('cart');
                                $cart_total = 0;
                            @endphp
                            @foreach ($session_cart as $it)
                                @php
                                    $the_product = App\MyHelpers::getProductById($it['product_id']);
                                    $variation =
                                        ProductVariation::find($it['variation_id']) ?? $the_product->variations[0];
                                    if ($variation) {
                                        $price = $variation->price;
                                    } else {
                                        $price = $the_product->product_pricel;
                                    }
                                @endphp
                                <tr>
                                    <td class="align-middle"><img src="img/product-1.jpg" alt=""
                                            style="width: 50px;">
                                        {{ $the_product->product_name }}
                                        {{-- ({{ json_encode($it['variations']) }}) --}}
                                    </td>
                                    <td class="align-middle">
                                        {{ App\MyHelpers::fromEuroView(session('currency_id', 0), $price) }}</td>
                                    <td class="align-middle">
                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                            {{-- <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-minus">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div> --}}
                                            <input type="text"
                                                class="form-control form-control-sm bg-secondary border-0 text-center"
                                                value="{{ $it['qty'] }}" readonly>
                                            {{-- <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div> --}}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $the_tot = $it['qty'] * $price;
                                            $cart_total += $the_tot;
                                        @endphp
                                        {{ App\MyHelpers::fromEuroView(session('currency_id', 0), $the_tot) }}
                                    </td>
                                    <td class="align-middle"><a class="btn btn-sm btn-danger"
                                            href="{{ route('store.removeItem', ['product_id' => $it['product_id'], 'variations' => json_encode($it['variations'])]) }}"
                                            onclick="return confirm('Are you sure you want to remove this item form cart')"><i
                                                class="fa fa-times"></i></button></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">
                                    <i>No items in your cart currently <a href="/">go home</a></i>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
            @if (null != session('cart') && count(session('cart')) > 0)
                <div class="col-lg-4">
                    <form class="mb-30" action="">
                        <div class="input-group">
                            <input type="text" class="form-control border-0 p-4" placeholder="Coupon Code">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Apply Coupon</button>
                            </div>
                        </div>
                    </form>
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart
                            Summary</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="border-bottom pb-2">
                            <div class="d-flex justify-content-between mb-3">
                                <h6>Subtotal</h6>
                                <h6>{{ App\MyHelpers::fromEuroView(session('currency_id', 0), $cart_total) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Shipping</h6>
                                <h6 class="font-weight-medium">Not added yet</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Discount</h6>
                                <h6 class="font-weight-medium">
                                    {{ App\MyHelpers::fromEuroView(session('currency_id', 0), 0) }}</h6>
                            </div>
                        </div>
                        <div class="pt-2">
                            <div class="d-flex justify-content-between mt-2">
                                <h5>Total</h5>
                                <h5>{{ App\MyHelpers::fromEuroView(session('currency_id', 0), $cart_total) }}</h5>
                            </div>
                            <a href="{{ route('store.order.init') }}"
                                class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To
                                Checkout</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Cart End -->
@endsection
@section('scripts')
@endsection
