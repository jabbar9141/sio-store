@php
    use Illuminate\Support\Facades\Auth;
    use App\MyHelpers;
    $role = Auth::user()->role;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Edit product')
@section('plugins')
    <link href="{{ asset('backend_assets') }}/plugins/Drag-And-Drop/dist/imageuploadify.min.css" rel="stylesheet" />
    <link href="{{ asset('backend_assets') }}/plugins/input-tags/css/tagsinput.css" rel="stylesheet" />
@endsection
@section('content')

    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Product</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route($role . '-profile') }}"><i
                                class="bx
                    bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product</li>
                    <li class="breadcrumb-item active" aria-current="page">Edit product</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title">Edit Product</h5>
            <hr />
            <form action="{{ route('vendor-product-update', ['id' => $data->product_id]) }}" method="POST"
                id="product_form" enctype="multipart/form-data">
                <input name="product_id" value="{{ $data->product_id }}" hidden />
                @csrf
                <div class="form-body mt-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="border border-3 p-4 rounded">
                                <div class="mb-3">
                                    <label for="inputProductTitle" class="form-label">Product Name/Title</label>
                                    <input name="product_name" type="text" class="form-control" id="inputProductTitle"
                                        placeholder="Enter product title" required value="{{ $data->product_name }}">
                                    <small style="color: #e20000" class="error" id="product_name-error"></small>

                                </div>
                                <div class="mb-3">
                                    <label for="inputProductDescription" class="form-label">Short Description</label>
                                    <textarea name="product_short_description" class="form-control" id="inputProductDescription" rows="3" required>{{ $data->product_short_description }}</textarea>
                                    <small style="color: #e20000" class="error"
                                        id="product_short_description-error"></small>

                                </div>
                                <div class="mb-3">
                                    <label for="inputProductLongDescription" class="form-label">Detailed Description</label>
                                    <textarea id="mytextarea" name="product_long_description"> {{ $data->product_long_description }}
                                    </textarea>
                                    <small style="color: #e20000" class="error"
                                        id="product_long_description-error"></small>

                                </div>



                                <div class="row mb-3">
                                    <div class="form-group col-sm-12">
                                        <label class="form-label">Product Tags( <small>comma seperated</small> )</label>
                                        <input name="product_tags" type="text" class="form-control visually-hidden"
                                            data-role="tagsinput" value="{{ $data->product_tags }}">
                                    </div>
                                </div>
                                <hr>
                                <h5>Product variations</h5>
                                <div id="form-wrapper">
                                    @foreach ($data->variations as $variation)
                                        <div class="input-group row">
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Color<span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="colors[]" id=""
                                                    value="{{ $variation->color_name }}">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Size<span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="sizes[]" id=""
                                                    value="{{ $variation->size_name }}">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Width<span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="width[]"
                                                    value="{{ $variation->width }}" required>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Length<span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="length[]" id=""
                                                    value="{{ $variation->length }}" required>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Height<span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="height[]"
                                                    id="" value="{{ $variation->height }}" required>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Weight<span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="weight[]"
                                                    id="" value="{{ $variation->weight }}" required>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="dimention">Price<span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="prices[]"
                                                    onfocusout="checkPrice(this)" id=""
                                                    value="{{ MyHelpers::fromEuro(Auth::user()->currency_id, $variation->price) }}"
                                                    required step="0.001">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label for="price">Whole Sale Price<span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="whole_sale_price[]"
                                                    onfocusout="checkPrice(this)" id="" required
                                                    value="{{ MyHelpers::fromEuro(Auth::user()->currency_id, $variation->whole_sale_price) }}"
                                                    step="0.001">
                                            </div>
                                            <div class=" mt-2 col-md-3">
                                                <label for="inputProductQuantity" class="form-label">Quantity</label>
                                                <input name="product_quantity[]" type="number" min="1"
                                                    class="form-control" id="inputProductQuantity" required
                                                    value="{{ $variation->product_quantity }}">
                                                <small style="color: #e20000" class="error"
                                                    id="product_quantity-error"></small>

                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="form-label">Upload Video</label>
                                                <input name="product_video[]" class="form-control video-input"
                                                    type="file" accept="video/*">
                                                <small style="color: #e20000" class="error"
                                                    id="product_video-error"></small>
                                                <div class="row video-preview" style="padding: 20px">
                                                    @php
                                                        $videos = json_decode($variation->video_url);
                                                    @endphp
                                                    @if (isset($videos) && count($videos) > 0)
                                                        @foreach ($videos as $video)
                                                            <video class="mt-2 ms-2"
                                                                src="{{ url('uploads/images/product/' . $video) }}"
                                                                alt="" style="max-width:150px;" controls></video>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="dimention">Image<span class="text-danger">*</span></label>
                                                <input class="form-control image-input" type="file" name="files[]"
                                                    id="" value="" multiple>
                                                <div class="row image-preview" style="padding: 20px">
                                                    @php
                                                        $images = json_decode($variation->image_url);
                                                    @endphp
                                                    @if (isset($images) && count($images) > 0)
                                                        @foreach ($images as $image)
                                                            <img class="mt-2 ms-2"
                                                                src="{{ url('uploads/images/product/' . $image) }}"
                                                                alt="" style="max-width:100px;">
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="hidden" name="variation_id[]" id=""
                                                value="{{ $variation->id ?? null }}">
                                            <div class="col-md-2">
                                                <br>
                                                <button class="remove-btn btn btn-sm btn-danger mt-2"
                                                    type="button">Remove</button>
                                            </div>
                                            <input type="hidden" name="product_variations[]" class="product-variation">
                                        </div>
                                    @endforeach
                                </div>
                                <br>
                                <button id="add-btn" type="button" class="btn btn-sm btn-primary">Add Group</button>


                                @php
                                    $locs = App\MyHelpers::getAllLocations();
                                @endphp
                                <div class="row">
                                    <div class="form-group col-sm-6 mb-3">
                                        <label for="ships_from">Ships From <span class="text-danger">*</span></label>
                                        <select id="ships_from" required name="ships_from">
                                            <option value="">--select product shipping origin--</option>
                                            @foreach ($locs as $l)
                                                <option value="{{ $l->id }}"
                                                    {{ $l->id == $data->ships_from ? 'selected' : '' }}>
                                                    {{ $l->name }},
                                                    {{ $l->country_code }}</option>
                                            @endforeach
                                        </select>
                                        <small style="color: #e20000" class="error" id="ships_from-error"></small>
                                    </div>
                                    <div class="form-group col-sm-6 mb-3">
                                        <label for="available_regions">Supported Regions <span
                                                class="text-danger">*</span></label>
                                        <select id="available_regions" name="available_regions[]" multiple required>

                                            <option value="global"
                                                {{ $data->available_regions != '' && in_array('global', (array) json_decode($data->available_regions, true)) ? 'selected' : '' }}>
                                                Global</option>
                                            <option value="AF"
                                                {{ $data->available_regions != '' && in_array('AF', (array) json_decode($data->available_regions, true)) ? 'selected' : '' }}>
                                                Afghanistan</option>
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
                                        <small style="color: #e20000" class="error"
                                            id="available_regions-error"></small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="form-label">Product Thumbnail</label>
                                    <div class="col-sm-12 text-secondary">
                                        <input name="product_thumbnail" id="product_thumbnail" class="form-control"
                                            type="file">
                                        <small style="color: #e20000" class="error"
                                            id="product_thumbnail-error"></small>

                                        <div>
                                            <img class="card-img-top"
                                                src="{{ url('uploads/images/product/' . $data->product_thumbnail) }}"
                                                style="max-width: 250px; margin-top: 20px" id="show_image">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Images</label>
                                    <input name="product_images[]" class="form-control" type="file" id="multi_image"
                                        multiple>
                                    <div class="row" id="preview_img" style="padding: 20px">
                                        @foreach ($productImages as $item)
                                            <img class="thumb"
                                                src="{{ url('uploads/images/product/' . $item->product_image) }}"
                                                style="max-width: 200px; margin-top: 20px" alt="product image" />
                                        @endforeach
                                    </div>
                                    <small style="color: #e20000" class="error" id="product_images-error"></small>
                                </div>


                                {{-- <div class="mb-3">
                                    <label class="form-label">Product Video</label>
                                    <input name="product_video" class="form-control" type="file" id="product_video"
                                    accept="video/*" >
                                    <div class="row" id="preview_video" style="padding: 20px">

                                            <video class="thumb"
                                                src="{{ url('uploads/images/product/' . $data->video_link) }}"
                                                style="max-width: 200px; margin-top: 20px" alt="product image"  controls></video>

                                    </div>
                                    <small style="color: #e20000" class="error" id="product_video-error"></small>
                                </div> --}}





                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border border-3 p-4 rounded">
                                <div class="row g-3">
                                    {{-- <div class="col-md-12">
                                        <div class="form-check">
                                            <input name="retail_available" class="form-check-input" type="checkbox"
                                                id="retail_available" {{ $data->retail_available ? 'checked' : null }}>
                                            <label class="form-check-label" for="retail_available">Retail
                                                Available?</label>
                                        </div>
                                        <label for="inputPrice" class="form-label">Retail Price (&euro;)</label>
                                        <input name="product_price" type="text" class="form-control" id="inputPrice"
                                            placeholder="00.00" value="{{ $data->product_price }}">
                                        <small style="color: #e20000" class="error" id="product_price-error"></small>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input name="wholesale_available" class="form-check-input" type="checkbox"
                                                id="wholesale_available"
                                                {{ $data->wholesale_available ? 'checked' : null }}>
                                            <label class="form-check-label" for="wholesale_available">Wholesale
                                                Available</label>
                                        </div>
                                        <label for="wholesale_price" class="form-label">Wholesale Price (&euro;)</label>
                                        <input name="wholesale_price" type="text" class="form-control"
                                            id="wholesale_price" placeholder="00.00"
                                            value="{{ $data->wholesale_price }}">
                                        <small style="color: #e20000" class="error" id="wholesale_price-error"></small>

                                    </div> --}}
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input name="returns_allowed" class="form-check-input"
                                                {{ $data->returns_allowed ? 'checked' : null }} type="checkbox"
                                                id="returns_allowed">
                                            <label class="form-check-label" for="returns_allowed">Returns Allowed
                                                ?</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputProductType" class="form-label">Product Brand</label>
                                        <select name="brand_id" class="form-select" id="inputProductType">
                                            <option>Choose a brand</option>
                                            @foreach ($brands as $item)
                                                <option value="{{ $item->brand_id }}"
                                                    {{ $item->brand_id == $data->brand_id ? 'selected' : null }}>
                                                    {{ $item->brand_name }}</option>
                                            @endforeach
                                        </select>
                                        <small style="color: #e20000" class="error" id="brand_id-error"></small>

                                    </div>
                                    <div class="col-12">
                                        <label for="inputVendor" class="form-label">Product Category</label>
                                        <select class="form-select" id="inputVendor" name="category_id">
                                            <option>Choose a category</option>
                                            @foreach ($categories as $item)
                                                <option>Choose a category</option>
                                                <option
                                                    {{ $item->category_id && $item->category_id == $data->category_id ? 'selected' : null }}
                                                    value="{{ $item->category_id }}">{{ $item->category_name }}</option>
                                            @endforeach
                                        </select>
                                        <small style="color: #e20000" class="error" id="category_id-error"></small>

                                    </div>
                                    <div class="form-check">
                                        <input name="product_status" class="form-check-input" type="checkbox"
                                            id="gridCheck" {{ $data->product_status ? 'checked' : null }}>
                                        <label class="form-check-label" for="gridCheck">Available</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="hot_deal" class="form-check-input" type="checkbox" id="gridCheck2"
                                            {{ $data->hot_deal ? 'checked' : null }}>
                                        <label class="form-check-label" for="gridCheck2">Hot Deal</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="featured_product" class="form-check-input" type="checkbox"
                                            id="gridCheck3" {{ $data->featured_product ? 'checked' : null }}>
                                        <label class="form-check-label" for="gridCheck3">Featured Product</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="special_offer" class="form-check-input" type="checkbox"
                                            id="gridCheck4" {{ $data->special_offer ? 'checked' : null }}>
                                        <label class="form-check-label" for="gridCheck4">Special Offer</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="special_deal" class="form-check-input" type="checkbox"
                                            id="gridCheck5" {{ $data->special_deal ? 'checked' : null }}>
                                        <label class="form-check-label" for="gridCheck5">Special Deal</label>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                                aria-hidden="true"></span>
                                            Save Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>

            </form>
        </div>
    </div>
    </div>
