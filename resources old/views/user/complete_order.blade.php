@extends('user.layout.app')
@section('page_name', 'Checkout')
@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Checkout</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <div class="container-fluid">
        <div id="sumup-card"></div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>
    <script type="text/javascript">
        SumUpCard.mount({
            id: 'sumup-card',
            checkoutId: '{{$client_secret}}',
            onResponse: function(type, body) {
                if(type == 'success' && body.status == 'PAID'){
                    window.location.assign('{{route("payment_complete_sumup", $client_secret)}}');
                }else{
                    window.location.assign('{{route("payment_complete_sumup", $client_secret)}}');
                }
                // console.log('Type', type);
                // console.log('Body', body);
            },
        });
    </script>

@endsection
