@extends('backend.layouts.app')
@section('PageTitle', 'Country List')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Country</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Weight Details
                        ({{ $country->name ? '(' . $country->name . ')' : '' }})</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    {{-- <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin-shipping-cost', ['id' => $country->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="multiple_cities" value="yes">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="cities">City</label>
                        <select multiple name="cities[]" id="cities" class="form-control select2" required>
                            <option value="all" selected>All Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="percentage">Shipping Percentage <small class="text-danger">*</small></label>
                        <input type="number" name="percentage" id="all_shipping_percentage" class="form-control"
                            min="0" max="100" step="0.01" required>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-body row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table" id="countries__weight_table">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Weight</th>
                            <th>Cost</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Body -->
    <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
    <div class="modal fade" id="shipping_cost_modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Modal title
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin-shipping-cost-update') }}" id="city_shipping_cost_form"
                        method="POST">
                        @csrf
                        <input type="hidden" name="shipping_id">
                        <div class="col-12 mb-3">
                            <label for="percentage">Shipping Cost</label>
                            <input type="number" name="cost" id="shipping_cost" class="form-control"
                                min="0" step="0.01" required>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Optional: Place to the bottom of scripts -->

@endsection

@section('js')
    <script>
        const options = {};

        // Initialize the modal with options
        // const myModal = new bootstrap.Modal(
        //     document.getElementById("shipping_cost_modal"),
        //     options
        // );

        function addWeightCost(shippingCostId) {
            $.ajax({
                type: "get",
                url: "{{ route('admin-weight-cost', ['id' => ':id']) }}".replace(':id', shippingCostId),
                data: "",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('input[name="shipping_id"]').val(response.shippingId);
                        $('#shipping_cost').val(response.shippingCost ?? 0);
                       $('#shipping_cost_modal').modal('show');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to get shipping cost',
                            showDenyButton: false,
                        });
                    }
                }
            });
        }


        $('#countries__weight_table').DataTable({
            "dom": 'Bfrtip',
            "iDisplayLength": 50,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin-weight-list', ['country_id' => $country->id]) }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },
               
                {
                    "data": "weight"
                },
                {
                    "data": "cost"
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
