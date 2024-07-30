@extends('backend.layouts.app')
@section('PageTitle', 'All Orders')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Currency</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Currency List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    {{-- modal --}}


    {{-- end mo --}}


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="ms-auto" style="margin-bottom: 20px">
                    <a href="#" class="btn btn-primary rounded-pill m-2" data-toggle="modal"
                        data-target="#currencyModal" id="currencyMode"><i class="bx bxs-plus-square"></i>Add New</a>

                    <table id="data_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Country Code</th>
                                <th>Currency Symbol</th>
                                <th>Exchange Rate</th>

                                <th> Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($currencies as $currency)
                                <tr>
                                    <td>{{ $currency->country }}</td>
                                    <td>{{ $currency->country_code }}</td>
                                    <td>{{ $currency->currency_symbol }}</td>
                                    <td>{{ $currency->currency_rate }}</td>

                                    <td>
                                        <div class="d-flex order-actions">
                                            <a href="javascript:void(0);" class="" data-bs-toggle="modal"
                                                data-bs-target="#exampleFullScreenModal"
                                                onclick="setCurrencyDataa({{ $currency->id }}, '{{ $currency->main_currency }}', '{{ $currency->country }}', '{{ $currency->country_code }}', '{{ $currency->currency_symbol }}', '{{ $currency->currency_rate }}')">
                                                <i class='bx bxs-edit'></i>
                                            </a>



                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleFullScreenModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Edit Currency
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="currencyFormEdit"
                                                                onsubmit="return editCurrencyForm(event)">
                                                                <div class="row">
                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                                                        <label for="main_currency" class="form-label">Main
                                                                            Currency</label>
                                                                        <input type="text" class="form-control"
                                                                            id="main_currency" name="main_currency"
                                                                            placeholder="" required
                                                                            style="border-radius: 10px;" readonly>
                                                                    </div>
                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                                                        <label for="country"
                                                                            class="form-label">Country</label>
                                                                        <input type="text" class="form-control"
                                                                            id="country" name="country" placeholder=""
                                                                            required style="border-radius: 10px;">
                                                                    </div>
                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                                                        <label for="country_code" class="form-label">Country
                                                                            Code</label>
                                                                        <input type="text" class="form-control"
                                                                            id="country_code" name="country_code"
                                                                            placeholder="" required
                                                                            style="border-radius: 10px;">
                                                                    </div>
                                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                                                        <label for="currency_symbol"
                                                                            class="form-label">Currency Symbol</label>
                                                                        <input type="text" class="form-control"
                                                                            id="currency_symbol" name="currency_symbol"
                                                                            placeholder="" required
                                                                            style="border-radius: 10px;">
                                                                    </div>
                                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                                        <label for="exchange_rate"
                                                                            class="form-label">Exchange Rate</label>
                                                                        <input type="number" class="form-control"
                                                                            id="exchange_rate" name="exchange_rate"
                                                                            placeholder="" required
                                                                            style="border-radius: 10px;">
                                                                    </div>
                                                                    <input type="hidden" id="currency_id"
                                                                        name="currency_id">
                                                                </div>
                                                                <div class="text-center">
                                                                    <button type="submit" class="btn btn-primary"
                                                                        style="border-radius: 10px"
                                                                        id="saveReview">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>















                                            <a href="javascript:void(0);" class="ms-3" data-bs-toggle="modal"
                                                data-bs-target="#exampleDangerModal"
                                                onclick="setCurrencyId({{ $currency->id }})">
                                                <i class='bx bxs-trash'></i>
                                            </a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleDangerModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content bg-danger">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-white">Sure?</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button id="confirmDelete"
                                                                class="btn btn-dark">Confirm</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="currency_id">



                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="currencyModal" tabindex="-1" role="dialog" aria-labelledby="currencyModal"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Currency</h5>
                        <button type="button" class="btn-close" data-dismiss="modal"
                                                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="currencyForm" onsubmit="return submitCurrencyForm(event)">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <label for="main_currency" class="form-label">Main Currency</label>
                                    <input type="text" class="form-control" id="main_currency" name="main_currency"
                                        placeholder="" value="euro" required style="border-radius: 10px;" readonly>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                        placeholder="" required style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <label for="country_code" class="form-label">Country Code</label>
                                    <input type="text" class="form-control" id="country_code" name="country_code"
                                        placeholder="" required style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                                    <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                    <input type="text" class="form-control" id="currency_symbol"
                                        name="currency_symbol" placeholder="" required style="border-radius: 10px;">
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="exchange_rate" class="form-label">Exchange Rate</label>
                                    <input type="number" class="form-control" id="exchange_rate" name="exchange_rate"
                                        placeholder="" required style="border-radius: 10px;">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary mt-2" style="border-radius: 10px"
                                    id="saveReview">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    @endsection
    @section('js')
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script>
            function submitCurrencyForm(event) {
                event.preventDefault(); // Prevent the default form submission

                var form = $('#currencyForm');

                if (form[0].checkValidity() === false) {
                    // If form is invalid, show native HTML5 validation messages
                    form[0].reportValidity();
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('store_currency') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: form.serialize(), // Serialize the form data
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Curency Added Successfully',
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

                return false;
            }



            function setCurrencyId(id) {
                document.getElementById('currency_id').value = id;
            }

            document.getElementById('confirmDelete').addEventListener('click', function() {
                const id = document.getElementById('currency_id').value;
                deleteCurrency(id);
            });

            function deleteCurrency(id) {
                $.ajax({
                    type: "DELETE",
                    url: "/currencies/" + id,
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Currency deleted successfully',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }


            function setCurrencyDataa(id, mainCurrency, country, countryCode, currencySymbol, exchangeRate) {
                document.getElementById('currency_id').value = id;
                document.getElementById('main_currency').value = mainCurrency;
                document.getElementById('country').value = country;
                document.getElementById('country_code').value = countryCode;
                document.getElementById('currency_symbol').value = currencySymbol;
                document.getElementById('exchange_rate').value = exchangeRate;
            }

            function editCurrencyForm(event) {
                event.preventDefault();
               
                const formData = $('#currencyFormEdit').serialize();

                $.ajax({
                    type: "POST",
                    url: "/currencies/update",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Currency updated successfully',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        </script>
    @endsection
