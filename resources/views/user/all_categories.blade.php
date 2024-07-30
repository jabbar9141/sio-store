@extends('user.layout.app')
@section('page_name', 'Shopping Cart')
@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30 px-4">
                    <a class="breadcrumb-item text-dark" href="{{ url('/') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ url('/store/my-cart') }}">Shop</a>
                    <span class="breadcrumb-item active">All Categories</span>
                </nav>
            </div>
        </div>
    </div>
    <div class="container mt-2">
        <h2 class="text-center mb-5">All Categories</h2>
        <div class="row">
            @foreach ($categories as $category)
                <div class="col-lg-3 w-full col-md-4 col-12 col-sm-4 pb-4">
                    <a href="{{ route('store.showCategory', $category->category_slug) }}">
                        <div class="card product-card p-2">
                            <!-- <img src="/uploads/images/category/{{ $category->category_image }}" class="card-img-top w-100" alt="{{ $category->category_name }}"> -->

                            <div class="product-card-img">
                                {{-- <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cHJvZHVjdHxlbnwwfHwwfHx8MA%3D%3D" alt="" class="h-100 w-100"> --}}
                                <img src="/uploads/images/category/{{ $category->category_image }}" class="h-100 w-100"
                                    alt="{{ $category->category_name }}">
                            </div>
                            <div class="card-body px-1 py-3">
                                <h5 class="card-title">
                                    @if (strlen($category->category_name) > 10)
                                        {{ substr($category->category_name, 0, 20) . '  ...  ' }}
                                    @else
                                        {{ $category->category_name }}
                                    @endif
                                </h5>
                                <p class="card-text">Products: {{ count($category->products) }}</p>
                                <a href="{{ route('store.showCategory', $category->category_slug) }}"
                                    class="product-btn">View
                                    Category</a>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <!-- Pagination Links -->
        {{ $categories->links() }}

    </div>
    <!-- category End -->
@endsection
@section('scripts')
@endsection
