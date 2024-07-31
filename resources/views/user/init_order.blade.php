@php
    use App\Models\ProductVariation;
@endphp
@extends('user.layout.app')
@section('page_name', 'Checkout')
@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Checkout</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <div class="container-fluid">
        <form action="{{ route('store.order.submit') }}" method="post">
            @csrf
            <div class="row px-xl-5">
                <div class="col-lg-8">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Billing
                            Address</span></h5>
                    <div class="bg-light p-30 mb-5">

                        <h6>Saved Addresses</h6>
                        @if (isset($addr) && count($addr) > 0)
                            <div class="row">
                                @foreach ($addr as $add)
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body shadow">
                                                <div class="col-4">
                                                    <input type="radio" name="billing_address" class=""
                                                        value="{{ $add->id }}" id="add_{{ $add->id }}"
                                                        onchange="evaluateShipping(this)"
                                                        data-country-iso-2="{{ $add->country }}">
                                                </div>
                                                <div class="col-12">
                                                    <label for="add_{{ $add->id }}">
                                                        <span class="card card-body">
                                                            Name : {{ $add->firstname }} {{ $add->lastname }} <br>
                                                            Email {{ $add->email }}, Phone: {{ $add->phone }} <br>
                                                            <br>
                                                            Address: {{ $add->address1 }}, {{ $add->address2 }}.
                                                            {{ $add->city }}
                                                            {{ $add->zip }} {{ $add->state }}, {{ $add->country }}.
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <i>No saved addresses, create one below</i>
                            <input type="hidden" name="shipping_address" value="">
                            <!-- Button trigger modal -->
                        @endif
                        <hr>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                            Add New Address.
                        </button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Order
                            Total</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="border-bottom">
                            <h6 class="mb-3">Products</h6>
                            {{-- <input type="hidden" id="current_order_id" value="{{ $order->id }}"> --}}


                            @if (null != session('cart') && count(session('cart')) > 0)
                                @php
                                    $session_cart = session('cart');
                                    $cart_total = 0;
                                @endphp
                                @foreach ($session_cart as $it)
                                    @php
                                        $the_product = App\MyHelpers::getProductById($it['product_id']);
                                        $variation =
                                            ProductVariation::find($it['variation_id']) ?? $the_product->variations[0];
                                        if ($variation) {
                                            $price = $variation->price;
                                        } else {
                                            $price = $the_product->product_pricel;
                                        }

                                        $cart_total += $price * $it['qty'];
                                    @endphp
                                    <div class="d-flex justify-content-between">
                                        <p style="width: 75%">{{ $the_product->product_name }}
                                            {{-- ({{ json_encode($it['variations']) }}) --}}
                                            <b>x
                                                {{ $it['qty'] }}</b>
                                        </p>
                                        <p>
                                            {{ App\MyHelpers::fromEuroView(session('currency_id', 0), $price * $it['qty']) }}
                                        </p>
                                        <p class="d-none"><span
                                                class="weight product_weight">{{ ($variation->weight ?? 1) * $it['qty'] }}</span>
                                            Kg</p>
                                    </div>
                                @endforeach
                            @else
                                {{--
                            @if (isset($order) && null != $order->items)
                                @php
                                    $total = 0;
                                    $data = $order->items;
                                @endphp
                                @foreach ($data as $item)
                                    @php
                                        $the_product = App\MyHelpers::getProductById($item->item_id);
                                        $variation =
                                            ProductVariation::find($item?->product_variation_id) ??
                                            $the_product->variations[0];
                                        if ($variation) {
                                            $price = $variation->price;
                                        } else {
                                            $price = $the_product->product_pricel;
                                        }
                                        $total += $price * $item->qty;
                                    @endphp
                                    <div class="d-flex justify-content-between">
                                        <p>{{ $the_product->product_name }} ({{ json_encode($item->variant) }}) <b>x
                                                {{ $item->qty }}</b></p>
                                        <p>
                                            {{ App\MyHelpers::fromEuroView(session('currency_id', 0), $price * $item->qty) }}
                                        </p>
                                    </div>
                                @endforeach
                            @else --}}
                                <h6 class="mb-3">Products</h6>
                                <div class="d-flex justify-content-between">
                                    <p>No products</p>
                                </div>
                            @endif

                            <input type="hidden" id="euro_cart_total" name="euro_cart_total" value="{{ $cart_total }}">

                        </div>
                        <div class="border-bottom pt-3 pb-2">
                            <div class="d-flex justify-content-between mb-3">
                                <h6>Subtotal</h6>
                                <h6><span
                                        id="sub_total_cost">{{ App\MyHelpers::fromEuroView(session('currency_id', 0), $cart_total) }}</span>
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <h6>Shipping</h6>
                                <h6>
                                    <span class="" id="shiping_cost_of_cart">{Please select an address}</span>
                                </h6>
                                <input type="hidden" name="shipping_cost">
                            </div>
                        </div>
                        <div class="pt-2">
                            <div class="d-flex justify-content-between mt-2">
                                <h5>Total</h5>
                                <h5><span id="total_cost">
                                        {{-- {{ App\MyHelpers::fromEuroView(session('currency_id', 0), $cart_total) }} --}}
                                    </span>
                                    <input type="hidden" name="total_amount_to_pay">
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <h5 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Payment</span></h5>
                        <div class="bg-light p-30">
                            <div class="form-group d-flex justify-content-center">
                                {{-- <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="payment" id="stripe"
                                        value="STRIPE" required>
                                    <label class="custom-control-label" for="stripe">Stripe</label>
                                </div> --}}
                                <div class="custom-control custom-radio ">
                                    <input type="radio" class="custom-control-input" name="payment" id="sumup"
                                        value="SUMUP" required checked>
                                    <label class="custom-control-label" for="sumup">SumUp</label>
                                </div>

                                {{-- <div class="custom-control custom-radio mx-4">
                                    <input type="radio" class="custom-control-input" name="payment" id="paypal"
                                        value="PAYPAL" required>
                                    <label class="custom-control-label" for="paypal">PAYPAL</label>
                                </div> --}}

                                {{-- <div class="custom-control custom-radio ">
                                    <input type="radio" class="custom-control-input" name="payment" id="paystack"
                                        value="PAYSTACK" required>
                                    <label class="custom-control-label" for="paystack">PayStack</label>
                                </div> --}}
                            </div>
                            <button class="btn btn-block btn-primary font-weight-bold py-3" id="place_order_btn"
                                disabled>Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <small>Fill the form below to add a new shipping address (all fileds marked * are
                            required)</small>
                        <br>
                        <form action="{{ route('address.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input class="form-control" name="firstname" type="text" placeholder="John"
                                        required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input class="form-control" name="lastname" type="text" placeholder="Doe"
                                        required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>E-mail <span class="text-danger">*</span></label>
                                    <input class="form-control" name="email" type="text"
                                        placeholder="example@email.com" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Mobile No <span class="text-danger">*</span></label>
                                    <input class="form-control" name="phone" type="text" placeholder="+123 456 789"
                                        required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Address Line 1 <span class="text-danger">*</span></label>
                                    <input class="form-control" name="address1" type="text"
                                        placeholder="123 Main Street" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Address Line 2</label>
                                    <input class="form-control" name="address2" type="text"
                                        placeholder="Block 1 Suite 2">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Country <span class="text-danger">*</span></label>
                                    <select name="country" id="country" class="custom-select" required>
                                        <option value="">Select a country...</option>
                                        <option value="AF">Afghanistan</option>
                                        <option value="AX">Åland Islands</option>
                                        <option value="AL">Albania</option>
                                        <option value="DZ">Algeria</option>
                                        <option value="AS">American Samoa</option>
                                        <option value="AD">Andorra</option>
                                        <option value="AO">Angola</option>
                                        <option value="AI">Anguilla</option>
                                        <option value="AQ">Antarctica</option>
                                        <option value="AG">Antigua and Barbuda</option>
                                        <option value="AR">Argentina</option>
                                        <option value="AM">Armenia</option>
                                        <option value="AW">Aruba</option>
                                        <option value="AU">Australia</option>
                                        <option value="AT">Austria</option>
                                        <option value="AZ">Azerbaijan</option>
                                        <option value="BS">Bahamas</option>
                                        <option value="BH">Bahrain</option>
                                        <option value="BD">Bangladesh</option>
                                        <option value="BB">Barbados</option>
                                        <option value="BY">Belarus</option>
                                        <option value="BE">Belgium</option>
                                        <option value="BZ">Belize</option>
                                        <option value="BJ">Benin</option>
                                        <option value="BM">Bermuda</option>
                                        <option value="BT">Bhutan</option>
                                        <option value="BO">Bolivia, Plurinational State of</option>
                                        <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                        <option value="BA">Bosnia and Herzegovina</option>
                                        <option value="BW">Botswana</option>
                                        <option value="BV">Bouvet Island</option>
                                        <option value="BR">Brazil</option>
                                        <option value="IO">British Indian Ocean Territory</option>
                                        <option value="BN">Brunei Darussalam</option>
                                        <option value="BG">Bulgaria</option>
                                        <option value="BF">Burkina Faso</option>
                                        <option value="BI">Burundi</option>
                                        <option value="KH">Cambodia</option>
                                        <option value="CM">Cameroon</option>
                                        <option value="CA">Canada</option>
                                        <option value="CV">Cape Verde</option>
                                        <option value="KY">Cayman Islands</option>
                                        <option value="CF">Central African Republic</option>
                                        <option value="TD">Chad</option>
                                        <option value="CL">Chile</option>
                                        <option value="CN">China</option>
                                        <option value="CX">Christmas Island</option>
                                        <option value="CC">Cocos (Keeling) Islands</option>
                                        <option value="CO">Colombia</option>
                                        <option value="KM">Comoros</option>
                                        <option value="CG">Congo</option>
                                        <option value="CD">Congo, the Democratic Republic of the
                                        </option>
                                        <option value="CK">Cook Islands</option>
                                        <option value="CR">Costa Rica</option>
                                        <option value="CI">Côte d'Ivoire</option>
                                        <option value="HR">Croatia</option>
                                        <option value="CU">Cuba</option>
                                        <option value="CW">Curaçao</option>
                                        <option value="CY">Cyprus</option>
                                        <option value="CZ">Czech Republic</option>
                                        <option value="DK">Denmark</option>
                                        <option value="DJ">Djibouti</option>
                                        <option value="DM">Dominica</option>
                                        <option value="DO">Dominican Republic</option>
                                        <option value="EC">Ecuador</option>
                                        <option value="EG">Egypt</option>
                                        <option value="SV">El Salvador</option>
                                        <option value="GQ">Equatorial Guinea</option>
                                        <option value="ER">Eritrea</option>
                                        <option value="EE">Estonia</option>
                                        <option value="ET">Ethiopia</option>
                                        <option value="FK">Falkland Islands (Malvinas)</option>
                                        <option value="FO">Faroe Islands</option>
                                        <option value="FJ">Fiji</option>
                                        <option value="FI">Finland</option>
                                        <option value="FR">France</option>
                                        <option value="GF">French Guiana</option>
                                        <option value="PF">French Polynesia</option>
                                        <option value="TF">French Southern Territories</option>
                                        <option value="GA">Gabon</option>
                                        <option value="GM">Gambia</option>
                                        <option value="GE">Georgia</option>
                                        <option value="DE">Germany</option>
                                        <option value="GH">Ghana</option>
                                        <option value="GI">Gibraltar</option>
                                        <option value="GR">Greece</option>
                                        <option value="GL">Greenland</option>
                                        <option value="GD">Grenada</option>
                                        <option value="GP">Guadeloupe</option>
                                        <option value="GU">Guam</option>
                                        <option value="GT">Guatemala</option>
                                        <option value="GG">Guernsey</option>
                                        <option value="GN">Guinea</option>
                                        <option value="GW">Guinea-Bissau</option>
                                        <option value="GY">Guyana</option>
                                        <option value="HT">Haiti</option>
                                        <option value="HM">Heard Island and McDonald Islands</option>
                                        <option value="VA">Holy See (Vatican City State)</option>
                                        <option value="HN">Honduras</option>
                                        <option value="HK">Hong Kong</option>
                                        <option value="HU">Hungary</option>
                                        <option value="IS">Iceland</option>
                                        <option value="IN">India</option>
                                        <option value="ID">Indonesia</option>
                                        <option value="IR">Iran, Islamic Republic of</option>
                                        <option value="IQ">Iraq</option>
                                        <option value="IE">Ireland</option>
                                        <option value="IM">Isle of Man</option>
                                        <option value="IL">Israel</option>
                                        <option value="IT">Italy</option>
                                        <option value="JM">Jamaica</option>
                                        <option value="JP">Japan</option>
                                        <option value="JE">Jersey</option>
                                        <option value="JO">Jordan</option>
                                        <option value="KZ">Kazakhstan</option>
                                        <option value="KE">Kenya</option>
                                        <option value="KI">Kiribati</option>
                                        <option value="KP">Korea, Democratic People's Republic of
                                        </option>
                                        <option value="KR">Korea, Republic of</option>
                                        <option value="KW">Kuwait</option>
                                        <option value="KG">Kyrgyzstan</option>
                                        <option value="LA">Lao People's Democratic Republic</option>
                                        <option value="LV">Latvia</option>
                                        <option value="LB">Lebanon</option>
                                        <option value="LS">Lesotho</option>
                                        <option value="LR">Liberia</option>
                                        <option value="LY">Libya</option>
                                        <option value="LI">Liechtenstein</option>
                                        <option value="LT">Lithuania</option>
                                        <option value="LU">Luxembourg</option>
                                        <option value="MO">Macao</option>
                                        <option value="MK">Macedonia, the former Yugoslav Republic of
                                        </option>
                                        <option value="MG">Madagascar</option>
                                        <option value="MW">Malawi</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="MV">Maldives</option>
                                        <option value="ML">Mali</option>
                                        <option value="MT">Malta</option>
                                        <option value="MH">Marshall Islands</option>
                                        <option value="MQ">Martinique</option>
                                        <option value="MR">Mauritania</option>
                                        <option value="MU">Mauritius</option>
                                        <option value="YT">Mayotte</option>
                                        <option value="MX">Mexico</option>
                                        <option value="FM">Micronesia, Federated States of</option>
                                        <option value="MD">Moldova, Republic of</option>
                                        <option value="MC">Monaco</option>
                                        <option value="MN">Mongolia</option>
                                        <option value="ME">Montenegro</option>
                                        <option value="MS">Montserrat</option>
                                        <option value="MA">Morocco</option>
                                        <option value="MZ">Mozambique</option>
                                        <option value="MM">Myanmar</option>
                                        <option value="NA">Namibia</option>
                                        <option value="NR">Nauru</option>
                                        <option value="NP">Nepal</option>
                                        <option value="NL">Netherlands</option>
                                        <option value="NC">New Caledonia</option>
                                        <option value="NZ">New Zealand</option>
                                        <option value="NI">Nicaragua</option>
                                        <option value="NE">Niger</option>
                                        <option value="NG">Nigeria</option>
                                        <option value="NU">Niue</option>
                                        <option value="NF">Norfolk Island</option>
                                        <option value="MP">Northern Mariana Islands</option>
                                        <option value="NO">Norway</option>
                                        <option value="OM">Oman</option>
                                        <option value="PK">Pakistan</option>
                                        <option value="PW">Palau</option>
                                        <option value="PS">Palestinian Territory, Occupied</option>
                                        <option value="PA">Panama</option>
                                        <option value="PG">Papua New Guinea</option>
                                        <option value="PY">Paraguay</option>
                                        <option value="PE">Peru</option>
                                        <option value="PH">Philippines</option>
                                        <option value="PN">Pitcairn</option>
                                        <option value="PL">Poland</option>
                                        <option value="PT">Portugal</option>
                                        <option value="PR">Puerto Rico</option>
                                        <option value="QA">Qatar</option>
                                        <option value="RE">Réunion</option>
                                        <option value="RO">Romania</option>
                                        <option value="RU">Russian Federation</option>
                                        <option value="RW">Rwanda</option>
                                        <option value="BL">Saint Barthélemy</option>
                                        <option value="SH">Saint Helena, Ascension and Tristan da Cunha
                                        </option>
                                        <option value="KN">Saint Kitts and Nevis</option>
                                        <option value="LC">Saint Lucia</option>
                                        <option value="MF">Saint Martin (French part)</option>
                                        <option value="PM">Saint Pierre and Miquelon</option>
                                        <option value="VC">Saint Vincent and the Grenadines</option>
                                        <option value="WS">Samoa</option>
                                        <option value="SM">San Marino</option>
                                        <option value="ST">Sao Tome and Principe</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="SN">Senegal</option>
                                        <option value="RS">Serbia</option>
                                        <option value="SC">Seychelles</option>
                                        <option value="SL">Sierra Leone</option>
                                        <option value="SG">Singapore</option>
                                        <option value="SX">Sint Maarten (Dutch part)</option>
                                        <option value="SK">Slovakia</option>
                                        <option value="SI">Slovenia</option>
                                        <option value="SB">Solomon Islands</option>
                                        <option value="SO">Somalia</option>
                                        <option value="ZA">South Africa</option>
                                        <option value="GS">South Georgia and the South Sandwich Islands
                                        </option>
                                        <option value="SS">South Sudan</option>
                                        <option value="ES">Spain</option>
                                        <option value="LK">Sri Lanka</option>
                                        <option value="SD">Sudan</option>
                                        <option value="SR">Suriname</option>
                                        <option value="SJ">Svalbard and Jan Mayen</option>
                                        <option value="SZ">Swaziland</option>
                                        <option value="SE">Sweden</option>
                                        <option value="CH">Switzerland</option>
                                        <option value="SY">Syrian Arab Republic</option>
                                        <option value="TW">Taiwan, Province of China</option>
                                        <option value="TJ">Tajikistan</option>
                                        <option value="TZ">Tanzania, United Republic of</option>
                                        <option value="TH">Thailand</option>
                                        <option value="TL">Timor-Leste</option>
                                        <option value="TG">Togo</option>
                                        <option value="TK">Tokelau</option>
                                        <option value="TO">Tonga</option>
                                        <option value="TT">Trinidad and Tobago</option>
                                        <option value="TN">Tunisia</option>
                                        <option value="TR">Turkey</option>
                                        <option value="TM">Turkmenistan</option>
                                        <option value="TC">Turks and Caicos Islands</option>
                                        <option value="TV">Tuvalu</option>
                                        <option value="UG">Uganda</option>
                                        <option value="UA">Ukraine</option>
                                        <option value="AE">United Arab Emirates</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="US">United States</option>
                                        <option value="UM">United States Minor Outlying Islands
                                        </option>
                                        <option value="UY">Uruguay</option>
                                        <option value="UZ">Uzbekistan</option>
                                        <option value="VU">Vanuatu</option>
                                        <option value="VE">Venezuela, Bolivarian Republic of</option>
                                        <option value="VN">Viet Nam</option>
                                        <option value="VG">Virgin Islands, British</option>
                                        <option value="VI">Virgin Islands, U.S.</option>
                                        <option value="WF">Wallis and Futuna</option>
                                        <option value="EH">Western Sahara</option>
                                        <option value="YE">Yemen</option>
                                        <option value="ZM">Zambia</option>
                                        <option value="ZW">Zimbabwe</option>

                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="city" placeholder="New York"
                                        required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>State <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="state" placeholder="New York"
                                        required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>ZIP Code <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="zip" placeholder="12300"
                                        required>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-primary">Save Address</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // $(document).on('click', '#sumup', function() {
        // let payment_method = $('input[name="payment"]').val();
        //     $('#place_order_btn').attr('disabled', true);
        //     if ($(this).is('checked')) {
        //         if ($('input[name="billing_address"]').is(':checked')) {
        //             $('#place_order_btn').attr('disabled', false);
        //         } else {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: 'Warning',
        //                 text: 'Select Location',
        //                 showDenyButton: false,
        //                 showCancelButton: false,
        //                 confirmButtonText: 'OK'
        //             })
        //         }
        //     }
        // });

        function evaluateShipping(obj) {
            if ($(obj).is(':checked')) {
                let country_iso_2 = $(obj).data('country-iso-2'),
                    weight = [];

                $.map($('.product_weight'), function(element, index) {
                    return weight.push($(element).text());
                });

                let address = $(obj).val();
                let order_id = $('#current_order_id').val();
                let shiping_cost_of_cart = $('#shiping_cost_of_cart');

                if (address != '' && country_iso_2 != '') {
                    // Perform AJAX request to fetch shipping cost for all items based on the user's adress
                    shiping_cost_of_cart.text('Estimating Cost...');
                    let sub_total = $('#sub_total_cost').text();

                    $.ajax({
                        url: '/estimate-order-ship-cost',
                        method: 'GET',
                        data: {
                            country_iso_2: country_iso_2,
                            weights: weight,
                            euro_cart_total: $('#euro_cart_total').val(),
                            // address_id: address,
                            // order_id: order_id
                        },
                        success: function(response) {

                            $(shiping_cost_of_cart).text('');
                            $(shiping_cost_of_cart).text(response.shipping_cost);
                            $('#place_order_btn').attr('disabled', false);
                            $('#total_cost').text(response.shipping_plus_total);
                            $('input[name="shipping_cost"]').val(response.euro_shipping_cost)
                            $('input[name="total_amount_to_pay"]').val(response.euro_shipping_plus_total)
                            // let markup = '';
                            //
                            // if (Object.keys(shipping_costs).length > 0) {
                            //     // Iterate over shipping methods and create radio buttons
                            //     for (let provider in shipping_costs) {
                            //         if (shipping_costs.hasOwnProperty(provider) && shipping_costs[provider] >
                            //             0) {
                            //             markup += `
                        //             <div class="form-check">
                        //                 <input class="form-check-input" type="radio" name="shipping_provider" id="${provider}" onchange="calculate_total(this)"
                        //                     value="${provider.toUpperCase()}" data-cost="${shipping_costs[provider]}" required>
                        //                 <label class="form-check-label" for="${provider}">${provider.toUpperCase()} - &euro; ${shipping_costs[provider]}</label>
                        //             </div>
                        //         `;
                            //         }
                            //     }
                            // if (response.markup) {
                            //     // Update the HTML content of shiping_cost_of_cart with the generated markup
                            //     shiping_cost_of_cart.html(response.markup);
                            //     //enable the submit btn
                            // } else {
                            //     shiping_cost_of_cart.text(
                            //         'No Shipping options for your selected address, select another address or try again later.'
                            //     );
                            // }

                        },
                        error: function(e, f, g) {
                            shiping_cost_of_cart.text(
                                "Error while estimating shipping cost, choose another address");
                        }
                    });
                }
            }
        }

        function calculate_total(obj) {
            let shipping_cost = $(obj).data('cost');
            let sub_total = $('#sub_total_cost').text();
            $('#total_cost').text(parseFloat(shipping_cost) + parseFloat(sub_total));
        }
    </script>
@endsection
