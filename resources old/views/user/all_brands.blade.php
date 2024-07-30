@extends('user.layout.app')
@section('page_name', 'Shopping Cart')
@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30 px-4">
                    <a class="breadcrumb-item text-dark" href="{{ url('/') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ url('/store/my-cart') }}">Shop</a>
                    <span class="breadcrumb-item active">All Brands</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <h2 class="text-center mb-5">All Brands</h2>

        <div class="row">
            @foreach ($brands as $brand)
                <div class="col-lg-3 w-full col-md-4 col-6 col-sm-4 pb-4">
                    <div class="card product-card p-2">
                        <div class="product-card-img">

                            {{-- <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cHJvZHVjdHxlbnwwfHwwfHx8MA%3D%3D" alt="" class="h-100 w-100"> --}}
                            @if (isset($brand->brand_image))
                                <img src="/uploads/images/brand/{{ $brand->brand_image }}" class="h-100 w-100"
                                    alt="{{ $brand->brand_name }}">
                            @else
                                <img src="{{ asset('uploads/images/product/bf51bb3bd5965bed168991794f01eafc.png') }}"
                                    class="h-100 w-100" alt="no-image">
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                @if (strlen($brand->brand_name) > 10)
                                    {{ substr($brand->brand_name, 0, 7) . '  ...  ' }}
                                @else
                                    {{ $brand->brand_name }}
                                @endif
                            </h5>
                            <p class="card-text">Products: {{ count($brand->products) }}</p>
                            <a href="{{ route('store.showBrand', $brand->brand_slug) }}" class="product-btn">View
                                Brand</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $brands->links() }}
    </div>
@endsection
@section('scripts')
@endsection
