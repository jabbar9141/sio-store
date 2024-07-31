<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\BrandModel;
use App\Models\product\ProductModel;
use App\MyHelpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    use ImageHandlerTrait;

    /**
     * @param BrandRequest $request
     */
    public function brandCreate(BrandRequest $request)
    {

        // dd($request->all());
        // validate
        $data = $request->validated();

        // handling the image
        $image = $request->file('brand_image');
        $data['brand_image'] = $this->handleRequestImage($request->file('brand_image'), 'uploads/images/brand');
        $data['brand_slug'] = $this->getBrandSlug($data['brand_name']);

        // insert
        if (BrandModel::insert($data))
            return response(['msg' => 'Brand is added successfully.'], 200);
        else
            return redirect('brands')->with('error', 'Failed to add this brand, try again.');
    }

    /**
     * @param string $brandName
     * @return array|string|string[]
     */
    private function getBrandSlug(string $brandName)
    {
        return str_replace(' ', '-', strtolower(trim($brandName))) . uniqid('-');
    }

    /**
     * @param Request $request
     */
    public function brandRemove(Request $request)
    {
        try {
            $brand = BrandModel::findOrFail($request->id);
            // MyHelpers::deleteImageFromStorage($brand->brand_image, 'uploads/images/brand/');
            if ($brand->delete())
                return redirect()->route('brand')->with('success', 'Successfully removed.');
            else
                return redirect('brands')->with('error', 'Failed to remove this brand.');
        } catch (ModelNotFoundException $exception) {
            return redirect('brands')->with('error', 'Failed to remove this brand.');
        }
    }

    /**
     * @param BrandRequest $request
     */
    public function brandUpdate(BrandRequest $request)
    {
        // validation
        $data = $request->validated();

        // get the current brand ( which being updated )
        try {
            $brand = BrandModel::findOrFail($request->get('brand_id'));
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('admin-brand')->with('error', 'Something went wrong, try again.');
        }

        // handling if the request has an image
        $newImage = $request->file('brand_image');
        if ($newImage) {
            $data['brand_image'] = $this->handleRequestImage($request->file('brand_image'), 'uploads/images/brand');
            // MyHelpers::deleteImageFromStorage($brand->brand_image, 'uploads/images/brand/');
        }

        // update
        $data['brand_slug'] = $this->getBrandSlug($data['brand_name']);
        if ($brand->update($data))
            return response(['msg' => 'Brand is updated successfully.'], 200);
        else
            return redirect()->route('admin-brand')->with('error', 'Something went wrong, try again.');
    }

    public function showAllBrand(Request $request)
    {
        $brands = BrandModel::paginate(20);
        return view('user.all_brands', ['brands' => $brands]);
    }

    public function showBrand(Request $request, $slug)
    {
        $brand = BrandModel::where('brand_slug', $slug)->first();
        // dd( $brand->products);
        if ($brand) {
            $products = ProductModel::with(['category', 'brand'])->where('product_status', 1)->where('admin_approved', 1)->where('product_quantity', '>', 0)->where('brand_id', $brand->brand_id)->paginate(50);

            return view('user.brand', ['brand' => $brand, 'products' => $products]);
        } else {
            abort(404, 'Category Not Found');
        }
    }
}
