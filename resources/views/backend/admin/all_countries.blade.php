@extends('backend.layouts.app')
@section('PageTitle', 'Country List')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Country</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Country List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
    <div class="card">
        <div class="card-body row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table" id="countries_table">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>ISO 2</th>
                            <th>Shipping Cost(Min-Max) (&euro;)</th>
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
        $('#countries_table').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 50,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin-country-data') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },
                {
                    "data": "iso2"
                },
                {
                    "data": "shipping_cost"
                },
                // {
                //     "data": "vendor"
                // },
                // {
                //     "data": "price"
                // },
                // {
                //     "data": "status"
                // },
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
