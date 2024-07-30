<div>
    {!! $image_markup !!}
    <br>
    <p>{{ $product->product_short_description }}</p>
    <br>
    <p style="width:100%; position: relative; word-break: break-all;">{!! $product_long_description !!}</p>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td>{{ $product->product_name }} By - {{ $product->vendor->shop_name }}</td>
                <th>SKU</th>
                <td>{{ $product->product_code }}</td>
                <th>Tags</th>
                <td>{{ $product->product_tags }}</td>
            </tr>
            <tr>
                <th>Product Variants</th>
                <td>{{ $product->product_name }}</td>
                <th>Admin Approved</th>
                <td>{{ $product->admin_approved == 1 ? 'Approved' : 'Unapproved' }}</td>
                <th>Returns Allowed</th>
                <td>{{ $product->returns_allowed == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Available Regions</th>
                <td>{{ $product->available_regions }} <b>Origin:</b> <span>{{ $product->origin->name }}</span></td>
                <th>Wholesale Available</th>
                <td>{{ $product->wholesale_available == 1 ? 'Yes' : 'No' }}</td>
                <th>Retail Available</th>
                <td>{{ $product->retail_available == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Wholesale Price</th>
                <td>&euro;{{ $product->wholesale_price }}</td>
                <th>Retail Price</th>
                <td>&euro;{{ $product->product_price }}</td>
                <th>Product Stock Quantity</th>
                <td>{{ $product->product_quantity }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $product->category ? $product->category->category_name : 'N/A' }}</td>
                <th>Brand</th>
                <td>{{ $product->brand ? $product->brand->brand_name : 'N/A' }}</td>
                <th>Weight (Kg)</th>
                <td>{{ $product->weight }}</td>
            </tr>
            <tr>
                <th>Length (cm)</th>
                <td>{{ $product->length }}</td>
                <th>Height (cm)</th>
                <td>{{ $product->height }}</td>
                <th>Width (cm)</th>
                <td>{{ $product->width }}</td>
            </tr>
        </table>
    </div>
    <hr>
    <h5>Modify data</h5>
    <form method="POST" action="{{ route('admin-modify-product', $product->product_id) }}">
        @csrf
        <div class="col-12">
            <label for="inputProductType" class="form-label">Product Brand</label>
            <select name="brand_id" class="form-select" id="inputProductType">
                <option value="">Choose a brand</option>
                {!! $brands_options !!}
            </select>
        </div>
        <div class="col-12">
            <label for="inputVendor" class="form-label">Product Category</label>
            <select class="form-select" id="inputVendor" name="category_id">
                <option value="">Choose a category</option>
                {!! $categories_options !!}
            </select>
        </div>
        <div class="form-check">
            <input name="product_status" class="form-check-input" type="checkbox" id="gridCheck" {{ $product->product_status ? 'checked' : '' }}>
            <label class="form-check-label" for="gridCheck">Available</label>
        </div>
        <div class="form-check">
            <input name="hot_deal" class="form-check-input" type="checkbox" id="gridCheck2" {{ ($product->offers && $product->offers->hot_deal) ? 'checked' : '' }}>
            <label class="form-check-label" for="gridCheck2">Hot Deal</label>
        </div>
        <div class="form-check">
            <input name="featured_product" class="form-check-input" type="checkbox" id="gridCheck3" {{ ($product->offers && $product->offers->featured_product) ? 'checked' : '' }}>
            <label class="form-check-label" for="gridCheck3">Featured Product</label>
        </div>
        <div class="form-check">
            <input name="special_offer" class="form-check-input" type="checkbox" id="gridCheck4" {{ ($product->offers && $product->offers->special_offer) ? 'checked' : '' }}>
            <label class="form-check-label" for="gridCheck4">Special Offer</label>
        </div>
        <div class="form-check">
            <input name="special_deal" class="form-check-input" type="checkbox" id="gridCheck5" {{ ($product->offers && $product->offers->special_deal) ? 'checked' : '' }}>
            <label class="form-check-label" for="gridCheck5">Special Deal</label>
        </div>
        <div class="col-12">
            <input type="submit" class="btn btn-primary" value="Save Product" />
        </div>
    </form>
</div>
