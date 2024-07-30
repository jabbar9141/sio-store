@if ($type == 'receipt_view')
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="padding: 15px 20px;border-bottom: 1px solid #e4e4e44a;">
        <tbody>
            <tr>
                <td>
                    <a href="../index.html" style="display: block; text-align: center;">
                        <img src="https://siostore.eu/backend_assets/images/siostore_logo.png"
                            style="width: 150px; object-fit: contain; vertical-align: middle;" class="main-logo">
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 15px 20px;    ">
        <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <p style="margin-bottom: 4px; margin-top: 0px;">Order ID:
                        <strong>{{ $order->order_id ?? '-' }}</strong>
                    </p>
                    <p style="margin-bottom: 4px; margin-top: 4px;">Customer
                        Name:<strong>{{ $order->customer_name ?? '-' }}</strong></p>
                    <p style="margin-bottom: 4px; margin-top: 4px;">Vendor
                        :<strong>{{ $vendor->shop_name ?? '-' }}</strong>
                    </p>
                    <p style="margin-bottom: 4px; margin-top: 4px;">Order Date
                        :<strong>{{ $order->created_at ?? '-' }}</strong>
                    </p>
                    <p style="margin-bottom: 4px; margin-top: 4px;">Payment Method
                        :<strong>{{ $order->payment_method ?? '-' }}</strong></p>

                </td>

            </tr>
        </tbody>
    </table>

    <table class="table table-bordered" style="width:100%;">
        <thead>
            <tr>
                <th
                    style="    text-align: left;
                font-size: 16px;
                color: #686868;     margin-bottom: 20px;
                display: block;
                ">
                    Item Name</th>
                <th
                    style="    text-align: left;
                font-size: 16px;
                color: #686868; width: 35% ; vertical-align: top;">
                    Product Code</th>
                <th
                    style="    text-align: left;
                font-size: 16px;
                color: #686868; vertical-align: top;">
                    Quantity</th>
                <th
                    style="    text-align: left;
                font-size: 16px;
                color: #686868; vertical-align: top;">
                    Price</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->product_name ?? '-' }}</td>
                    <td>{{ $item->product->product_code ?? '-' }}</td>
                    <td>{{ $item->qty ?? '-' }}</td>
                    <td>{{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $item->price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Total Paid:</strong>{{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $order->total_paid) }}</p>
@elseif ($type == 'print')
    <div class="invoice-box"
        style="font-family: Arial, sans-serif; margin: 0; padding: 0; width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; color: #555;">
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
                                {{ $user->name }}.<br>
                                Phone No.: {{ $user->phone_number }}<br>
                                Email: {{ $user->email }}<br>
                                {{ $user->address }}IT<br>

                            </td>
                            <td>
                                <strong>CUSTOMER</strong><br>
                                {{ $order->customer_name }}<br>
                                VAT no.: {{ $order->vat_no }}<br>
                                {{ $order->address }}<br>
                                Order Id : {{ $order->order_id }}

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
            @foreach ($order->items as $item)
                <tr class="item" style="border-bottom: 1px solid #eee;">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product->product_name }}</td>
                    <td>{{ $item->product->product_code }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $item->price) }}</td>
                </tr>
            @endforeach

        </table>
        <table style="width: 100%; margin-top: 50px;">
            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;">
                <td colspan="3">PAYMENT METHOD</td>
                <td colspan="4">SHIPPING ADDRESS</td>
            </tr>
            <tr class="details" style="padding-bottom: 20px;">
                <td>{{ $order->payment_method ?? '-' }}</td>
                <td></td>
                <td></td>
                <td colspan="4">{{ $order->shipping_address ?? '-' }}</td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 50px;">
            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;">
                <td colspan="5">INVOICE CALCULATION</td>
                <td colspan="2">CALCULATION</td>
            </tr>
            @foreach ($order->items as $item)
                <tr class="item" style="border-bottom: 1px solid #eee;">
                    <td colspan="5">Amount of products or services</td>
                    <td colspan="2">{{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $item->price) }}</td>
                </tr>
            @endforeach

            <tr class="total" style="border-top: 2px solid #eee; font-weight: bold;">
                <td colspan="5">Net payable</td>
                <td colspan="2">
                    <strong>{{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $order->total_paid) }}</strong>
                </td>
            </tr>
        </table>
        <div id="barcode" style="display: block; margin: 20px auto;"></div>
    </div>
@elseif ($type == 'slip_view')
    <div style="font-family: Arial, sans-serif; margin: 5px;">
        <div style="max-width: 900px; margin: auto; border: 1px solid #000; padding: 10px;">
            <div style="margin-bottom: 20px;">
                <h2 style="margin: 0;text-align: center;">{{ $user->name ?? '-' }}</h2>
                <div>
                    <p style="margin: 0;white-space: nowrap;">Data: ${new Date(order.order.created_at).toLocaleString()}
                    </p>
                    <p style="margin: 0;white-space: nowrap;"> Indirizzo: {{ $user->address ?? '-' }}</p>
                    <p style="margin: 0;white-space: nowrap;">E-mail: {{ $user->email ?? '-' }}</p>
                    <p style="margin: 0;white-space: nowrap;">Order ID: {{ $order->order_id ?? '-' }}</p>
                </div>
            </div>
            <div style="width: 100%; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding: 5px;">{{ $item->product->product_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">{{ $item->qty ?? '-' }} x
                                {{ \App\MyHelpers::fromEuroView(Auth::user()->currency_id, $item->price) }}</td>
                            <td style="padding: 5px; text-align: right;">
                                {{ $item->qty * \App\MyHelpers::fromEuro(Auth::user()->currency_id, $item->price) }} {{ Auth::user()->currency->currency_symbol ?? '' }}
                            </td>
                        </tr>
                        {{ $total += $item->qty * \App\MyHelpers::fromEuro(Auth::user()->currency_id, $item->price) }}
                    @endforeach
                </table>
            </div>
            <div style="width: 100%; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="text-align: left; padding: 5px; border-bottom: 1px solid #000;">Total</th>
                        <td style="padding: 5px; text-align: right; border-bottom: 1px solid #000;">
                            {{ $total }} {{ Auth::user()->currency->currency_symbol ?? '' }}</td>
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
@endif
