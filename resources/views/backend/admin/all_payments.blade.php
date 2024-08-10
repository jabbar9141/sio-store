@extends('backend.layouts.app')
@section('PageTitle', 'All Orders')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Payments</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Retail Orders Payment List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
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
                        <div class="col-sm-4">
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
                            <td>{{ $stats['total_amt']/100 }}</td>
                            <th>Total Successful(&euro;)</th>
                            <td>{{ $stats['total_success']/100 }}</td>
                            <th>Total Pending(&euro;)</th>
                            <td>{{ $stats['total_pending']/100 }}</td>
                            <th>Total Cancelled(&euro;)</th>
                            <td>{{ $stats['total_cancelled']/100 }}</td>
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
                                <th>Pay Ref</th>
                                <th>Amount(&euro;)</th>
                                <th>Status</th>
                                <th>Method</th>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->order->order_id }}</td>
                                        <td>{{ $entry->ref }}</td>
                                        <td>{{ $entry->amount / 100 }}</td>
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
