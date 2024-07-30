<form id="updateProducts">

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="start" class="form-label">Enter First Product Number</label><br>
            <input type="number" class="form-control" id="start" name="start" required>
        </div>
        <div class="col-md-6">
            <label for="end" class="form-label">Enter End Product Number</label><br>
            <input type="number" class="form-control" id="end" name="end" required>
            <div class="text-danger mt-1" id="product_error"></div> 
        </div>
       
        {{-- <select class="form-select" id="inputProduct" name="product_id[]" multiple>
            <option disabled>Choose a Product</option>
            @foreach ($products as $product)
                <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
            @endforeach
        </select> --}}
        {{-- <div class="invalid-feedback" id="product_id-error"></div> --}}
    </div>
    {{-- <div class="mb-3">
        <label for="inputProduct" class="form-label">Product</label>
        <select class="form-select" id="inputProduct" name="product_id[]" multiple>
            <option disabled>Choose a Product</option>
            @foreach ($products as $product)
                <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="product_id-error"></div>
    </div> --}}

    <div class="mb-3">
        <label for="inputCategory" class="form-label">Category</label>
        <select class="form-select" id="inputCategory" name="category_id">
            <option selected disabled>Choose a Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="category_id-error"></div>
    </div>

    <div class="mb-3">
        <label for="inputBrand" class="form-label">Brand</label>
        <select class="form-select" id="inputBrand" name="brand_id">
            <option selected disabled>Choose a Brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="brand_id-error"></div>
    </div>
   
    <div class="row m-3">
        <div class="col-md-6 form-check">
            <input name="returns_allowed" class="form-check-input" checked type="checkbox" id="returns_allowed">
            <label class="form-check-label" for="returns_allowed">Returns Allowed
                ?</label>
        </div>
        <div class="col-md-6 form-check">
            <input name="product_status" class="form-check-input" type="checkbox" id="gridCheck" checked>
            <label class="form-check-label" for="gridCheck">Available</label>
        </div>
        <div class="col-md-6 form-check">
            <input name="hot_deal" class="form-check-input" type="checkbox" id="gridCheck2">
            <label class="form-check-label" for="gridCheck2">Hot Deal</label>
        </div>
        <div class="col-md-6 form-check">
            <input name="featured_product" class="form-check-input" type="checkbox" id="gridCheck3">
            <label class="form-check-label" for="gridCheck3">Featured Product</label>
        </div>
        <div class="col-md-6 form-check">
            <input name="special_offer" class="form-check-input" type="checkbox" id="gridCheck4">
            <label class="form-check-label" for="gridCheck4">Special Offer</label>
        </div>
        <div class="col-md-6 form-check">
            <input name="special_deal" class="form-check-input" type="checkbox" id="gridCheck5">
            <label class="form-check-label" for="gridCheck5">Special Deal</label>
        </div>
        
    </div>
   
</form>
<button id="updateFormBrand" class="btn btn-primary " style="margin-left: 40%">Submit</button>