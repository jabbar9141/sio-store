@php
    use App\Http\Controllers\ProductController;
    use Illuminate\Support\Facades\Auth;
    $role = Auth::user()->role;
@endphp

@extends('backend.layouts.app')
@section('css')


    <style>
        .picker .pc-select .pc-trigger {
            cursor: pointer;
            margin-right: 0;
            width: 100%;
            height: 35px;
            border-radius: 6px;
            /* text-align: center; */
            display: flex;
            align-items: center;
        }
    </style>
    </style>
@endsection
@section('PageTitle', 'Products')
@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Products</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route($role . '-profile') }}"><i
                                class="bx
                    bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="d-flex ms-auto justify-content-between" style="margin-bottom: 20px">
                    @if (Auth::user()->role == 'vendor')
                        <a href="add_product" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                            <i class="bx bxs-plus-square"></i>Add New Product</a>
                        {{-- {{ route('vendor-koffFeedProducts') }} --}}
                        {{-- @if (Auth::user()->id == 38) --}}

                        <div>

                            <a href="#" id="updateProducts" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                                Edit Brand & Category
                            </a>
                            {{-- <a href="#" id="koffFeedProducts" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                            <i class="bx bxs-plus-square"></i>Add New Product Fom Koff</a>/ --}}
                            <a type="button" class="btn btn-primary radius-30 mt-2 mt-lg-0" data-bs-toggle="modal"
                                data-bs-target="#uploadCsvModal" >
                                Import Csv
                            </a>
                            
                             <a type="button" class="btn btn-danger radius-30 mt-2 mt-lg-0" id="bulkDelete">
                                Delete All
                            </a>

                        </div>

                </div>
                @endif
                <table id="data_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Publish Status</th>
                            <th>Admin Approval Status</th>
                            <th>View Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailsModalLabel">Product details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="updateForm" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal --}}

    {{-- Mass upload Csv --}}
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Csv File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uplaodCsvForm">
                        <div class="mt-3 mb-3">
                            <label for="inputProductLongDescription" class="form-label text-danger" id="contantCount"></label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 mb-2" style="margin-left: 40%">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Save
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center align-center" id="exampleModalLabel2">Select Product To update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="formsInput">
                    <!-- Form content will be appended here -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
@endsection


