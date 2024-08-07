@php
    use Illuminate\Support\Facades\Auth;
    $role = Auth::user()->role;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Add new product')
@section('plugins')
    <link href="{{ asset('backend_assets') }}/plugins/Drag-And-Drop/dist/imageuploadify.min.css" rel="stylesheet" />
    <link href="{{ asset('backend_assets') }}/plugins/input-tags/css/tagsinput.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css" rel="stylesheet">
@endsection

@section('content')
    <style>
        /* Temporary CSS for debugging */
        .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure the container for the dropdown is positioned correctly */
        .bootstrap-select .dropdown-menu {
            position: absolute !important;
        }
    </style>

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
                    <li class="breadcrumb-item active" aria-current="page">Add new product</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
    <div class="card">
        <div class="card-body p-4">
            <!--<h5>Mass upload XML</h5>-->
            {{-- <form action="{{ route('vendor-product-massInsertProducts') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="xml_file" id="" class="form-control">
                <button type="submit">upload</button>
            </form>
            <h5>Mass upload CSV</h5>
            <form action="{{ route('vendor-product-massInsertProductsFromCSV') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file" id="" class="form-control">
                <button type="submit">upload</button>
            </form> --}}
            <h5 class="card-title">Add New Product</h5>

            <hr />
            <form action="{{ route('vendor-product-create') }}" method="POST" id="product_form"
                enctype="multipart/form-data">
                @csrf
                <div class="form-body mt-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="border border-3 p-4 rounded">
                                <div class="mb-3">
                                    <label for="inputProductTitle" class="form-label">Product Name/Title <span
                                            class="text-danger">*</span></label>
                                    <input name="product_name" type="text" class="form-control" id="inputProductTitle"
                                        placeholder="Enter product title" required>
                                    <small style="color: #e20000" class="error" id="product_name-error"></small>

                                </div>
                                <div class="mb-3">
                                    <label for="inputProductTitle" class="form-label">Product Code(SKU) <span
                                            class="text-danger">*</span></label>
                                    <input name="product_code" type="text" class="form-control" id="inputProductTitle"
                                        placeholder="Enter product code" required>
                                    <small style="color: #e20000" class="error" id="product_code-error"></small>

                                </div>
                                <div class="mb-3">
                                    <label for="inputProductDescription" class="form-label">Short Description <span
                                            class="text-danger">*</span></label>
                                    <textarea name="product_short_description" class="form-control" id="inputProductDescription" rows="3" required></textarea>
                                    <small style="color: #e20000" class="error"
                                        id="product_short_description-error"></small>

                                </div>
                                <div class="mb-3">
                                    <label for="inputProductLongDescription" class="form-label">Detailed Description</label>
                                    <textarea id="mytextarea" name="product_long_description"> </textarea>
                                    <small style="color: #e20000" class="error"
                                        id="product_long_description-error"></small>

                                </div>
                                <!--<div class="mb-3">-->
                                <!--                                   <label for="inputProductLongDescription" class="form-label">Mass upload CSV</label>-->
                                <!--                                   <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv">-->
                                <!--                               </div>-->


                                <div class="row mb-3">
                                    <div class="form-group col-sm-12">
                                        <label class="form-label">Product Tags( <small>comma seperated</small> )</label>
                                        <input name="product_tags" type="text" class="form-control visually-hidden"
                                            data-role="tagsinput">
                                    </div>
                                </div>
                                <hr>
                                <h5>Product variations</h5>
                                <div id="form-wrapper">
                                    <div class="input-group row" id>
                                        <div class="col-md-3 mt-2">
                                            <label for="color">Color<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="colors[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="size">Size<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="sizes[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="width">Width<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="width[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="length">Length<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="length[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="height">Height<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="height[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="weight">Weight<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="weight[]" id="">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="price">Price<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" onfocusout="checkPrice(this)"
                                                step="0.001" name="prices[]" id="" required>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="price">Whole Sale Price<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="number" step="0.001"
                                                onfocusout="checkPrice(this)" name="whole_sale_price[]" id=""
                                                required>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="quantity">Quantity<span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="product_quantity[]"
                                                id="inputProductQuantity" min="1" required>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label class="form-label">Upload Video</label>
                                            <input name="product_video[]" class="form-control video-input" type="file"
                                                accept="video/*">
                                            <div class="row video-preview" style="padding: 20px"></div>
                                            <small style="color: #e20000" class="error"
                                                id="product_video-error"></small>
                                        </div>
                                        <div class="col-md-5 mt-2">
                                            <label class="form-label">Product Images</label>
                                            <input name="files[]" class="form-control image-input" type="file"
                                                multiple>
                                            <div class="row image-preview" style="padding: 20px"></div>
                                            <small style="color: #e20000" class="error" id="var_image-error"></small>
                                        </div>

                                        <div class="col-md-2 mt-2">
                                            <button class="remove-btn btn btn-sm btn-danger"
                                                type="button">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <button id="add-btn" type="button" class="btn btn-sm btn-primary">Add Group</button>


                                {{-- <div class="d-flex justify-content-between">
                                    <small>Here we set variations of the product like color, size etc.</small>
                                    <button class="btn btn-primary" type="button" onclick="addVariantItem()"><i
                                            class="fas fa-plus"></i></button>
                                </div> --}}
                                <br>
                                {{-- <div class="variant_items">
                                    <div class="variant_item row">
                                        <div class="form-group col-sm-4"> <label for="variation_name">Variation
                                            name</label>
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
                                            <button class="btn btn-danger btn-sm" type="button"
                                                onclick="removeVariantItem(this)"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                </div> --}}
                                <hr>
                                {{-- <div class="mb-3 row">
                                    <div class="form-group col-sm-3">
                                        <label for="width">Width(cm) <span class="text-danger">*</span></label>
                                        <input type="number" name="width" id="width" step="any"
                                            placeholder="0.1" class="form-control" min="0" required>
                                        <small style="color: #e20000" class="error" id="width-error"></small>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="width">Length(cm) <span class="text-danger">*</span></label>
                                        <input type="number" name="length" min="0" id="length"
                                            step="any" placeholder="0.1" class="form-control" required>
                                        <small style="color: #e20000" class="error" id="length-error"></small>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="width">Height(cm) <span class="text-danger">*</span></label>
                                        <input type="number" name="height" id="height" step="any"
                                            placeholder="0.1" class="form-control" min="0" required>
                                        <small style="color: #e20000" class="error" id="height-error"></small>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="weight">Weight(Kg)</label>
                                        <input type="number" step="any" min="0" name="weight"
                                            id="weight" placeholder="0.00" class="form-control">
                                    </div>
                                </div> --}}
                                @php
                                    $locs = App\MyHelpers::getAllLocations();
                                @endphp
                                <div class="row">
                                    <div class="form-group col-sm-6 mb-3">
                                        <label for="ships_from">Ships From <span class="text-danger">*</span></label>
                                        <select id="" class="form-control" name="ships_from" required>
                                            <option value="">Shipping Origin</option>
                                            @foreach ($cities as $l)
                                                <option value="{{ $l->id }}">{{ $l->name }},
                                                    {{ $l->country_code }}</option>
                                            @endforeach
                                        </select>
                                        <small style="color: #e20000" class="error" id="ships_from-error"></small>
                                    </div>
                                    <div class="form-group col-sm-6 mb-3 selectpicker_parent">
                                        <label for="available_regions">Supported Regions <span
                                                class="text-danger">*</span></label>
                                        <select id="available_regions" required name="available_regions[]"
                                            {{-- data-size="7" data-container="container" data-actions-box="true" data-live-search="true"
                                            class="available_regions form-control" --}} multiple>
                                            <option value="global" selected>Global</option>

                                            @foreach ($countries as $c)
                                                <option value="{{ $c->iso2 }}">{{ $c->name ?? $c->iso2 }}</option>
                                            @endforeach
                                        </select>
                                        <small style="color: #e20000" class="error"
                                            id="available_regions-error"></small>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <label class="form-label">Product Thumbnail</label>
                                    <div class="col-sm-12 text-secondary">
                                        <input name="product_thumbnail" id="product_thumbnail" class="form-control"
                                            type="file" required>
                                        <small style="color: #e20000" class="error"
                                            id="product_thumbnail-error"></small>

                                        <div>
                                            <img class="card-img-top" style="max-width: 250px; margin-top: 20px"
                                                id="show_image">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Images</label>
                                    <input name="product_images[]" class="form-control" type="file" id="multi_image"
                                        multiple>
                                    <div class="row" id="preview_img" style="padding: 20px"></div>
                                    <small style="color: #e20000" class="error" id="product_images-error"></small>
                                </div>

                                {{-- <div class="mb-3">
                                    <label class="form-label">Upload Video</label>
                                    <input name="product_video" class="form-control" type="file" id="product_video"
                                        accept="video/*" required>

                                    <div class="row" id="preview_video" style="padding: 20px"></div>
                                    <small style="color: #e20000" class="error" id="product_video-error"></small>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border border-3 p-4 rounded">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        {{-- <div class="form-check">
                                            <input name="retail_available" class="form-check-input" checked
                                                type="checkbox" id="retail_available">
                                            <label class="form-check-label" for="retail_available">Retail
                                                Available?</label>
                                        </div> --}}
                                        {{-- <label for="inputPrice" class="form-label">Retail Price (&euro;)</label>
                                        <input name="product_price" type="text" class="form-control" id="inputPrice"
                                            placeholder="00.00">
                                        <small style="color: #e20000" class="error" id="product_price-error"></small>

                                    </div> --}}
                                        {{-- <div class="col-md-12">
                                        <div class="form-check">
                                            <input name="wholesale_available" class="form-check-input" checked
                                                type="checkbox" id="wholesale_available">
                                            <label class="form-check-label" for="wholesale_available">Wholesale
                                                Available</label>
                                        </div>
                                        <label for="wholesale_price" class="form-label">Wholesale Price (&euro;)</label>
                                        <input name="wholesale_price" type="text" class="form-control"
                                            id="wholesale_price" placeholder="00.00">
                                        <small style="color: #e20000" class="error" id="wholesale_price-error"></small>

                                    </div> --}}
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input name="returns_allowed" class="form-check-input" checked
                                                    type="checkbox" id="returns_allowed">
                                                <label class="form-check-label" for="returns_allowed">Returns Allowed
                                                    ?</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="inputProductType" class="form-label">Product Brand</label>
                                            <select name="brand_id" class="form-select" id="inputProductType">
                                                <option>Choose a brand</option>
                                                @foreach ($brands as $item)
                                                    <option value="{{ $item->brand_id }}">{{ $item->brand_name }}</option>
                                                @endforeach
                                            </select>
                                            <small style="color: #e20000" class="error" id="brand_id-error"></small>

                                        </div>
                                        <div class="col-12">
                                            <label for="inputVendor" class="form-label">Product Category</label>
                                            <select class="form-select" id="inputVendor" name="category_id">
                                                <option>Choose a category</option>

                                                @foreach ($categories as $item)
                                                    <option value="{{ $item->category_id }}">{{ $item->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small style="color: #e20000" class="error" id="category_id-error"></small>

                                        </div>
                                        <div class="form-check">
                                            <input name="product_status" class="form-check-input" type="checkbox"
                                                id="gridCheck" checked>
                                            <label class="form-check-label" for="gridCheck">Available</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="hot_deal" class="form-check-input" type="checkbox"
                                                id="gridCheck2">
                                            <label class="form-check-label" for="gridCheck2">Hot Deal</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="featured_product" class="form-check-input" type="checkbox"
                                                id="gridCheck3">
                                            <label class="form-check-label" for="gridCheck3">Featured Product</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="special_offer" class="form-check-input" type="checkbox"
                                                id="gridCheck4">
                                            <label class="form-check-label" for="gridCheck4">Special Offer</label>
                                        </div>
                                        <div class="form-check">
                                            <input name="special_deal" class="form-check-input" type="checkbox"
                                                id="gridCheck5">
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
@endsection


@section('AjaxScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <script>
        document.getElementById('multi_image').addEventListener('change', function(event) {
            console.log(event.target.files); // Log the selected files
        });
    </script>
    <script>
        // $(document).ready(function() {
        //     $('#available_regions').selectpicker();

        //     $('#available_regions').on('shown.bs.select', function() {
        //         var $dropdown = $(this).next('.dropdown-menu');
        //         var offset = $(this).offset();
        //         var windowHeight = $(window).height();
        //         var dropdownHeight = $dropdown.outerHeight();

        //         // Check if the dropdown is going out of the viewport
        //         if (offset.top + $(this).outerHeight() + dropdownHeight > windowHeight) {
        //             $dropdown.css({
        //                 'top': 'auto',
        //                 'bottom': '100%'
        //             });
        //         } else {
        //             $dropdown.css({
        //                 'top': '100%',
        //                 'bottom': 'auto'
        //             });
        //         }
        //     });
        // });
        $('#available_regions').on('change', function() {
            if (this.value == 'global') {
                $(this).options()
            }
            console.log("🚀 ~ $ ~ $(this).options():", $(this).find('option'))
        });

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

        ;
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            let selectedFiles = [];
            let repeaterFormSelectedFiles = [];

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
                preview.innerHTML = '';
                selectedFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
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

            $('#product_form').on('submit', function(event) {
                event.preventDefault();

                var $submitButton = $('button[type="submit"]');
                var $spinner = $submitButton.find('.spinner-border');

                // // Show spinner and disable button
                $spinner.removeClass('d-none');
                $submitButton.prop('disabled', true);
                // Remove errors if the conditions are true
                $('#product_form *').filter(':input.is-invalid').each(function() {
                    this.classList.remove('is-invalid');
                });
                $('#product_form *').filter('.error').each(function() {
                    this.innerHTML = '';
                });

                const formData = new FormData(this);
                selectedFiles.forEach(file => {
                    formData.append('product_images[]', file);
                });

                var inputGroups = document.querySelectorAll('.input-group');
                var productVariations = [];

                inputGroups.forEach(function(group) {
                    var color = group.querySelector('input[name="colors[]"]').value;
                    var size = group.querySelector('input[name="sizes[]"]').value;
                    var width = group.querySelector('input[name="width[]"]').value;
                    var length = group.querySelector('input[name="length[]"]').value;
                    var height = group.querySelector('input[name="height[]"]').value;
                    var weight = group.querySelector('input[name="weight[]"]').value;
                    var price = group.querySelector('input[name="prices[]"]').value;
                    var quantity = group.querySelector('input[name="product_quantity[]"]').value;
                    var files = group.querySelector('input[name="files[]"]').files;
                    var whole_sale_price = group.querySelector('input[name="whole_sale_price[]"]')
                        .value;
                    var product_videos = group.querySelector('input[name="product_video[]"]').files;



                    var variation = {
                        color_name: color,
                        size_name: size,
                        width: width,
                        length: length,
                        height: height,
                        weight: weight,
                        price: price,
                        quantity: quantity,
                        fileIndices: Array.from(files).map((file, fileIndex) => fileIndex),
                        videoIndices: Array.from(product_videos).map((product_videos,
                            product_videosIndex) => product_videosIndex),

                        whole_sale_price: whole_sale_price
                    };

                    productVariations.push(variation);
                });
                formData.append('product_variations', JSON.stringify(productVariations));
                // console.log(formData);
                $.ajax({
                    url: "{{ route('vendor-product-create') }}",
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
                        console.log(response);
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
    <!-- Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap-Select JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <!-- Your custom script -->

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

    <script src="https://cdn.tiny.cloud/1/7oooqgtzrwzitybq53na6ljyfcqa2lq3y0tsf253mx2nfhbq/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea'
        });
    </script>
    <script src="{{ asset('backend_assets') }}/plugins/input-tags/js/tagsinput.js"></script>

    {{-- <script>
        $(document).ready(function() {
            $('#multi_image').on('change', function() { //on file input change
                if (window.File && window.FileReader && window.FileList && window
                    .Blob) //check File API supported browser
                {
                    var data = $(this)[0].files; //this file data

                    $.each(data, function(index, file) { //loop though each file
                        if (/(\.|\/)(gif|jpe?g|png)$/i.test(file
                                .type)) { //check supported file type
                            var fRead = new FileReader(); //new filereader
                            fRead.onload = (function(file) { //trigger function on successful read
                                return function(e) {
                                    var img = $('<img/>').addClass('thumb').attr('src',
                                            e.target.result).width(130)
                                        .height(120); //create image element
                                    $('#preview_img').append(
                                        img); //append image to output element
                                };
                            })(file);
                            fRead.readAsDataURL(file); //URL representing the file's data.
                        }
                    });

                } else {
                    alert("Your browser doesn't support File API!"); //if File API is absent
                }
            });
        });
    </script> --}}

    <script>
        // Initialize Select2 for location search
        $('#ships_from').select2({
            placeholder: 'Search for a location',
            ajax: {
                url: '/search-locations',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(location) {
                            return {
                                id: location.id,
                                text: location.name + ', ' + location.country_code
                            };
                        })
                    };
                },
                cache: true
            }
        });
    </script>
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
