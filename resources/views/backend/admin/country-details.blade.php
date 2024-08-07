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
                    <li class="breadcrumb-item active" aria-current="page">City Details ({{ $country->name }})</li>
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
                            <th>Shipping Cost (&euro;)</th>
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
                    <form action="{{ route('admin-shipping-cost', ['id' => $country->id]) }}" id="city_shipping_cost_form"
                        method="POST">
                        @csrf
                        <input type="hidden" name="city_id">
                        <div class="col-12 mb-3">
                            <label for="cost">Shipping Cost</label>
                            <input type="number" name="cost" id="shipping_cost" class="form-control" min="0"
                                step="0.01" required>
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
        const myModal = new bootstrap.Modal(
            document.getElementById("shipping_cost_modal"),
            options
        );

        function addShippingCost(city_id) {
            $.ajax({
                type: "get",
                url: "{{ route('admin-city-cost', ['id' => ':id']) }}".replace(':id', city_id),
                data: "",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        $('input[name="city_id"]').val(city_id);
                        $('#shipping_cost').val(response.shipping_cost ?? 0);
                        // $('#shipping_cost_modal').modal('show');
                        myModal.show();

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

        $('#city_shipping_cost_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Shipping Cost Updated Successfully',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to update shipping cost',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to update shipping cost',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    })
                }
            })
        })

        $('.select2').select2();

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
                "url": "{{ route('admin-city-list', ['country_id' => $country->id]) }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "name"
                },
                {
                    "data": "shipping_cost"
                },
                // {
                //     "data": "customer"
                // },
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
