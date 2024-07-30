@extends('user.layout.app')
@section('page_name', 'Shopping Cart')
@section('content')
<!-- Breadcrumb Start -->
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <nav class="breadcrumb bg-light mb-30">
                <a class="breadcrumb-item text-dark" href="#">Home</a>
                <a class="breadcrumb-item text-dark" href="#">Shop</a>
                <span class="breadcrumb-item active">Products</span>
            </nav>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->
<!-- category Start -->
<div class="container-fluid mt-2">
    @include('user.partials.filter_sidebar')
</div>
<!-- category End -->
@endsection
@section('scripts')
<script>
    function submit_filter_form() {
        $('#filter_form').submit();
    }
</script>
@endsection