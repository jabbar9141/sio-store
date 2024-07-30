@php
    use Illuminate\Support\Facades\Auth;
    $status = Auth::user()->status;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'All Online Orders')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Orders</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Online Orders List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
    @if (!$status)
        <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
            <div class="d-flex align-items-center">
                <div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 text-white">Your account is still not activated</h6>
                    <div class="text-white">Wait for admin to activate your account</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="" class="form-inline" method="get">
                    <div class="row">
                        <small>Select date range and click proceed to get transactions</small>
                        <br>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="start_date" placeholder="start date" value="{{request()->start_date ?? ''}}" required>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="end_date" placeholder="end date" value="{{request()->end_date ?? ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">Proceed</button>
                        </div>
                    </div>
                </form>
                <hr>
                @if (isset($stats))
                    <table class="table table-bordered">
                        <tr>
                            <th>Start Date</th>
                            <td>{{ request()->start_date ?? 'N/A' }}</td>
                            <th>End Date</th>
                            <td>{{ request()->end_date ?? 'N/A' }}</td>
                            <th>Status filter</th>
                            <td>{{ request()->status ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount(&euro;)</th>
                            <td>{{ $stats['total_amt'] }}</td>
                            <th>Total Successful(&euro;)</th>
                            <td>{{ $stats['total_success'] }}</td>
                            <th>Total Pending(&euro;)</th>
                            <td>{{ $stats['total_pending'] }}</td>
                            <th>Total Cancelled(&euro;)</th>
                            <td>{{ $stats['total_cancelled'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Count</th>
                            <td>{{ $stats['total_count'] }}</td>
                            <th>Count Successful</th>
                            <td>{{ $stats['count_success'] }}</td>
                            <th>Count Pending</th>
                            <td>{{ $stats['count_pending'] }}</td>
                            <th>Count Cancelled</th>
                            <td>{{ $stats['count_cancelled'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Customers</th>
                            <td>{{ $stats['total_customers'] }}</td>
                            <th>Total orders</th>
                            <td>{{ $stats['total_orders'] }}</td>
                            <th>Total items</th>
                            <td>{{ $stats['total_items'] }}</td>
                        </tr>
                    </table>
                @endif
                <hr>
                @if (isset($entries))
                    <h5>Transaction List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Date</th>
                                <th>Amount(&euro;)</th>
                                <th>Status</th>
                                <th>Method</th>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->order_id }}</td>
                                        <td>{{ $entry->customer_name }}</td>
                                        <td>{{$entry->created_at}}</td>
                                        <td>{{ $entry->total_paid }}</td>
                                        <td>
                                            @if ($entry->status == 'Done')
                                                <span class="badge bg-success">Done</span>
                                            @elseif ($entry->status == 'Pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $entry->payment_method }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <i>No records</i>
                @endif
            </div>
        </div>
    </div>

@endsection
@section('js')

@endsection
