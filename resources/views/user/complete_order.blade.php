@extends('user.layout.app')
@section('page_name', 'Checkout')
@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid m-0">
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

        <div class="container">

            <!-- Display any success or error messages -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Subscription Form -->

            <div class="card w-75 mx-auto mb-5">
                <div class="card-body">
                    <h3 style="text-align: start" class="mb-4">Stripe</h3>
                    <form action="{{ route('payment') }}" method="POST" id="subscription-form">
                        @csrf
                        <div class="mb-2">
                            <label for="name" class="form-labl">Card Holder Name</label>
                            <input type="text" class="form-control bg-transparent b-none" required
                                placeholder="Enter Name">
                        </div>
                        <div class="mb-2">
                            <label for="email" class="form-labl">Email</label>
                            <input type="email" class="form-control bg-transparent b-none" required
                                placeholder="Enter Email">
                        </div>
                        <div class="mb-2">
                            <label for="" class="form-label">Enter Card Details</label>
                            <div id="card-element" class="form-control">
                            </div>
                        </div>
                        <div>
                            <input type="hidden" value="{{ $cost }}" name="price">
                            <input type="hidden" value="{{ $order_id }}" name="order_id">
                        </div>
                        <div style="width:100%; text-align:center">
                            <button style="text-align: center" id="submit-button" class="btn btn-primary mt-4">Pay
                                {{ $cost }} €</button>

                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    @endsection
    @section('scripts')

        <script src="https://js.stripe.com/v3/"></script>


        <script>
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();
            var card = elements.create('card');
            card.mount('#card-element');

            // Handle form submission
            var form = document.getElementById('subscription-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error.
                        alert(result.error.message);
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('subscription-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        </script>



    @endsection
