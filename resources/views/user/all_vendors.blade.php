@extends('user.layout.app')
@section('page_name', 'All Vendors')
@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30 px-4">
                    <a class="breadcrumb-item text-dark" href="{{ url('/') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ url('/store/my-cart') }}">Shop</a>
                    <span class="breadcrumb-item active">All Vendors</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="container mt-2">
        <h2 class="text-center mb-5">All Vendors</h2>
        <div class="row">
            @foreach ($vendors as $vendor)
                <div class="col-lg-3 w-full col-md-4 col-12 col-sm-4 pb-4">
                    <div class="card product-card p-2">
                        <div class="product-card-img">
                            @if (!empty($vendor->user->photo) && isset($vendor->user->photo))
                                <img src="/uploads/images/profile/{{ $vendor->user->photo }}" alt=""
                                    class="h-100 w-100">
                            @else
                                <img src="{{ asset('uploads/images/product/bf51bb3bd5965bed168991794f01eafc.png') }}"
                                    class="h-100 w-100" alt="no-image">
                            @endif
                            {{-- <img src="/uploads/images/profile/{{ $vendor->user->photo }}" class="card-img-top w-100"
                                alt="{{ $vendor->shop_name }}">  --}}
                            {{-- <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cHJvZHVjdHxlbnwwfHwwfHx8MA%3D%3D" alt="" class="h-100 w-100"> --}}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                @if (strlen($vendor->shop_name) > 10)
                                    {{ substr($vendor->shop_name, 0, 20) . '  ...  ' }}
                                @else
                                    {{ $vendor->shop_name }}
                                @endif
                            </h5>
                            <p class="card-text">Products: {{ count($vendor->products) }}</p>
                            <a href="{{ route('store.showVendor', $vendor->vendor_id) }}" class="product-btn">View
                                Vendor</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $vendors->links() }}
    </div>
@endsection
@section('scripts')
@endsection
