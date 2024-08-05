<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Mail\AddProductEmail;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\Color;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Dimention;
use App\Models\Location;
use App\Models\product\ProductImagesModel;
use App\Models\product\ProductModel;
use App\Models\product\ProductOffersModel;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use App\Models\Size;
use App\Models\User;
use App\MyHelpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use League\Csv\Reader;

use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    private const PRODUCT_AVAILABLE_OFFERS = [
        'hot_deal',
        'featured_product',
        'special_offer',
        'special_deal'
    ];
    private const PRODUCT_IMAGES_PATH = 'uploads/images/product';

    /**
     * @return int
     */
    private function getVendorId(): int
    {
        return  DB::table('vendor_shop')->where('user_id', '=', Auth::id())->first('vendor_id')->vendor_id;
    }

    /**
     * @return View
     */
    public function productAdd()
    {
        $brands = BrandModel::all();
        $categories = CategoryModel::all();
        $colors = Color::all();
        $sizes = Size::all();
        $dimentions = Dimention::all();
        $country = Country::where('name', 'like', (Auth::user()->currency->country ?? 'Italy'))->first();
        $cities = $country->cities;

        return view('backend.product.product_add', compact('brands', 'categories', 'colors', 'sizes', 'dimentions', 'cities'));
    }

    /**
     * @param ProductRequest $request
     */
    public function productCreate(ProductRequest $request)
    {

        set_time_limit(300);
        $data = $request->validated();

        // handling the product thumbnail
        if ($request->has('csv_file')) {
            $this->sioBuyProducts($request);
            return response(['msg' => 'Product is uploading In Progress.'], 200);
        } else {

            $data['product_thumbnail'] =
                MyHelpers::uploadImage($request->file('product_thumbnail'), self::PRODUCT_IMAGES_PATH);

            // handling the vendor id
            $data['vendor_id'] = $this->getVendorId();

            if (null == $data['product_tags']) {
                $data['product_tags']  =  implode(',', explode(' ', $request->get('product_name'))); // Example tags
            }

            // handling the product slug
            $data['product_slug'] = $this->getProductSlug($data['product_name']);

            // status of the product
            $data['product_status'] = $request->get('product_status') ? 1 : 0;
            $data['retail_available'] = $request->get('retail_available') ? 1 : 0;
            $data['wholesale_available'] = $request->get('wholesale_available') ? 1 : 0;
            $data['returns_allowed'] = $request->get('returns_allowed') ? 1 : 0;
            $data['product_quantity'] = array_sum($request->get('product_quantity')) ?? 0;
            $data['product_price'] = 0;
            $data['wholesale_price'] = 0;

            $data['product_colors'] = json_encode([]);

            $data['available_regions'] = json_encode($data['available_regions']);


            // inserting the product
            if ($data['product_images'])
                unset($data['product_images']);

            if ($data['product_variations'])
                unset($data['product_variations']);

            if ($data['files'])
                unset($data['files']);

            $insertedProductId = ProductModel::insertGetId($data);

            if ($insertedProductId) {
                if ($request->has('product_images'))

                    $this->handleProductMultiImages($request->file('product_images'), $insertedProductId);

                $this->handleProductOffers($request, $insertedProductId);

                if ($request->has('product_variations')) {
                    $uploadedImages = [];
                    if (isset($request->files) && count($request->files) > 0) {
                        $files = $request->file('files');
                        foreach ($files as $file) {
                            $uploadedImages[] =  MyHelpers::uploadImage($file, self::PRODUCT_IMAGES_PATH);
                        }
                    }
                    $uploadedVideos = [];
                    if (isset($request->product_video) && count($request->product_video) > 0) {
                        $videos = $request->file('product_video');
                        foreach ($videos as $video) {
                            $uploadedVideos[] =  MyHelpers::uploadImage($video, self::PRODUCT_IMAGES_PATH);
                        }
                    }

                    $productVariations = json_decode($request->input('product_variations'), true);
                    foreach ($productVariations as $productVariation) {
                        if (count($productVariation['fileIndices']) > 0) {
                            $tempArray = [];
                            foreach ($productVariation['fileIndices'] as $count) {
                                $tempArray[] = $uploadedImages[$count];
                            }
                            $uploadedImages = array_slice($uploadedImages, count($tempArray));
                            $jsonEncodedImages = json_encode($tempArray);
                        }

                        if (count($productVariation['videoIndices']) > 0) {
                            $tempVideoLink = [];
                            foreach ($productVariation['videoIndices'] as $count) {
                                $tempVideoLink[] = $uploadedVideos[$count];
                            }
                            $uploadedVideos = array_slice($uploadedVideos, count($tempVideoLink));
                            $jsonEncodedVideo = json_encode($tempVideoLink);
                        }

                        ProductVariation::create([
                            'product_id' => $insertedProductId,
                            'color_id' => 0,
                            'size_id' => 0,
                            'dimention_id' => 0,
                            'size_name' => $productVariation['size_name'],
                            'color_name' => $productVariation['color_name'],
                            'width' => $productVariation['width'],
                            'height' => $productVariation['height'],
                            'length' => $productVariation['length'],
                            'weight' => $productVariation['weight'],
                            'price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $productVariation['price']),
                            'product_quantity' => $productVariation['quantity'],
                            'whole_sale_price' =>  MyHelpers::toEuro(Auth::user()?->currency_id, (float) $productVariation['whole_sale_price']),
                            'image_url' => $jsonEncodedImages ?? null,
                            'video_url' => $jsonEncodedVideo ?? null
                        ]);
                    }
                }

                $users = User::where('status', true)->get();

                $total_quantity = 0;
                $total_wholesale = 0;
                $total_price = 0;

                $product = ProductModel::find($insertedProductId);
                foreach ($product->variations as $key => $variation) {
                    $total_quantity += $variation->product_quantity ?? 0;
                    $total_price += $variation->price * $variation->product_quantity ?? 0;
                    $total_wholesale += $variation->whole_sale_price * $variation->product_quantity ?? 0;
                }

                $product->update([
                    'total_variation_quantity' => $total_quantity,
                    'total_variation_whole_sale_price' => $total_wholesale,
                    'total_variation_price' => $total_price,
                ]);
                $products = ProductModel::where('vendor_id', $this->getVendorId())
                    ->orderBy('product_id', 'desc')
                    ->limit(12)
                    ->get();
                foreach ($users as $user) {
                    Mail::to($user->email)->send(new AddProductEmail($product, $products));
                }
                Mail::to('support@siostore.eu')->send(new AddProductEmail($product, $products));

                return response(['msg' => 'Product is added successfully.'], 200);
            } else return redirect('add_product')->with('error', 'Failed to add this product, try again.');
        }
    }

    public function massInsertProducts(Request $request)
    {
        set_time_limit(600);
        try {
            // dd($request->all());
            // Validate the request to ensure an upload occurred
            $request->validate([
                'xml_file' => 'required|file|mimes:xml',
            ]);

            // Retrieve the file
            $file = $request->file('xml_file');

            // dd($file);
            // Get the real path to the temporary uploaded file
            $path = $file->getRealPath();

            // Load the XML file
            $xml = simplexml_load_file($path);
            // dd($xml);
            if ($xml === false) {
                return back()->with(['error' => 'Failed to parse the XML file.']);
            }
            $products = [];
            DB::beginTransaction();
            foreach ($xml->product as $product) {
                // dd((string)$product->Images->Image[0]);
                $wholesalePrice = (float)$product->WH_Price + ((float)$product->WH_Price * 0.015);
                $retailPrice = $wholesalePrice + ($wholesalePrice * 0.03);
                $productData = [
                    'product_name' => (string)$product->Name,
                    'product_code' => (string)$product->SKU,
                    'product_tags' => implode(',', explode(' ', (string)$product->Name)), // Example tags
                    'product_colors' => json_encode([]), // As color is not specified in XML
                    'admin_approved' => 0, // Example value, could be managed via an approval system
                    'returns_allowed' => 1,
                    'available_regions' => json_encode(["global"]), // Default to global if not specified
                    'wholesale_available' => 1,
                    'retail_available' => 1,
                    // 'wholesale_price' => $wholesalePrice,
                    'wholesale_price' => MyHelpers::toEuro(Auth::user()?->currency_id, $wholesalePrice),

                    'product_short_description' => htmlentities(substr((string)$product->description, 0, 120), ENT_QUOTES, 'UTF-8'),
                    'product_long_description' => htmlentities((string)$product->description, ENT_QUOTES, 'UTF-8'),
                    'product_slug' => $this->getProductSlug((string)$product->Name),
                    'product_price' => MyHelpers::toEuro(Auth::user()?->currency_id, $retailPrice),

                    'product_thumbnail' => $this->uploadImageFromURL((string)$product->Images->Image[0], self::PRODUCT_IMAGES_PATH),
                    'product_status' => 1, // Assuming all products are active by default
                    'category_id' => 1, // Assuming category mapping is handled elsewhere or not needed
                    'sub_category_id' => null,
                    'brand_id' => 1, // Assuming a default or dummy brand
                    'vendor_id' => $this->getVendorId(),
                    'length' => 1.00,
                    'weight' => 1.00,
                    'height' => 1.00,
                    'width' => 1.00,
                    'ships_from' => 2,
                    'product_quantity' => (int)$product->Stock,
                ];

                $products[] = $productData;
                $insertedProductId = ProductModel::insertGetId($productData);
                $img_data = [];
                $img_data['image_product_id'] = $insertedProductId;
                foreach ($product->Images->Image as $image) {
                    // dd($image);
                    $img_data['product_image'] = $this->uploadImageFromURL((string)$image, self::PRODUCT_IMAGES_PATH);
                    ProductImagesModel::insert($img_data);
                }
            }
            DB::commit();
            return response(['msg' => 'Products have been successfully added.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to upload products, try again.');
        }
    }


    public function massInsertProductsFromCSV(Request $request)
    {
        set_time_limit(6000);  // Extend execution time


        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);


        // If a new file is uploaded,
        $csvPath = $request->file('csv_file')->getRealPath();


        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $batchSize = 1000;
        $currentPosition = Session::get('csv_process_position', 0); // Default to 0 if not set


        DB::beginTransaction();
        try {
            $counter = 0;
            foreach ($records as $index => $record) {
                if ($index < $currentPosition) {
                    continue; // Skip records until we reach the last processed position
                }

                if ($counter >= $batchSize) {
                    // Save progress and break the loop
                    DB::commit();
                    Session::put('csv_process_position', $index);
                    Session::put('csv_file_path', $csvPath);
                    return response(['msg' => "Processed $batchSize records. More to go..." . $currentPosition], 200);
                }

                $brandName = strtok($record['Name'], ' ');  // Get the first word in the product name as brand
                $brand = BrandModel::firstOrCreate(['brand_name' => $brandName], ['brand_image' => null, 'brand_slug' => $this->getProductSlug($brandName)]);

                $wholesalePrice = (float)$record['Pret (vat excl)'] * 1.015;  // Add 1.5%
                $retailPrice = $wholesalePrice * 1.03;  // Add 3%

                $productData = [
                    'product_name' => $record['Name'],
                    'product_code' => $record['SKU'],
                    'product_tags' => implode(',', explode(' ', $record['Name'])), // Example tags
                    'product_colors' => json_encode([]),
                    'admin_approved' => 0,
                    'returns_allowed' => 1,
                    'available_regions' => json_encode(["global"]),
                    'wholesale_available' => 1,
                    'retail_available' => 1,
                    // 'wholesale_price' => $wholesalePrice,
                    'wholesale_price' => MyHelpers::toEuro(Auth::user()?->currency_id, $wholesalePrice),

                    'product_short_description' => htmlentities(substr($record['Name'], 0, 120), ENT_QUOTES, 'UTF-8'),
                    'product_long_description' => htmlentities($record['Name'], ENT_QUOTES, 'UTF-8'),
                    'product_slug' => $this->getProductSlug($record['Name']),
                    'product_price' => MyHelpers::toEuro(Auth::user()?->currency_id, $retailPrice),

                    'product_thumbnail' => isset($record['Images']) ? $this->uploadImageFromURL(explode(';', $record['Images'])[0], self::PRODUCT_IMAGES_PATH) : '',
                    'product_status' => $record['Availability'] == 'Activ' ? 1 : 0,
                    'category_id' => 1,  // Assuming a default category
                    'sub_category_id' => null,
                    'brand_id' => $brand->brand_id,
                    'vendor_id' => $this->getVendorId(),
                    'length' => 1.00,
                    'weight' => 1.00,
                    'height' => 1.00,
                    'width' => 1.00,
                    'ships_from' => 2,
                    'product_quantity' => (int)$record['Stock'],
                ];
                $insertedProductId = ProductModel::insertGetId($productData);
                $img_data = [];
                $img_data['image_product_id'] = $insertedProductId;
                if ($record['Images'] && '' != $record['Images']) {
                    foreach (explode(';', $record['Images']) as $image) {
                        $img_data['product_image'] = $this->uploadImageFromURL($image, self::PRODUCT_IMAGES_PATH);
                        ProductImagesModel::insert($img_data);
                    }
                }

                $counter++;
            }

            // Commit any remaining transactions
            DB::commit();
            Session::forget('csv_process_position');
            Session::forget('csv_file_path');
            return response(['msg' => 'Products have been successfully added.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to upload products, try again.');
        }
    }


    function uploadImageFromURL($url, $path = self::PRODUCT_IMAGES_PATH)
    {
        try {
            $client = new Client();
            if ($url && is_string($url)) {
                $response = $client->get($url);

                // Check if the request was successful
                if ($response->getStatusCode() == 200) {
                    // Get the content of the response
                    $imageContent = $response->getBody()->getContents();
                    $contentType = $response->getHeader('Content-Type')[0];
                    $extension = substr($contentType, strpos($contentType, '/') + 1);
                    $filename = Str::random(40) . '.' . $extension;
                    $tempFilePath = sys_get_temp_dir() . '/' . $filename;

                    // Save the temporary file
                    file_put_contents($tempFilePath, $imageContent);

                    // Create an UploadedFile object
                    $uploadedFile = new UploadedFile(
                        $tempFilePath,
                        $filename,
                        $contentType,
                        null,
                        true // Setting test to true enables us to bypass the is_uploaded_file() PHP function
                    );
                    $product_thumbnail = MyHelpers::uploadImage($uploadedFile, $path);
                    return $product_thumbnail;
                }
            } else {
                // dd($url);
                return '';
            }


            return ''; // Return empty string if the response status code is not 200
        } catch (\Exception $e) {
            // Log::error($e->getMessage(), [$e]);
            return '';
        }
    }



    /**
     * @param string $productName
     * @return array|string|string[]
     */
    private function getProductSlug(string $productName)
    {
        return str_replace(' ', '-', strtolower(trim($productName))) . uniqid('-');
    }

    /**
     * @param array $images
     * @param int $productId
     * @return void
     */
    private function handleProductMultiImages(array $images, int $productId): void
    {
        $data['image_product_id'] = $productId;
        foreach ($images as $image) {
            $data['product_image'] = MyHelpers::uploadImage($image, self::PRODUCT_IMAGES_PATH);
            ProductImagesModel::insert($data);
        }
    }

    /**
     * @param Request $requestData
     * @param int $productId
     * @param bool $editCase
     * @return void
     */
    private function handleProductOffers(Request &$requestData, int $productId, bool $editCase = false): void
    {
        $offers['offer_product_id'] = $productId;
        foreach (self::PRODUCT_AVAILABLE_OFFERS as $offerName) {
            $offers[$offerName] = ($requestData->get($offerName)) != null ? 1 : 0;
        }
        if ($editCase) {

            try {
                ProductOffersModel::firstOrFail()
                    ->where('offer_product_id', $productId)->update($offers);
            } catch (ModelNotFoundException $exception) {
            }
        } else ProductOffersModel::insert($offers);
    }

    /**
     * @param int $productId
     * @return mixed
     */
    public static function getProductImages(int $productId)
    {
        return ProductImagesModel::where('image_product_id', '=', $productId)->get('product_image');
    }

    /**
     * @param string $tags
     * @return arraystok
     */
    public static function getProductSeparatedTags(string $tags): array
    {
        if ($tags)
            return explode(',', $tags);
        return [];
    }

    /**
     * @param string $colors
     * @return array
     */
    public static function getProductSeparatedColors(string $colors): array
    {
        if ($colors)
            return explode(',', $colors);
        return [];
    }

    /**
     * @param Request $request
     */
    public function productRemove(Request $request, $id)
    {
        $productId = $request->id ?? $id;
        $images = self::getProductImages($productId);

        try {
            $product = ProductModel::findOrFail($productId);

            // Delete related records in product_offers
            DB::table('product_offers')->where('offer_product_id', $productId)->delete();

            if ($product->delete()) {
                // Removing the thumbnail
                MyHelpers::deleteImageFromStorage($product->product_thumbnail, self::PRODUCT_IMAGES_PATH . '/');

                // Removing images
                foreach ($images as $item) {
                    MyHelpers::deleteImageFromStorage($item->product_image, self::PRODUCT_IMAGES_PATH . '/');
                }

                // return redirect('vendor/products')->with('success', 'Removed Successfully.');
                return response([
                    'success' => true,
                    'msg' => 'Removed Successfully.'
                ], 200);
            } else {
                return response([
                    'success' => false,
                    'msg' => 'Failed to remove this product.'
                ], 404);
                // return redirect('vendor/products')->with('error', 'Failed to remove this product.');
            }
        } catch (ModelNotFoundException $exception) {
            return response([
                'success' => false,
                'msg' => 'Failed to remove this product.'
            ], 404);
            // return redirect('vendor/products')->with('error', 'Failed to remove this product.');
        } catch (\Exception $exception) {
            return response([
                'success' => false,
                'msg' => 'Failed to remove this product.'
            ], 404);
            // return redirect('vendor/products')->with('error', 'An unexpected error occurred.');
        }
    }


    /**
     * @param int $productId
     */
    public function productEdit(int $productId)
    {

        try {
            $data = ProductModel::findOrFail($productId);
            // $data = DB::table('get_product_data')
            //     ->where('offer_product_id', '=', $productId)->get()[0];
            // dd($data);
            $brands = BrandModel::all();
            $categories = CategoryModel::all();
            $productImages = ProductImagesModel::where('image_product_id', $productId)->get();
            $categories = CategoryModel::all();
            $colors = Color::all();
            $sizes = Size::all();
            $dimentions = Dimention::all();
            return view('backend.product.product_edit', compact('data', 'brands', 'categories', 'productImages', 'colors', 'sizes', 'dimentions'));
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('vendor-product')->with('error', 'Failed, try again later.');
        }
    }

    /**
     * @param ProductRequest $request
     */
    public function productUpdate(ProductRequest $request)
    {
        // dd($request->all());
        $data = $request->validated();
        $product_id = $request->get('product_id');

        // getting the old data
        try {
            $oldProduct = ProductModel::findOrFail($product_id);
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('vendor-product-edit')->with('error', 'Something went wrong, try again.');
        }

        if (null == $data['product_tags']) {
            $data['product_tags']  =  implode(',', explode(' ', $request->get('product_name'))); // Example tags
        }

        // handling the product thumbnail
        if ($request->file('product_thumbnail')) {
            $data['product_thumbnail'] = MyHelpers::uploadImage(
                $request->file('product_thumbnail'),
                self::PRODUCT_IMAGES_PATH
            );

            // removing the old image from uploads directory
            MyHelpers::deleteImageFromStorage($oldProduct->product_thumbnail, self::PRODUCT_IMAGES_PATH .  '/');
        }


        // handling the vendor id
        $data['vendor_id'] = $this->getVendorId();

        // handling the product slug
        $data['product_slug'] = $this->getProductSlug($data['product_name']);

        // status of the product
        $data['product_status'] = $request->get('product_status') ? 1 : 0;
        $data['retail_available'] = $request->get('retail_available') ? 1 : 0;
        $data['wholesale_available'] = $request->get('wholesale_available') ? 1 : 0;
        $data['returns_allowed'] = $request->get('returns_allowed') ? 1 : 0;
        $data['variation_names'] = $request->get('variation_names') ? 1 : 0;
        $data['variation_values'] = $request->get('variation_values') ? 1 : 0;
        $data['product_quantity'] = array_sum($request->get('product_quantity')) ?? 0;


        $r = [];
        // if ($request->has('variation_names') && $request->has('variation_values')) {
        //     if (
        //         $request->get('variation_names') &&
        //         isset($data['variation_names']) &&
        //         isset($data['variation_values']) &&
        //         is_array($data['variation_names']) &&
        //         is_array($data['variation_values']) &&
        //         count($data['variation_names']) == count($data['variation_values'])
        //     ) {
        //         for ($i = 0; $i < count($data['variation_names']); $i++) {
        //             array_push(
        //                 $r,
        //                 [
        //                     'variation_name' => $data['variation_names'][$i] ?? 'Default',
        //                     'variation_values' => $data['variation_values'][$i] ??  'Default',
        //                 ]
        //             );
        //         }
        //     }
        // }

        $data['product_colors'] = json_encode($r);
        $data['available_regions'] = json_encode($data['available_regions']);

        $data['product_colors'] = json_encode([]);

        $data['available_regions'] = json_encode($data['available_regions']);

        // if ($request->hasFile('product_video')) {
        //     $file = $request->file('product_video');
        //     $extension = $file->getClientOriginalExtension();
        //     $encryptedName = $file->getClientOriginalName() . time() . rand(1, 9);
        //     $fileName = $encryptedName . '.' . $extension;
        //     $file->move(self::PRODUCT_IMAGES_PATH, $fileName);
        //     $destinationFolder = str_replace('siostore/siostore/public/', 'siostore/public_html/', self::PRODUCT_IMAGES_PATH);
        //     $destinationFileName = $destinationFolder . '/' . $fileName;
        //     copy(self::PRODUCT_IMAGES_PATH . '/' . $fileName, $destinationFileName);
        //     $data['video_link'] = $fileName;

        //     unset($data['product_video']);
        // }
        // inserting the product
        // if ($data['product_images'])
        //     unset($data['product_images']);

        // if ($data['colors'])
        //     unset($data['colors']);
        // if ($data['sizes'])
        //     unset($data['sizes']);
        // if ($data['width'])
        //     unset($data['width']);


        // if ($data['length'])
        //     unset($data['length']);
        // if ($data['weight'])
        //     unset($data['weight']);
        // if ($data['height'])
        //     unset($data['height']);

        // if ($data['prices'])
        //     unset($data['prices']);
        if (isset($data['files']))
            unset($data['files']);

        if (isset($data['product_variations']))
            unset($data['product_variations']);

        // // inserting the product
        if (isset($data['product_images'])) {
            unset($data['product_images']);
        }

        if (isset($data['variation_names'])) {
            unset($data['variation_names']);
        }

        if (isset($data['variation_values'])) {
            unset($data['variation_values']);
        }

        // Check if the key still exists
        // dd(array_key_exists('variation_values', $data)); // This should return false

        if ($oldProduct->update($data)) {
            // handling the product images
            if ($request->file('product_images')) {
                // removing the old images
                $oldImages = ProductImagesModel::where('image_product_id', '=', $product_id)->get();
                foreach ($oldImages as $item)
                    MyHelpers::deleteImageFromStorage($item->product_image, self::PRODUCT_IMAGES_PATH . '/');

                ProductImagesModel::where('image_product_id', '=', $product_id)->delete();

                // inserting the new images
                $this->handleProductMultiImages($request->file('product_images'), $product_id);
            }

            // handling the product offers
            $this->handleProductOffers($request, $product_id, true);


            if ($request->has('product_variations')) {
                $uploadedImages = [];
                if (isset($request->files) && count($request->files) > 0) {
                    $files = $request->file('files');
                    foreach ($files as $file) {
                        $uploadedImages[] =  MyHelpers::uploadImage($file, self::PRODUCT_IMAGES_PATH);
                    }
                }

                $uploadedVideos = [];
                if (isset($request->product_video) && count($request->product_video) > 0) {
                    $videos = $request->file('product_video');
                    foreach ($videos as $video) {
                        $uploadedVideos[] =  MyHelpers::uploadImage($video, self::PRODUCT_IMAGES_PATH);
                    }
                }

                $productVariations = json_decode($request->input('product_variations'), true);
                foreach ($productVariations as $productVariation) {
                    $variationResult = [
                        'product_id' => $product_id,
                        'color_id' => 0,
                        'size_id' => 0,
                        'dimention_id' => 0,
                        'size_name' => $productVariation['size_id'],
                        'color_name' => $productVariation['color_id'],
                        'width' => $productVariation['width'],
                        'height' => $productVariation['height'],
                        'length' => $productVariation['length'],
                        'weight' => $productVariation['weight'],
                        'product_quantity' => $productVariation['product_quantity'],
                        'price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $productVariation['price']),
                        'whole_sale_price' =>  MyHelpers::toEuro(Auth::user()?->currency_id, (float) $productVariation['whole_sale_price']),

                    ];

                    if (count($productVariation['fileIndices']) > 0) {
                        $tempArray = [];
                        foreach ($productVariation['fileIndices'] as $count) {
                            $tempArray[] = $uploadedImages[$count];
                        }
                        $uploadedImages = array_slice($uploadedImages, count($tempArray));
                        $jsonEncodedImages = json_encode($tempArray);

                        $variationResult['image_url'] = $jsonEncodedImages;
                    }

                    if (count($productVariation['videoIndices']) > 0) {
                        $tempVideoLink = [];
                        foreach ($productVariation['videoIndices'] as $count) {
                            $tempVideoLink[] = $uploadedVideos[$count];
                        }
                        $uploadedVideos = array_slice($uploadedVideos, count($tempVideoLink));
                        $jsonEncodedVideo = json_encode($tempVideoLink);
                        $variationResult['video_url'] = $jsonEncodedVideo;
                    }

                    if ($productVariation['variation_id'] != '') {
                        $prodVariation = ProductVariation::where('id', $productVariation['variation_id'])->first();
                        $oldQuantity = $prodVariation->product_quantity;

                        if ($oldQuantity > (int)$variationResult['product_quantity']) {
                            $newQuantity = $oldQuantity - $variationResult['product_quantity'];
                            $newPrice = $newQuantity * $prodVariation->price;
                            $newWholeSale = $newQuantity * $prodVariation->whole_sale_price;

                            $prodVariation->product->update([
                                'total_variation_quantity' => $prodVariation->product->total_variation_quantity - $newQuantity,
                                'total_variation_price' =>  $prodVariation->product->total_variation_price - $newPrice,
                                'total_variation_whole_sale_price' => $prodVariation->product->total_variation_whole_sale_price - $newWholeSale,
                            ]);
                        } elseif ($oldQuantity < (int)$variationResult['product_quantity']) {
                            $newQuantity = $variationResult['product_quantity'] - $oldQuantity;
                            $newPrice = $newQuantity * $variationResult['price'];
                            $newWholeSale = $newQuantity * $variationResult['whole_sale_price'];

                            $prodVariation->product->update([
                                'total_variation_quantity' => $prodVariation->product->total_variation_quantity + $newQuantity,
                                'total_variation_price' =>  $prodVariation->product->total_variation_price + $newPrice,
                                'total_variation_whole_sale_price' => $prodVariation->product->total_variation_whole_sale_price + $newWholeSale,
                            ]);
                        } else {
                            $oldVariationPrice = $prodVariation->price;
                            $oldVariationWholeSalePrice = $prodVariation->whole_sale_price;

                            $getOldTotalPrice = $prodVariation->product->total_variation_price - ($oldVariationPrice * $oldQuantity);
                            $getOldTotalWholeSalePrice = $prodVariation->product->total_variation_whole_sale_price - ($oldVariationWholeSalePrice * $oldQuantity);

                            $newTotalPrice = $variationResult['price'] * $oldQuantity;
                            $newTotalWholeSalePrice = $variationResult['whole_sale_price'] * $oldQuantity;

                            $prodVariation->product->update([
                                'total_variation_price' =>  $getOldTotalPrice + $newTotalPrice,
                                'total_variation_whole_sale_price' => $getOldTotalWholeSalePrice + $newTotalWholeSalePrice,
                            ]);
                        }

                        $prodVariation->update($variationResult);
                    } else {
                        ProductVariation::create($variationResult);
                    }
                }
            }

            return response(['msg' => 'Product is updated successfully.'], 200);
        } else return redirect('update_product')->with('error', 'Failed to update this product, try again.');
    }

    /**
     * @param Request $request
     */
    public function productActivate(Request $request)
    {
        $product_id = $request->product_id;

        // check whether activate or de-activate
        if ($request->current_status == "1") {
            return $this->productDeActivate($product_id);
        }

        try {
            $product = ProductModel::findOrFail($product_id);

            foreach ($product->variations as $key => $item) {
                if (is_null($item->whole_sale_price) || is_null($item->weight)) {
                    return back()->with('error', 'Product Wholesale Price and Weight is required to Approve !');
                }
            }
            $product->update(['product_status' => 1]);
            return response(['msg' => 'Product now is active.'], 200);
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('vendor-product')->with('error', 'Failed to activate this product, try again');
        }
    }

    /**
     * @param int $productId
     */
    public function productDeActivate(int $productId)
    {
        try {
            ProductModel::findOrFail($productId)->update(['product_status' => 0]);
            return response(['msg' => 'Product now is disabled.'], 200);
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('vendor-product')->with('error', 'Failed to activate this product, try again');
        }
    }

    /**
     * estimate product shipping cost
     */
    public static function estimate_shipping($data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get(env('SHIPPING_API_URL'), [
            "width" => $data['width'],
            "height" => $data['height'],
            "weight" => $data['weight'],
            "length" => $data['length'],
            "count" => $data['count'],
            "item_desc" => $data['item_desc'],
            "item_value" => $data['item_value'],
            "origin_city" => $data['origin_city'],
            "dest_city" => $data['dest_city'],
            "origin_zip" => $data['origin_zip'],
            "dest_zip" => $data['dest_zip'],
            "origin_country" => $data['origin_country'],
            "dest_country" => $data['dest_country']
        ]);

        if ($response->successful()) {
            return ($response->body());
        } else {
            Log::info($response->body(), [$response]);
            return false;
        }
    }


    /**
     * To get the products of the current authenticated vendor/shop
     */
    public function getProducts()
    {
        return view('backend.product.product_default');
    }

    public function productList()
    {
        $currentVendorId = DB::table('vendor_shop')
            ->where('user_id', Auth::id())->value('vendor_id');

        $items = ProductModel::where('vendor_id', $currentVendorId)->orderBy('product_id', 'desc')->get();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('action', function ($item) {
                $url = route('vendor-product-edit', $item->product_id);
                $editBtn = "<a class='btn btn-sm btn-primary' href='$url'>Edit</a>";
                $deleteBtn = "<a class='btn btn-sm btn-danger btn-delete ms-2' data-product-id='' onclick='deleteProduct(" . $item->product_id . ")'>Delete</a>";
                $checkBox = '<input class="ms-2 deleteProduct" type="checkbox" name="product_id[]" value="' . $item->product_id . '">';

                return '<div class="d-flex">' . $editBtn . $deleteBtn . $checkBox . '</div>';
            })
            ->editColumn('product_thumbnail', function ($item) {
                $img_url = $item->product_thumbnail
                    ? url('/uploads/images/product/' . $item->product_thumbnail)
                    : url('/uploads/images/user_default_image.png');
                return "<img src='$img_url' width='50px'>";
            })
            ->addColumn('details', function ($item) {
                return '<button type="button" class="btn btn-primary btn-sm btn-see-details" data-product-id="' . $item->product_id . '">
                            See Details
                        </button>';
            })
            ->editColumn('product_status', function ($item) {
                return $item->product_status == 1
                    ? "<span class='badge bg-success'>Published</span>"
                    : "<span class='badge bg-secondary'>Unpublished</span>";
            })
            ->editColumn('admin_approved', function ($item) {
                return $item->admin_approved == 1
                    ? "<span class='badge bg-success'>Approved</span>"
                    : "<span class='badge bg-secondary'>Unapproved</span>";
            })
            ->rawColumns(['action', 'product_thumbnail', 'details', 'product_status', 'admin_approved'])
            ->make(true);
    }

    public function getProductDetails($id)
    {
        $product = ProductModel::with(['vendor', 'origin', 'category', 'brand', 'images'])->find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $image_markup = '<div class="row">';
        foreach ($product->images as $image) {
            $image_markup .= "<div class='col-sm-6'>
                <img src='/uploads/images/product/{$image->product_image}' style='width:80%; margin: 10px;'>
            </div>";
        }
        $image_markup .= "</div>";

        $words = explode(' ', $product->product_long_description);
        $chunks = array_chunk($words, 30);
        $product_long_description = '';
        foreach ($chunks as $chunk) {
            $product_long_description .= implode(' ', $chunk) . '<br>';
        }

        return view('backend.product.partials.product_details', compact('product', 'image_markup', 'product_long_description'))->render();
    }



    public function showProduct(Request $request, $slug)
    {
        $product = ProductModel::where('product_slug', $slug)->where('admin_approved', true)->where('product_status', true)->whereHas('variations', function ($q) {
            $q->where('product_quantity', '>', 0);
        })->first();
        if ($product) {
            $r = Location::find(1);
            if (session('ship_to') == null) {
                session(['ship_to' => 1, 'ship_to_str' => $r->name . ', ' . $r->country_code]);
            }

            if (null != $product->ships_from) {
                $o = Location::find($product->ships_from);
                $d = Location::find(session('ship_to') ?? 1);
                $shipping_data = [
                    "width" => $product->width,
                    "height" => $product->height,
                    "weight" => $product->weight,
                    "length" => $product->length,
                    "count" => 1,
                    "item_desc" => substr($product->product_name, 0, 24),
                    "item_value" => $product->product_price,
                    "origin_city" => $o->name,
                    "dest_city" => $d->name,
                    "origin_zip" => $o->zip,
                    "dest_zip" => $d->zip,
                    "origin_country" => $o->country_code,
                    "dest_country" => $d->country_code
                ];

                // dd($shipping_data);

                // $shipping_cost = self::estimate_shipping($shipping_data);
                $shipping_cost = null;
                if (!$shipping_cost) {
                    $shipping_cost = null;
                }
            } else {
                $shipping_cost = null;
            }
            $similar = ProductModel::where('admin_approved', true)->where('product_status', true)->withWhereHas('variations', function ($q) {
                $q->where('product_quantity', '>', 0);
            })
                ->where(function ($query) use ($product) {
                    $query->where('product_name', 'like', '%' . $product->product_name . '%')
                        ->orWhere('product_short_description', 'like', '%' . $product->product_short_description . '%')
                        ->orWhere('product_long_description', 'like', '%' . $product->product_long_description . '%')
                        ->orWhere('category_id', $product->category_id);
                })
                ->where('product_id', '!=', $product->product_id)
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get();

            $reviews = ProductReview::where('product_id', $product->product_id)->paginate(20);

            // $sizes = Size::get();
            // $colors = Color::get();
            // $dimentions = Dimention::all();
            $productVariations = ProductVariation::where('product_id', $product->product_id)->get();
            $variaationSizes = [];
            foreach ($productVariations as $productVariation) {
                $variaationSizes = array_merge($variaationSizes, explode(',', $productVariation->size_name));
            }
            $sizes  = array_unique($variaationSizes);

            return view('user.product', compact('product', 'similar', 'shipping_cost', 'reviews', 'sizes'));
        } else {
            abort(404, 'Product not found');
        }
    }

    public function searchProducts(Request $request)
    {
        $query = ProductModel::query()->where('admin_approved', true)->where('product_status', true)->whereHas('variations', function ($q) {
            $q->where('product_quantity', '>', 0);
        });

        // Search by product name, short description, and long description
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($subquery) use ($keyword) {
                $subquery->where('product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('product_short_description', 'like', '%' . $keyword . '%')
                    ->orWhere('product_long_description', 'like', '%' . $keyword . '%');
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('product_price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('product_price', '<=', $request->input('max_price'));
        }

        // Filter by categories
        if ($request->filled('categories')) {
            $categories = $request->input('categories');

            // Ensure $categories is always an array
            if (!is_array($categories)) {
                $categories = [$categories];
            }

            $query->whereIn('category_id', $categories);
        }

        // Filter by brands
        if ($request->filled('brands')) {
            $brands = $request->input('brands');

            // Ensure $brands is always an array
            if (!is_array($brands)) {
                $brands = [$brands];
            }

            $query->whereIn('brand_id', $brands);
        }

        // Sort the results
        $sort_by = $request->input('sort_by');
        switch ($sort_by) {
            case 'latest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'price_asc':
                $query->orderBy('product_price', 'ASC');
                break;
            case 'price_desc':
                $query->orderBy('product_price', 'DESC');
                break;
        }

        // Paginate the results
        $products = $query->paginate(15);

        // Return the view with the products
        return view('user.products', ['products' => $products]);
    }

    public function getVariationDetails(Request $request)
    {
        $currency_id =  session('currency_id', Currency::where('status', true)->first()?->id ?? 0);
        $product = ProductModel::find($request->product_id);
        $query = ProductVariation::query();
        $query->where('product_id', $request->product_id);
        if ($request->filled('color_name')) {
            $query->where('color_name', $request->color_name);
        }

        if ($request->filled('width')) {
            $query->where('width', $request->width);
        }

        if ($request->filled('height')) {
            $query->where('height', $request->height);
        }

        if ($request->filled('length')) {
            $query->where('length', $request->length);
        }

        if ($request->filled('weight')) {
            $query->where('weight', $request->weight);
        }
        if ($request->filled('size_name')) {
            $productVariations = $query->get();
            foreach ($productVariations as $productVariation) {
                $sizesArray = explode(',', $productVariation->size_name);

                if (in_array($request->size_name, $sizesArray)) { {
                        return response()->json([
                            'success' => true,
                            'product_variation' => $productVariation,
                            'product_images' => count(json_decode($productVariation->image_url)) > 0 ? json_decode($productVariation->image_url) : $product?->product_thumbnail,
                            'video_url' => json_decode($productVariation->video_url),
                        ]);
                    }
                }
            }
        }
        $productVariation = $query->first();

        if ($productVariation) {
            return response()->json([
                'success' => true,
                'product_variation' => $productVariation,
                'product_images' => count(json_decode($productVariation->image_url)) > 0 ? json_decode($productVariation->image_url) : $product?->product_thumbnail,
                'video_url' => json_decode($productVariation->video_url),
                'formatedPrice' => MyHelpers::fromEuroView($currency_id, $productVariation->price)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'product_variation' => null
            ]);
        }
    }


    public function koffFeedProducts()
    {
        $products = ProductModel::where('vendor_id', 16)->get();
        if (count($products) > 0) {
            foreach ($products as $product) {
                DB::table('product_offers')->where('offer_product_id', $product->product_id)->delete();
                if ($product->delete()) {
                    MyHelpers::deleteImageFromStorage($product->product_thumbnail, self::PRODUCT_IMAGES_PATH . '/');
                    $images = self::getProductImages($product->product_id);

                    foreach ($images as $item) {
                        MyHelpers::deleteImageFromStorage($item->product_image, self::PRODUCT_IMAGES_PATH . '/');
                    }
                }
            }
        }
        return response([
            'success' => true,
            'msg' => 'Products deleted successfully.'
        ], 200);
    }



    public function getVendorProductsToBulkUpdate()
    {
        $vendor_id = $this->getVendorId();
        $products = ProductModel::where('vendor_id', $vendor_id)->get();
        return $products;
    }



    public function updateProducts(Request $request)
    {
        $vendor_id = $this->getVendorId();
        // $products = ProductModel::where('vendor_id', $vendor_id)->where('update_category_or_brand',false)->get();
        $categories = CategoryModel::all();
        $brands = BrandModel::all();

        $data = [
            // 'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ];

        $form = view('backend.product.modalForm', $data)->render();

        return response()->json([
            'success' => true,
            'form' => $form
        ]);
    }


    public function updateProductsBrandCategory(Request $request)
    {
        $products = ProductModel::where('vendor_id', $this->getVendorId())->skip($request->start)->take($request->end - $request->start)->orderBy('product_id', 'desc')->get();
        foreach ($products as $product) {
            $product->update([
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'update_category_or_brand' => true,
                'returns_allowed' => $request->has('return_allowed'),
                'product_status' => $request->has('product_status'),
            ]);
            $this->handleProductOffers($request, $product->product_id);
        }
        return response()->json([
            'success' => true,
            'msg' => 'Product category and brand updated successfully'
        ]);
    }



    public function sioBuyProducts(Request $request)
    {
        set_time_limit(600);
        $vendor_id = $this->getVendorId();
        $file = $request->file('csv_file');
        $contants = [];

        if (($handle = fopen($file, 'r')) !== false) {
            $line = fgets($handle);
            rewind($handle);

            // Detect the delimiter based on the first line
            $delimiter = (strpos($line, ';') !== false) ? ';' : ',';

            $header = fgetcsv($handle, 1000, $delimiter);
            $contants = [];

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (count($row) === count($header)) {
                    $contants[] = array_combine($header, $row);
                    if (count($contants) >= 100) {
                        $this->processBatch($contants, $request, $vendor_id);
                        $contants = [];
                    }
                }
            }
            fclose($handle);

            if (!empty($contants)) {
                $this->processBatch($contants, $request, $vendor_id);
            }
        }
        return response([
            'success' => true,
            'msg' => 'All records processed successfully.'
        ], 200);
    }

    public function processBatch(array $contants, $request, $vendor_id)
    {
        foreach ($contants as $contant) {
            $productCode = $contant['sku'] ?? '';
            $title = $contant['name'] ?? '';
            $imageURl = $contant['images'] ?? null;
            $stock =  is_numeric($contant['stock_c']) ?  $contant['stock_c'] : 0;
            $wholesalePrice  = is_numeric($contant['pvd']) ? $contant['pvd'] : 0;
            $price  = is_numeric($contant['pvr']) ? $contant['pvr'] : 0;

            if ($stock > 0 && $wholesalePrice > 0 && $price > 0) {
                $existProduct = ProductModel::where('vendor_id', $vendor_id)->where('product_code', $productCode)->first();

                if (!isset($existProduct)) {
                    $image = isset($imageURl) ? $this->uploadImageFromURL(explode(';', $imageURl)[0], self::PRODUCT_IMAGES_PATH) : '';
                    $productData = [
                        'product_name' => $title,
                        'product_code' => $productCode,
                        'product_tags' => implode(',', explode(' ', $title)),
                        'product_colors' => json_encode([]),
                        'admin_approved' => 0,
                        'returns_allowed' => 1,
                        'available_regions' => json_encode(["global"]),
                        'wholesale_available' => 1,
                        'retail_available' => 1,
                        'wholesale_price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $wholesalePrice),

                        'product_short_description' => htmlentities(substr($title, 0, 120), ENT_QUOTES, 'UTF-8'),
                        'product_long_description' => htmlentities($title, ENT_QUOTES, 'UTF-8'),
                        'product_slug' => $this->getProductSlug($title),
                        'product_price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $price),
                        'product_thumbnail' => $image,
                        'product_status' => 0,
                        'category_id' => 8,
                        'sub_category_id' => null,
                        'brand_id' => 1,
                        'vendor_id' => $vendor_id,
                        'length' => 1.00,
                        'weight' => 1.00,
                        'height' => 1.00,
                        'width' => 1.00,
                        'ships_from' => 1,
                        'product_quantity' => $stock ?? 0,
                    ];

                    $insertedProductId = ProductModel::insertGetId($productData);

                    if ($insertedProductId) {
                        ProductImagesModel::create([
                            'product_image' => $image,
                            'image_product_id' => $insertedProductId
                        ]);
                        ProductVariation::create([
                            'product_id' => $insertedProductId,
                            'color_id' => 0,
                            'size_id' => 0,
                            'length' => 1.00,
                            'weight' => 1.00,
                            'height' => 1.00,
                            'width' => 1.00,
                            'dimention_id' => 0,
                            'price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $price),
                            'product_quantity' =>  $stock ?? 0,
                            'whole_sale_price' => MyHelpers::toEuro(Auth::user()?->currency_id, (float) $wholesalePrice),
                        ]);

                        ProductModel::where('product_id', $insertedProductId)->update([
                            'total_variation_quantity' => (int)$stock,
                            'total_variation_whole_sale_price' => (int)$stock * (float)$wholesalePrice,
                            'total_variation_price' => (int)$stock * (float)$price,
                        ]);
                    } else {
                        Log::error('Failed to add product: ' . $title);
                    }
                }
            }
        }
        return true;
    }

    public function bulkRemove(Request $request)
    {

        $products = ProductModel::whereIn('product_id', $request->product_ids)->get();
        foreach ($products as $product) {
            $productId = $product->product_id;
            $images = self::getProductImages($productId);

            DB::table('product_offers')->where('offer_product_id', $productId)->delete();

            if ($product->delete()) {

                MyHelpers::deleteImageFromStorage($product->product_thumbnail, self::PRODUCT_IMAGES_PATH . '/');


                foreach ($images as $item) {
                    MyHelpers::deleteImageFromStorage($item->product_image, self::PRODUCT_IMAGES_PATH . '/');
                }
            }
        }
        return response([
            'success' => true,
            'msg' => 'Removed Successfully.'
        ], 200);
    }
}
