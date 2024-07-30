<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\CategoryModel;
use App\Models\Currency;
use App\Models\product\ProductModel;
use App\MyHelpers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use DB;

class CategoryController extends Controller
{
    use ImageHandlerTrait;

    /**
     * @param CategoryRequest $request
     */
    public function categoryCreate(CategoryRequest $request)
    {
        // validate
        $data = $request->validated();

        // handling the image
        $data['category_image'] = $this->handleRequestImage($request->file('category_image'), 'uploads/images/category');
        $data['category_slug'] = $this->getCategorySlug($data['category_name']);

        // insert
        if (CategoryModel::insert($data))
            return response(['msg' => 'Category is added successfully.'], 200);
        else
            return redirect('categories')->with('error', 'Failed to add this category, try again.');
    }

    /**
     * @param string $categoryName
     * @return array|string|string[]
     */
    private function getCategorySlug(string $categoryName)
    {
        return str_replace(' ', '-', strtolower(trim($categoryName))) . uniqid('-');
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function categoryRemove(Request $request)
    {
        try {
            $category = CategoryModel::findOrFail($request->id);
            // MyHelpers::deleteImageFromStorage($category->category_image, 'uploads/images/category/');
            if ($category->delete())
                return redirect()->route('category')->with('success', 'Successfully removed.');
            else
                return redirect('categories')->with('error', 'Failed to remove this category.');
        } catch (ModelNotFoundException $exception) {
            return redirect('categories')->with('error', 'Failed to remove this category.');
        }
    }

    /**
     * @param CategoryRequest $request
     */
    // public function categoryUpdate(CategoryRequest $request)
    // {

    //     // dd($request->all());
    //     // validation
    //     $data = $request->validated();

    //     // dd($data);

    //     // get the current category ( which being updated )
    //     try {
    //         $category = CategoryModel::findOrFail($request->get('category_id'));
    //     } catch (ModelNotFoundException $exception) {
    //         return redirect()->route('admin-category')->with('error', 'Something went wrong, try again.');
    //     }

    //     // handling if the request has an image
    //     $newImage = $request->file('category_image');
    //     if ($newImage) {

    //         $data['category_image'] = $this->handleRequestImage($newImage, 'uploads/images/category');
    //         // MyHelpers::deleteImageFromStorage($category->category_image, 'uploads/images/category/');
    //     }

    //     // update
    //     $data['category_slug'] = $this->getCategorySlug($data['category_name']);
    //     // echo '<pre>';
    //     // print_r($data);
    //     // die;

    //     $updateValues = [
    //         'category_name' => $data['category_name'],
    //         'category_image' => $data['category_image'],
    //         'category_slug' => $data['category_slug'],
    //     ];
    //     DB::table('category')->where('category_id', $request->category_id)->update($updateValues);

    //     // if ($category->update($data))
    //     return response(['msg' => 'Category is updated successfully.'], 200);
    //     // else
    //     //     return redirect()->route('admin-category')->with('error', 'Something went wrong, try again.');
    // }

    public function categoryUpdate(CategoryRequest $request)
    {
        // Validate the request data
        $data = $request->validated();

        // Attempt to find the category being updated
        try {
            $category = CategoryModel::findOrFail($request->get('category_id'));
        } catch (ModelNotFoundException $exception) {
            // Redirect with an error message if the category is not found
            return redirect()->route('admin-category')->with('error', 'Category not found, try again.');
        }

        // Handle the image upload if a new image is provided
        if ($request->hasFile('category_image')) {
            // Upload the new image and get the path
            $data['category_image'] = $this->handleRequestImage($request->file('category_image'), 'uploads/images/category');

            // Optionally, delete the old image from storage
            // MyHelpers::deleteImageFromStorage($category->category_image, 'uploads/images/category/');
        }

        // Update the category with the validated data
        $category->update($data);

        // Redirect with a success message
        return response(['msg' => 'Category is updated successfully.'], 200);
    }


    public function showAllCategory(Request $request)
    {
        $cats = CategoryModel::paginate(20);
        return view('user.all_categories', ['categories' => $cats]);
    }

    public function showCategory(Request $request, $slug)
    {   
        $cat = CategoryModel::where('category_slug', $slug)->first();
        if ($cat) {
            $products = ProductModel::with(['category', 'brand'])->where('product_status', 1)->where('admin_approved', 1)->where('category_id', $cat->category_id)->paginate(12);
            // dd(  $products);
            return view('user.category', ['category' => $cat, 'products' => $products]);
        } else {
            abort(404, 'Category Not Found');
        }
    }
}
