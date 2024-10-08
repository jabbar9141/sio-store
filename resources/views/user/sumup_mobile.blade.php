<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Siostore Checkout - {{ $session->id }}</title>
</head>

<body>
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
                                {{-- <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li> --}}
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="/" class="btn btn-primary">Back to Home</a>
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
                                {{-- <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li> --}}
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="/" class="btn btn-primary">Back to Home</a>
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
                                {{-- <li class="list-group-item"><strong>Customer Name:</strong>
                                    {{ auth()->user()->name }}</li> --}}
                                <li class="list-group-item"><strong>Email:</strong> {{ $session->pay_from_email }}
                                </li>
                                {{-- Add more details as needed --}}
                            </ul>
                            <p>You will receive a confirmation email shortly.</p>
                            <div class="mt-3">
                                <a href="/" class="btn btn-primary">Back to Home</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>
