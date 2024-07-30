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
                    <span class="breadcrumb-item active">Checkout Complete</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    @if ($status == 'success')
                        <div class="card-header">
                            <h3 class="card-title">Payment Success</h3>
                        </div>
                        <div class="card-body">
                            <p>Thank you for your payment!</p>
                            <p>Your payment details:</p>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Session ID:</strong> {{ $session->id }}</li>
                                <li class="list-group-item"><strong>Amount Total:</strong>
                                    {{ $session->amount_total / 100 }}
                                    {{ strtoupper($session->currency) }}</li>
                                <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ $session->customer_details->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->customer_details->email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>Your order is now complete. You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="/" class="btn btn-primary">Back to Home</a>
                            </div>
                        </div>
                    @else
                        <div class="card-header">
                            <h3 class="card-title">Payment Failed</h3>
                        </div>
                        <div class="card-body">
                            <p>Sorry, your payment could not be processed.</p>
                            <p>Payment details:</p>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Session ID:</strong> {{ $session->id }}</li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>Please try again or contact support for assistance.</p>
                            <div class="mt-3">
                                <a href="{{ route('/') }}" class="btn btn-primary">Back to Home</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
