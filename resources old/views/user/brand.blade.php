@extends('user.layout.app')
@section('page_name', 'Shopping Cart')
@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('store.showAllCategory') }}">Categories</a>
                    <span class="breadcrumb-item active">{{ $brand->brand_name }}</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <!-- category Start -->
    <div class="container-fluid mt-2">
        @include('user.partials.filter_sidebar')
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
    <!-- category End -->
@endsection
@section('scripts')
    <script>
        function submit_filter_form() {
            $('#filter_form').submit();
        }
    </script>
@endsection
