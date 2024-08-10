@php
    use Illuminate\Support\Facades\Auth;
    $status = Auth::user()->status;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Orders')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Orders</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Order Items</li>
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
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>Total Order Count</th>
                                <td>{{ $items_count }}</td>
                                <th>Total Gross Income(&euro;)</th>
                                <th>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id,$total_income) }}</th>
                            </tr>
                            <tr>
                                <th>Total Cost Price</th>
                                <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id,$total_cost_price) }}</td>
                                <th>Total Profit</th>
                                <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id,$total_profit) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table" id="orders_tbl">
                                <thead>
                                    <th>#</th>
                                    <th>Order Id</th>
                                    <th>Order Date</th>
                                    <th>Customer</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#orders_tbl').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 50,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "buttons": ['pageLength', 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('vendor-vendorOrdersData') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "order_id"
                },
                {
                    "data": "created_at"
                },
                {
                    "data": "customer"
                },
                {
                    "data": "price"
                },
                {
                    "data": "status"
                },
                {
                    "data": "action"
                }
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
    </script>
@endsection
