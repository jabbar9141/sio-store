@php
    use Illuminate\Support\Facades\Auth;
    $status = Auth::user()->status;
    use App\MyHelpers;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Walk In Order')


@section('content')
    <style>
        .search-container {
            position: relative;
        }

        #product-list {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 0 0 5px 5px;
            max-height: 200px;
            overflow-y: auto;
        }

        .list-group-item {
            cursor: pointer;
        }





        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            /* text-align: right; */
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .invoice-box table tr.vat td {
            background: #eee;
            font-weight: bold;
        }

        .invoice-box table tr.vat td:nth-child(1) {
            border-right: 1px solid #ddd;
        }
    </style>
    @php $address = Auth::user()->address; @endphp

    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Walk-in Order </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create Walk In Order</li>
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
            <div class="col-sm-5">
                <div class="card">
                    <form id="pos_form" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="customer_name">Customer name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name" required>
                            </div>
                            <div class="form-group">
                                <label for="vat_no">VAT No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="vat_no" id="vat_no" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="address" id="address" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping_address">Shipping Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_address" id="shipping_address"
                                    required>
                            </div>
                            <div class="search-container">
                                <div class="form-group">
                                    <label for="product_name">Search Product</label>
                                    <input type="text" class="form-control" id="product_name"
                                        placeholder="search product name or sku">
                                </div>
                                <ul id="product-list" class="list-group"></ul>
                            </div>
                            <hr>
                            <h6>Selected products</h6>
                            <div class="table-respnsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        {{-- <th>variation</th> --}}
                                        <td>*</td>
                                    </thead>
                                    <tbody id="selected-items-list">

                                    </tbody>
                                    <tfoot>
                                        <th>Items: <span id="count_total_tbl"></span></th>
                                        <th></th>
                                        <th>Total: <span id="price_total_tbl"></span></th>
                                        <th>Total Qty: <span id="qty_total_tbl"></span></th>
                                        {{-- <th>Variation</th> --}}
                                        <th></th>
                                    </tfoot>
                                </table>
                                <hr>
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value="CASH">Cash</option>
                                        <option value="POS">POS</option>
                                        <option value="ONLINE">Online</option>
                                    </select>
                                </div>
                                <hr>
                                <input type="hidden" name="total_paid" id="total_paid">
                                <div class="d-flex justify-content-between">
                                    <button type="button" id="submit_btn" class="btn btn-primary"> <span
                                            class="fa fa-save"></span> Submit</button>
                                    <button type="button" onclick="clearInvoiceAndSlip()" class="btn btn-danger"><span
                                            class="fa fa-times"></span> Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="reciept_div3" id="reciept_div3" style="display:none;"></div>
                            <div class="reciept_div" id="reciept_div" style="border: 1px solid black;"></div>
                            <div class="reciept_div2" id="reciept_div2"
                                style="border: 1px solid black; display:none ;  margin: 0 auto;"></div>
                            <button class="btn btn-primary btn-sm" onclick="PrintElem('reciept_div3')"><span
                                    class="fa fa-print"></span>Print</button>
                            <button class="btn btn-primary btn-sm" onclick="PrintElem('reciept_div2')"><span
                                    class="fa fa-print"></span>Print Slip</button>
                            <button class="btn btn-danger btn-sm" onclick="clearInvoiceAndSlip()">Clear</button>

                        </div>
                        <div class="row">
                            @foreach ($products as $pp)
                                @php
                                    if (count($pp->images) > 0) {
                                        $image = $pp->images[0]->product_image;
                                    } else {
                                        $image = $pp->product_thumbnail;
                                    }
                                    $price = $pp->variations[0]->price ?? $pp->product_price;
                                    $only_price = \App\MyHelpers::fromEuro(auth()->user()->currency_id, $price);
                                    $price = \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $price);
                                @endphp

                                <div class="col-sm-4" style="margin-top: 10px;">
                                    <div class="card h-100 m-1 mt-1">
                                        <div class="m-2" onclick="selectProduct(this)"
                                            data-name="[{{ $pp->product_code }}] {{ $pp->product_name }}"
                                            data-price="{{ $price }}" data-img="{{ $image }}"
                                            data-id="{{ $pp->product_id }}">
                                            <img src="/uploads/images/product/{{ $image }}" class="card-img-top"
                                                alt="IMG" style="width: 100%">
                                            <p>[{{ $pp->product_code }}] {{ $pp->product_name }}</p>
                                        </div>
                                        <div class="variationDiv d-flex align-items-center ms-2 me-2 mt-n2">
                                            <b class="productPrice text-nowrap">{{ $price }}</b>
                                            <select name="variation_id" class="form-control ms-2 productVariation"
                                                id="productVariation{{ $pp->product_id }}">
                                                @foreach ($pp->variations as $variation)
                                                    <option value="{{ $variation->id }}" selected
                                                        data-variationquantity="{{ $variation->product_quantity }}"
                                                        data-variationprice="{{ $variation->price }}">
                                                        @if (!empty($variaton->color_name))
                                                            {{ $variation->color_name }}
                                                        @elseif (!empty($variaton->size_name))
                                                            {{ $variation->color_name }}
                                                        @elseif (!empty($variaton->width) || !empty($variaton->height) || !empty($variaton->length) || !empty($variaton->weight))
                                                            W: {{ $variation->width }}; H: {{ $variation->height }};
                                                            L: {{ $variation->length }}
                                                            ; W: {{ $variation->weight }} Kg
                                                        @else
                                                            Default
                                                        @endif

                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <br>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"
        integrity="sha256-UuAyU0w/mJdq2Vy4wguvgO0MyD1CWQYCqM8dsW4uIu0=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#product_name').on('keyup', function() {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: "{{ route('walk-in-order.product.search') }}",
                        type: "GET",
                        data: {
                            'query': query
                        },
                        success: function(data) {
                            $('#product-list').empty();
                            if (data.length > 0) {
                                data.forEach(function(product) {
                                    $('#product-list').append(
                                        `<li
                                        onclick="selectProduct(this)"
                                        class="list-group-item"
                                        data-name="[ ${product.product_code} ]  ${product.product_name} "
                                        data-price="${product.product_price}"
                                        data-img="${product.images[0].product_image}"
                                        data-id="${product.product_id}"
                                        >
                                        <img style="width:50px;" src = "/uploads/images/product/${
                                        product.product_thumbnail}">  [${
                                        product.product_code}] ${product
                                        .product_name}</li>`);
                                });
                            } else {
                                $('#product-list').append(
                                    '<li class="list-group-item">No results found</li>');
                            }
                        }
                    });
                } else {
                    $('#product-list').empty();
                }
            });

            $(document).on('change', '.productVariation', function() {
                let product_price = $(this).find('option:selected').data('variationprice');
                let parent = $(this).parent();
                parent.find('.productPrice').html(product_price);
            });

        });
    </script>
    <script>
        let userAddress = {!! json_encode($address) !!};

        function selectProduct(obj) {
            let list = $('#selected-items-list');
            let product_name = $(obj).data('name');
            let p_price = $(obj).data('price');
            let product_img = $(obj).data('img');
            let product_id = $(obj).data('id');
            let mainParent = $(obj).parent();
            let variationDiv = mainParent.find('.variationDiv');
            let variation_id = variationDiv.find('.productVariation').val();
            let maxQuantity = variationDiv.find('.productVariation option:selected').data('variationquantity');
            let product_price = variationDiv.find('.productVariation option:selected').data('variationprice');

            let existing_product = $(list).find('#' + product_id);

            if (existing_product.length > 0) {
                let quantity = $(existing_product).find('.qty-field').val();
                $(existing_product).find('.qty-field').val(parseInt(quantity) + 1);
                // let var_price = parseFloat(product_price) + parseFloat(variationDiv.find(
                //     '.productVariation option:selected').data('variationprice'));

                // let text = $(existing_product).find('.price_td').text().replace(/[^0-9.-]+/g, '');

                // let new_price = parseFloat(text);
                // if (!isNaN(new_price)) {
                //     $(existing_product).find('.price_td').text(new_price + new_price);
                // }
            } else {

                $('#product-list').html('');

                let mar = `
                    <tr id="${product_id}">
                        <td>
                            ${product_name}
                        </td>
                        <td>
                            <img src = "/uploads/images/product/${product_img}" style="width:50px">
                        </td>
                        <td class="price_td">
                            ${p_price}
                            <input type="hidden" name="prices[]" value = "${product_price}" class="form-control price-field">
                            <input type="hidden" name="products[]" value = "${product_id}" class="form-control product-field">
                        </td>
                        <td>
                            <input type="number" name="qty[]" min=1 max=${maxQuantity} step="1" value = "1" onkeyup="calculateTotals()" class="form-control qty-field">
                        </td>

                            <input type="hidden" name="variations[]" value = ${variation_id} class="form-control variation-field" readonly>

                        <td>
                            <button class = "btn btn-sm btn-danger" onclick=removeRow(this)>x</button>
                        </td>
                    </tr>
                `;
                list.append(mar);
            }

            calculateTotals();
        }

        function removeRow(obj) {
            $(obj).closest('tr').remove();
            calculateTotals();
        }

        function resetForm() {
            $('#pos_form')[0].reset();
            $('#selected-items-list').empty();
            calculateTotals();
        }


        // Serialize form and submit via AJAX
        let first_save = true;
        $('#submit_btn').on('click', function() {
            calculateTotals();
            if (document.getElementById('pos_form')[0].checkValidity() && first_save) {
                let formData = $('#pos_form').serialize();
                $.ajax({
                    url: "{{ route('walk-in-order.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        // Handle the response from the server
                        // console.log(response);
                        // alert('Form submitted successfully!');
                        if (response.success) {
                            first_save = false;
                            $('#submit_btn').attr('disabled', true);
                            generateReciept('reciept_div', response);
                            generateReciept2('reciept_div2', response);
                            generateMainReciept(response);

                            resetForm();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order saved, proceed to print reciept',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Warning!',
                                text: response.message ?? 'Something went wrong',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(error) {
                        // Handle any errors
                        console.log(error.responseJSON.message);
                        Swal.fire({
                            title: 'Error!',
                            text: error.responseJSON.message || error.responseJSON.error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });

        $(document).on('change', 'input[name="qty[]"]', function() {
            calculateTotals();
        })

        // Calculate totals
        function calculateTotals() {
            let total_price = 0,
                total_price_view = 0;

            $('.price_td').each(function() {
                let text = $(this).text().replace(/[^0-9.-]+/g, '');
                let price = parseFloat(text);
                if (!isNaN(price)) {
                    total_price_view += price;
                }
            });

            $('.price-field').each(function() {
                let price = parseFloat($(this).val());
                if (!isNaN(price)) {
                    total_price += price;
                }
            });
            $('#price_total_tbl').text(total_price_view.toFixed(2));
            $('#total_paid').val(total_price.toFixed(2));

            let total_qty = 0;
            $('.qty-field').each(function() {
                let qty = parseFloat($(this).val());
                if (!isNaN(qty)) {
                    total_qty += qty;
                }
            });
            $('#qty_total_tbl').text(total_qty);

            let total_product_count = 0;
            $('.product-field').each(function() {
                total_product_count += 1;
            });
            $('#count_total_tbl').text(total_product_count);
        }

        function generateReciept(div, order) {

            let mar = `
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 15px 20px;border-bottom: 1px solid #e4e4e44a;">
					<tbody>
						<tr>
							<td>
								<a href="../index.html" style="display: block; text-align: center;">
								<img src="	https://siostore.eu/backend_assets/images/siostore_logo.png" style="width: 150px;
									object-fit: contain;
									vertical-align: middle;
									" class="main-logo">
								</a>
							</td>
						</tr>
					</tbody>
				</table>
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 15px 20px;    ">
					<tbody>
						<tr>
							<td style="vertical-align: top;">
								<p style="margin-bottom: 4px; margin-top: 0px;">Order ID: <strong>${order.order.order_id}</strong></p>
								<p style="margin-bottom: 4px; margin-top: 4px;">Customer Name:<strong>${order.order.customer_name}</strong></p>
								<p style="margin-bottom: 4px; margin-top: 4px;">Vendor :<strong>${order.vendor.shop_name}</strong></p>
								<p style="margin-bottom: 4px; margin-top: 4px;">Order Date :<strong>${new Date(order.order.created_at).toLocaleString()}</strong></p>
								<p style="margin-bottom: 4px; margin-top: 4px;">Payment Method :<strong>${order.order.payment_method}</strong></p>

							</td>

						</tr>
					</tbody>
				</table>

                <table class="table table-bordered" style="width:100%;">
                  <thead>
						<tr>
							<th style="    text-align: left;
								font-size: 16px;
								color: #686868;     margin-bottom: 20px;
								display: block;
								">Item Name</th>
							<th style="    text-align: left;
								font-size: 16px;
								color: #686868; width: 35% ; vertical-align: top;">Product Code</th>
							<th style="    text-align: left;
								font-size: 16px;
								color: #686868; vertical-align: top;">Quantity</th>
							<th style="    text-align: left;
								font-size: 16px;
								color: #686868; vertical-align: top;">Price</th>

						</tr>
					</thead>

                    <tbody>
                        ${order.order.items.map(item => `
                                                                                                                                                    <tr>
                                                                                                                                                        <td>${item.product.product_name}</td>
                                                                                                                                                        <td>${item.product.product_code}</td>
                                                                                                                                                        <td>${item.qty}</td>
                                                                                                                                                        <td>&euro; ${item.price.toFixed(2)}</td>
                                                                                                                                                    </tr>
                                                                                                                                                `).join('')}
                    </tbody>
                </table>
                <p><strong>Total Paid:</strong> &euro; ${order.order.total_paid.toFixed(2)}</p>
            `;


            console.log(div);
            $('.' + div).html(order.view);

        }

        function generateMainReciept(response) {
            let mar = `
                <div class="invoice-box" style="font-family: Arial, sans-serif; margin: 0; padding: 0; width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; color: #555;">
        <table style="width: 100%; line-height: inherit; text-align: left; border-collapse: collapse;">
            <tr class="top">
                <td colspan="7" style="padding-bottom: 20px;">
                    <table style="width: 100%;">
                        <tr>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="7" style="padding-bottom: 40px;">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <strong>SUPPLIER</strong><br>
                                ${response.user.name}.<br>
                                Phone No.: ${response.user.phone_number}<br>
                                Email: ${response.user.email}<br>
                                ${response.user.address}IT<br>

                            </td>
                            <td>
                                <strong>CUSTOMER</strong><br>
                                 ${response.order.customer_name}<br>
                                VAT no.:  ${response.order.vat_no}<br>
                                 ${response.order.address}<br>
                                Order Id : ${response.order.order_id}

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;">
                <td style="width: 5%;">No</td>
                <td style="width: 40%;">Name</td>
                <td style="width: 25%;">Product Code</td>
                <td style="width: 10%;">Quantity</td>
                <td style="width: 20%;margin-left: 5px;">Amount</td>
            </tr>
                ${response.order.items.map((item, index) => `<tr class="item" style="border-bottom: 1px solid #eee;">
                                                                                                                                    <td>${ index + 1 }</td>
                                                                                                                                    <td>${item.product.product_name}</td>
                                                                                                                                    <td>${item.product.product_code}</td>
                                                                                                                                    <td>${item.qty}</td>
                                                                                                                                    <td>${item.price.toFixed(2)} €</td>
                                                                                                                                </tr>`).join('')}

        </table>
        <table style="width: 100%; margin-top: 50px;">
            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;">
                <td colspan="3">PAYMENT METHOD</td>
                <td colspan="4">SHIPPING ADDRESS</td>
            </tr>
            <tr class="details" style="padding-bottom: 20px;">
                <td>${response.order.payment_method}</td>
                <td></td>
                <td></td>
                <td colspan="4">${response.order.shipping_address}</td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 50px;">
            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;">
                <td colspan="5">INVOICE CALCULATION</td>
                <td colspan="2">CALCULATION</td>
            </tr>
            ${response.order.items.map(item => `
                                                                                                                                 <tr class="item" style="border-bottom: 1px solid #eee;">
                                                                                                                                    <td colspan="5">Amount of products or services</td>
                                                                                                                                    <td colspan="2">${item.price.toFixed(2)} €</td>
                                                                                                                                </tr>
                                                                                                                                `).join('')}

            <tr class="total" style="border-top: 2px solid #eee; font-weight: bold;">
                <td colspan="5">Net payable</td>
                <td colspan="2"><strong>${response.order.total_paid.toFixed(2)} €</strong></td>
            </tr>
        </table>
        <div id="barcode" style="display: block; margin: 20px auto;"></div>
    </div>
            `;
            $('#reciept_div3').html(response.print_view);
        }

        function generateReciept2(div, order) {
            let total = 0;

            let mar = `
        <div style="font-family: Arial, sans-serif; margin: 5px;">
            <div style="max-width: 900px; margin: auto; border: 1px solid #000; padding: 10px;">
                <div style="margin-bottom: 20px;">
                    <h2 style="margin: 0;text-align: center;">${order.user.name}</h2>
                    <div>
                        <p style="margin: 0;white-space: nowrap;">Data: ${new Date(order.order.created_at).toLocaleString()}</p>
                        <p style="margin: 0;white-space: nowrap;"> Indirizzo: ${order.user.address}</p>
                        <p style="margin: 0;white-space: nowrap;">E-mail: ${order.user.email}</p>
                        <p style="margin: 0;white-space: nowrap;">Order ID: ${order.order.order_id}</p>
                    </div>
                </div>
                <div style="width: 100%; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        ${order.order.items.map(item => `
                                                                                                                                            <tr>
                                                                                                                                                <td style="padding: 5px;">${item.product.product_name}</td>
                                                                                                                                            </tr>
                                                                                                                                            <tr>
                                                                                                                                                <td style="padding: 5px;">${item.qty} x ${item.price.toFixed(2)}</td>
                                                                                                                                                <td style="padding: 5px; text-align: right;">${(item.qty * item.price).toFixed(2)}</td>
                                                                                                                                            </tr>
                                                                                                                                            ${total += item.qty * item.price}
                                                                                                                                        `).join('')}
                    </table>
                </div>
                <div style="width: 100%; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="text-align: left; padding: 5px; border-bottom: 1px solid #000;">Total</th>
                            <td style="padding: 5px; text-align: right; border-bottom: 1px solid #000;">EUR ${total.toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
                <div style="text-align: center;">
                    <p style="margin: 0;">Pagato con: somma: Modificare:</p>
                    <p style="margin: 0;">Cash: 53.00: 0.00</p>
                    <div style="text-align: center; margin-top: 20px;">
                        <svg id="barcode" style="width: 100px; height: 100px;"></svg><br>
                    </div>
                </div>
            </div>
        </div>
    `;
            $('.' + div).html(order.slip_view);
            JsBarcode("#barcode", order.order.slip_serial_no, {
                format: "CODE128"
            });
        }

        function clearInvoiceAndSlip() {
            // document.getElementById("reciept_div3").innerHTML = '';
            // document.getElementById("reciept_div").innerHTML = '';
            // document.getElementById("reciept_div2").innerHTML = '';
            window.location.reload();
        }

        function PrintElem(elemId) {
            console.log(elemId);

            var printContents = document.getElementById(elemId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>



@endsection
