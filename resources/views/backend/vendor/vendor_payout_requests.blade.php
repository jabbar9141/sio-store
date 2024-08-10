@php
    use Illuminate\Support\Facades\Auth;
    $status = Auth::user()->status;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Payout Requests')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Payout Requests</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All Payout Requests</li>
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
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="requests_tbl">
                        <thead>
                            <th>#</th>
                            <th>Request</th>
                            <th>Response</th>
                            <th>Status</th>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#requests_tbl').DataTable({
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
                "url": "{{ route('vendor-payout-list-data') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "request"
                },
                {
                    "data": "response"
                },
                {
                    "data": "status"
                },
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