@section('js')




    <script>
        $('#data_table').DataTable({
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
                "url": "{{ route('vendor-product-list') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": "DT_RowIndex"
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "product_thumbnail"
                },
                {
                    "data": "product_code"
                },
                {
                    "data": "product_status"
                },
                {
                    "data": "admin_approved"
                },
                {
                    "data": "details"
                },
                {
                    "data": "action"
                },
            ],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });

        $('#data_table').on('click', '.btn-see-details', function() {
            let productId = $(this).data('product-id');
            let modal = $('#productDetailsModal');

            $.ajax({
                url: `/product/details/${productId}`,
                method: 'GET',
                success: function(data) {
                    modal.find('.modal-body').html(data);
                    modal.modal('show');
                }
            });
        });

        $('#koffFeedProducts').on('click', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('vendor-koffFeedProducts') }}",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Products Uploading In Progress',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something Wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }

                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something Wrong',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                }
            });

        });
        
         $(document).on('click', '#bulkDelete', function() {
            var checkedValues = [];
            $('input.deleteProduct:checked').each(function() {
                checkedValues.push($(this).val());
            });
            if (checkedValues.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('vendor-bulkRemove') }}",
                    data: {
                        product_ids: checkedValues
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Products Deleted Successfully',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something Wrong',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }

                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something Wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }
                });
            }

        });
        
          document.getElementById('csv_file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const text = e.target.result;
                    const lines = text.split(/\r\n|\n/);
                    if (lines.length > 200) {
                       $('#contantCount').text('File Not Contains More than 200 Records');
                        document.getElementById('csv_file').value = ''; 
                    } else {
                     $('#contantCount').text('');   
                    }
                };
                reader.readAsText(file);
            }
        });

        $('#uplaodCsvForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            var $submitButton = $('button[type="submit"]');
            var $spinner = $submitButton.find('.spinner-border');

            // Show spinner and disable button
            $spinner.removeClass('d-none');
            $submitButton.prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "{{ route('vendor-sioBuyProducts') }}",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        // Hide spinner and re-enable button
                        $spinner.addClass('d-none');
                        $submitButton.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Products Uploaded Successfully',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        // Hide spinner and re-enable button
                        $spinner.addClass('d-none');
                        $submitButton.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Something Wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }
                },
                error: function(response) {
                    // Hide spinner and re-enable button
                    $spinner.addClass('d-none');
                    $submitButton.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Something Wrong',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                }
            });
        });


        //  filter

         $('#updateProducts').on('click', function(event) {
            event.preventDefault();

            $.ajax({
                type: "GET",
                url: "{{ route('vendor-updateProducts') }}",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        console.log('done');
                        $('#formsInput').empty();
                        $('#formsInput').append(response.form);
                        $("#staticBackdrop2").modal('show');

                        $(document).on('input', '#start, #end', function() {
                            var startValue = $('#start').val();
                            var endValue = $('#end').val();

                            if (parseInt(startValue) > parseInt(endValue)) {
                                $('#product_error').text(
                                    'End value must be greater than start value');
                            } else {
                                $('#product_error').text('');
                            }
                        });

                        $('#updateFormBrand').click(function(e) {
                            start = $('#start').val();
                            end = $('#end').val();
                            category_id = $('#inputCategory').val();
                            brand_id = $('#inputBrand').val();
                            returns_allowed = $('input[name="returns_allowed"]').val();
                            product_status = $('input[name="product_status"]').val();
                            hot_deal = $('input[name="hot_deal"]').val();
                            featured_product = $('input[name="featured_product"]').val();
                            special_offer = $('input[name="special_offer"]').val();
                            special_deal = $('input[name="special_deal"]').val();
                            if (parseInt(start) > parseInt(end)) {
                                $('#product_error').text(
                                    'End value must be greater than start value');
                            } else {

                                $.ajax({
                                    type: "GET",
                                    url: "{{ route('vendor-updateProductsBrandCategory') }}",
                                    data: {
                                        'start': start,
                                        'end': end,
                                        'category_id': category_id,
                                        'brand_id': brand_id,
                                        'returns_allowed': returns_allowed,
                                        'product_status': product_status,
                                        'hot_deal': hot_deal,
                                        'featured_product': featured_product,
                                        'special_offer': special_offer,
                                        'special_deal': special_deal,
                                    },
                                    dataType: "json",
                                    success: function(response) {
                                        if (response.success) {
                                            $("#staticBackdrop2").modal('hide');
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.msg,
                                                confirmButtonText: 'OK'
                                            });

                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Something went wrong',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    },
                                    error: function(response) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Something went wrong',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });

                            }


                        });


                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // $('#updateFormBrand').click(function(event) {
        //     alert('sdafs')
        //     event.preventDefault();

        //     $data= $('#updateProducts').serializeArray(),
        //     console.log($data);
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('vendor-updateProductsBrandCategory') }}",
        //         data: $('#updateProducts').serializeArray(),
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         dataType: "json",
        //         success: function(response) {
        //             if (response.success) {
        //                 console.log('done');
        //                 $('#formsInput').empty();
        //                 $('#formsInput').append(response.form);
        //                 $("#staticBackdrop2").modal('show');
        //                 $('#inputProduct').picker();
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Something went wrong',
        //                     confirmButtonText: 'OK'
        //                 });
        //                 displayValidationErrors(response.errors);
        //             }
        //         },
        //         error: function(response) {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Something went wrong',
        //                 confirmButtonText: 'OK'
        //             });
        //         }
        //     });
        // });

        $('#sioBuyProducts').on('click', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('vendor-sioBuyProducts') }}",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Products Uploading In Progress',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something Wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }

                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Something Wrong',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                }
            });

        });

        function deleteProduct(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                denyButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('vendor-product-remove', ['id' => ':id']) }}".replace(":id", id),
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Product deleted successfully',
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }

                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something Wrong',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    });
                };
            });

        }
    </script>
@endsection
