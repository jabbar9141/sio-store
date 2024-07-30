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
                <td>{{ $product->admin_approved == 1 ? "Approved" : "Unapproved" }}</td>
                <th>Returns Allowed</th>
                <td>{{ $product->returns_allowed == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Available Regions</th>
                <td>{{ $product->available_regions }} <b>Origin:</b> <span>{{ $product->origin->name }}</span></td>
                <th>Wholesale Available</th>
                <td>{{ $product->wholesale_available == 1 ? "Yes" : "No" }}</td>
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
</div>
