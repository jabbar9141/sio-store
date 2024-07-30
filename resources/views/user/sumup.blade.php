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
                    @if ($session->status == 'PAID')
                        <div class="card-header">
                            <h3 class="card-title">Payment Success</h3>
                        </div>
                        <div class="card-body">
                            <p>Thank you for your payment!</p>
                            <p>Your payment details:</p>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Payment status:</strong>
                                    <span class="badge bg-success">Paid</span>
                                </li>
                                <li class="list-group-item"><strong>Session ID:</strong> {{ $session->id }}</li>
                                <li class="list-group-item"><strong>Payment Channel:</strong> SUMUP </li>
                                <li class="list-group-item"><strong>Amount Total:</strong>
                                    {{ $session->amount }}
                                    {{ strtoupper($session->currency) }}</li>
                                <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="{{route('dashboard')}}" class="btn btn-primary">Back to Dashboard</a>
                            </div>
                        </div>
                    @elseif ($session->status == 'PENDING')
                        <div class="card-header">
                            <h3 class="card-title">Payment Processing</h3>
                        </div>
                        <div class="card-body">
                            <p>Thank you for your payment!</p>
                            <p>Your payment details:</p>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Payment status:</strong>
                                    <span class="badge bg-warning">Processing</span>
                                </li>
                                <li class="list-group-item"><strong>Session ID:</strong> {{ $session->id }}</li>
                                <li class="list-group-item"><strong>Payment Channel:</strong> SUMUP </li>
                                <li class="list-group-item"><strong>Amount Total:</strong>
                                    {{ $session->amount }}
                                    {{ strtoupper($session->currency) }}</li>
                                <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="{{route('dashboard')}}" class="btn btn-primary">Back to Dashboard</a>
                            </div>
                        </div>
                    @else
                        <div class="card-header">
                            <h3 class="card-title">Payment Failed</h3>
                        </div>
                        <div class="card-body">
                            <p>Thank you for your payment attempt!</p>
                            <p>Your payment attempt details:</p>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Payment status:</strong>
                                    <span class="badge bg-danger">Failed</span>
                                </li>
                                <li class="list-group-item"><strong>Session ID:</strong> {{ $session->id }}</li>
                                <li class="list-group-item"><strong>Payment Channel:</strong> SUMUP </li>
                                <li class="list-group-item"><strong>Amount Total:</strong>
                                    {{ $session->amount }}
                                    {{ strtoupper($session->currency) }}</li>
                                <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="{{route('dashboard')}}" class="btn btn-primary">Back to Dashboard</a>
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
