@extends('backend.layouts.app')
@section('PageTitle', 'All Orders')


@section('content')
<!--breadcrumb -->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Orders</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Order Items List</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table" id="orders_tbl">
                        <thead>
                            <th>#</th>
                            <th>Order Id</th>
                            <th>Order Date</th>
                            <th>Customer</th>
                            <th>Vendor</th>
                            <th>Price(&euro;)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                    </table>
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
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin-allOrdersData') }}",
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
                    "data": "vendor"
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
