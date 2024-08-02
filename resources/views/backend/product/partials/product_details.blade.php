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
                <td>Colors: {{ implode(', ', $product->variations->pluck('color_name')->toArray()) }} / Sizes:
                    {{ implode(', ', $product->variations->pluck('size_name')->toArray()) }}</td>
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
                <td>{{ implode(',', $product->variations->pluck('whole_sale_price')->toArray()) }}</td>
                <th>Retail Price</th>
                <td>{{ implode(',', $product->variations->pluck('price')->toArray()) }}</td>
                <th>Product Stock Quantity</th>
                <td>{{ implode(', ', $product->variations->pluck('product_quantity')->toArray()) }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $product->category ? $product->category->category_name : 'N/A' }}</td>
                <th>Brand</th>
                <td>{{ $product->brand ? $product->brand->brand_name : 'N/A' }}</td>
                <th>Weight (Kg)</th>
                <td>{{ implode(',', $product->variations->pluck('weight')->toArray()) }}</td>
            </tr>
            <tr>
                <th>Length (cm)</th>
                <td>{{ implode(',', $product->variations->pluck('length')->toArray()) }}</td>
                <th>Height (cm)</th>
                <td>{{ implode(',', $product->variations->pluck('height')->toArray()) }}</td>
                <th>Width (cm)</th>
                <td>{{ implode(',', $product->variations->pluck('width')->toArray()) }}</td>
            </tr>
        </table>
    </div>
</div>