@endsection


@section('AjaxScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <script>
        function checkPrice(obj) {
            let input_name = $(obj).attr('name');

            if ($(obj).val() < 1) {
                $(obj).val(1);
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Price must be greater than 1',
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                });
                return false;
            }

            let objValue = parseFloat($(obj).val());
            let parent_group = $(obj).parents('.input-group');
            if (input_name == 'whole_sale_price[]') {
                let price = parseFloat($(parent_group).find('input[name="prices[]"]').val());
                if (price <= objValue) {
                    let difference = price - 1;
                    $(obj).val(difference);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Price must be greater than whole sale price',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                let wholesale_price = parseFloat($(parent_group).find('input[name="whole_sale_price[]"]').val());
                if (wholesale_price >= objValue) {
                    let difference = wholesale_price + 1;
                    $(obj).val(difference);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Price must be greater than whole sale price',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    });
                }
            }
        }

        $(document).on('change', 'input[name="product_quantity[]"]', function() {
            if (this.value < 0) {
                this.value = 1;
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Quantity must be greater than 0',
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                });
            }
        })

        document.addEventListener('DOMContentLoaded', function() {
            var formWrapper = document.getElementById('form-wrapper');
            var addButton = document.getElementById('add-btn');

            // Hide the remove button in the first form
            var initialRemoveButton = document.querySelector('.remove-btn');
            if (initialRemoveButton) {
                initialRemoveButton.style.display = 'none';
            }

            // Function to create a new input group by cloning the initial group
            function createInputGroup() {
                var initialGroup = document.querySelector('.input-group');
                var newInputGroup = initialGroup.cloneNode(true);

                // Clear values in the cloned input group
                var inputs = newInputGroup.querySelectorAll('input, select');
                inputs.forEach(function(input) {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                });

                // Clear image and video previews
                var imagePreview = newInputGroup.querySelector('.image-preview');
                var videoPreview = newInputGroup.querySelector('.video-preview');
                if (imagePreview) imagePreview.innerHTML = '';
                if (videoPreview) videoPreview.innerHTML = '';

                // Show the remove button in the cloned input group
                var removeButton = newInputGroup.querySelector('.remove-btn');
                removeButton.style.display = 'inline-block';

                // Add event listener to the remove button in the cloned input group
                removeButton.addEventListener('click', function() {
                    newInputGroup.remove();
                });

                // Add event listeners for image and video previews
                var imageInput = newInputGroup.querySelector('.image-input');
                if (imageInput) {
                    imageInput.addEventListener('change', function() {
                        previewImages(this);
                    });
                }

                var videoInput = newInputGroup.querySelector('.video-input');
                if (videoInput) {
                    videoInput.addEventListener('change', function() {
                        previewVideo(this);
                    });
                }

                return newInputGroup;
            }

            // Function to preview selected images
            function previewImages(input) {
                var imagePreview = input.closest('.input-group').querySelector('.image-preview');
                imagePreview.innerHTML = '';
                if (input.files) {
                    Array.from(input.files).forEach(file => {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100px';
                            img.style.marginRight = '10px';
                            imagePreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }

            // Function to preview selected video
            function previewVideo(input) {
                var videoPreview = input.closest('.input-group').querySelector('.video-preview');
                videoPreview.innerHTML = '';
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var video = document.createElement('video');
                        video.src = e.target.result;
                        video.controls = true;
                        video.style.maxWidth = '200px';
                        videoPreview.appendChild(video);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Add event listener to the add button
            addButton.addEventListener('click', function() {
                var newInputGroup = createInputGroup();
                formWrapper.appendChild(newInputGroup);
            });

            // Add event listeners to the initial image and video inputs
            var initialImageInput = document.querySelector('.image-input');
            if (initialImageInput) {
                initialImageInput.addEventListener('change', function() {
                    previewImages(this);
                });
            }

            var initialVideoInput = document.querySelector('.video-input');
            if (initialVideoInput) {
                initialVideoInput.addEventListener('change', function() {
                    previewVideo(this);
                });
            }

            // Add event listener to the initial remove button
            if (initialRemoveButton) {
                initialRemoveButton.addEventListener('click', function() {
                    this.parentNode.parentNode.remove();
                });
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            let selectedFiles = [];

            $('#multi_image').on('change', function(event) {
                const files = Array.from(event.target.files);
                selectedFiles = selectedFiles.concat(files);

                // Remove duplicates by file name (or use another unique property if needed)
                const fileNames = new Set();
                selectedFiles = selectedFiles.filter(file => {
                    if (fileNames.has(file.name)) {
                        return false;
                    } else {
                        fileNames.add(file.name);
                        return true;
                    }
                });

                console.log('Selected files:', selectedFiles);

                // Display the selected files
                const preview = document.getElementById('preview_img');
                preview.innerHTML = ''; // Clear current preview
                selectedFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'thumb';
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.margin = '10px';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });

                // Clear the input field to allow selecting the same file again
                event.target.value = '';
            });

            $('#product_form').on('submit', function(event) {
                event.preventDefault();
                var $submitButton = $('button[type="submit"]');
                var $spinner = $submitButton.find('.spinner-border');

                // Show spinner and disable button
                $spinner.removeClass('d-none');
                $submitButton.prop('disabled', true);
                // Remove errors if the conditions are true
                $('#product_form *').filter(':input.is-invalid').each(function() {
                    this.classList.remove('is-invalid');
                });
                $('#product_form *').filter('.error').each(function() {
                    this.innerHTML = '';
                });


                var productVariations = [];
                const formData = new FormData(this);
                document.querySelectorAll('.input-group').forEach(function(group, index) {
                    var variation_id = group.querySelector('input[name="variation_id[]"]').value;

                    var color = group.querySelector('input[name="colors[]"]').value;
                    var size = group.querySelector('input[name="sizes[]"]').value;
                    var width = group.querySelector('input[name="width[]"]').value;
                    var length = group.querySelector('input[name="length[]"]').value;
                    var height = group.querySelector('input[name="height[]"]').value;
                    var weight = group.querySelector('input[name="weight[]"]').value;
                    var product_quantity = group.querySelector('input[name="product_quantity[]"]')
                        .value;

                    var price = group.querySelector('input[name="prices[]"]').value;
                    var files = group.querySelector('input[name="files[]"]').files;
                    var whole_sale_price = group.querySelector('input[name="whole_sale_price[]"]')
                        .value;
                    var product_videos = group.querySelector('input[name="product_video[]"]').files;

                    var variation = {
                        variation_id: variation_id,
                        color_id: color,
                        size_id: size,
                        width: width,
                        length: length,
                        height: height,
                        weight: weight,
                        price: price,
                        product_quantity: product_quantity,
                        fileIndices: Array.from(files).map((file, fileIndex) => fileIndex),
                        videoIndices: Array.from(product_videos).map((product_videos,
                            product_videosIndex) => product_videosIndex),
                        whole_sale_price: whole_sale_price

                    };

                    productVariations.push(variation);

                    // if (file) {
                    //     formData.append('files[]', file);
                    // }
                });
                formData.append('product_variations', JSON.stringify(productVariations));
                selectedFiles.forEach(file => {
                    formData.append('product_images[]', file);
                });

                $.ajax({
                    url: "{{ route('vendor-product-update', ['id' => $data->product_id]) }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        // Remove errors if the conditions are true
                        $('#product_form *').filter(':input.is-invalid').each(function() {
                            this.classList.remove('is-invalid');
                        });
                        $('#product_form *').filter('.error').each(function() {
                            this.innerHTML = '';
                        });

                        // Hide spinner and re-enable button
                        $spinner.addClass('d-none');
                        $submitButton.prop('disabled', false);
                        Swal.fire({
                            icon: 'success',
                            title: response.msg,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        var res = $.parseJSON(response.responseText);
                        $.each(res.errors, function(key, err) {
                            $('#' + key + '-error').text(err[0]);
                            $('#' + key).addClass('is-invalid');
                        });

                        // Hide spinner and re-enable button
                        $spinner.addClass('d-none');
                        $submitButton.prop('disabled', false);
                    }
                });
            });
        });
    </script>

@endsection


@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#product_thumbnail').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#show_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>

    <script src="https://cdn.tiny.cloud/1/7oooqgtzrwzitybq53na6ljyfcqa2lq3y0tsf253mx2nfhbq/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea'
        });
    </script>
    <script>
        function addVariantItem() {
            let mark = `
                <div class="variant_item row">
                    <div class="form-group col-sm-4" <label for="variation_name">Variation name</label>
                        <input type="text" class="form-control" name="variation_names[]"
                            placeholder="e.g color">
                    </div>
                    <div class="form-group col-sm-7">
                        <label class="form-label">Variants ( <small>comma seperated</small>
                            )</label>
                        <input name="variation_values[]" type="text"
                            class="form-control visually-hidden" data-role="tagsinput"
                            placeholder="blue, black etc">
                    </div>
                    <div class="col-sm-1 mt-4">
                        <button class="btn btn-danger btn-sm" type="button" onclick="removeVariantItem(this)"><i
                                class="fas fa-times"></i></button>
                    </div>
                </div>`;

            $('.variant_items').append(mark);
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function removeVariantItem(obj) {
            $(obj).closest('.variant_item').remove();
        }
    </script>
    <script src="{{ asset('backend_assets') }}/plugins/input-tags/js/tagsinput.js"></script>
    <script>
        $(function() {
            $("#ships_from").selectize({
                delimiter: " - ",
                persist: false,
                maxItems: 1,
                valueField: "id",
                labelField: "name",
                searchField: ["name"],
            });
        });

        $(function() {
            $("#available_regions").selectize();
        });
    </script>
@endsection
