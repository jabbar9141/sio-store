<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\MyHelpers;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessSioBuyProductsChunks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const PRODUCT_IMAGES_PATH = 'uploads/images/product';
    public $timeout = 300;

    protected $chunks;
    protected $vendor_id;
    protected $uploadedImages;
    protected $uploadedVideos;
    protected $category_id;
    protected $brand_id;
    protected $ships_from;
    protected $product_quantity;
    protected $product_variations;


    /**
     * Create a new job instance.
     *
     * @param  array  $chunks
     * @param  int  $vendor_id
     * @param  array  $uploadedImages
     * @param  array  $uploadedVideos
     * @return void
     */
    public function __construct($chunks,$vendor_id,$uploadedImages, $uploadedVideos ,$category_id,$brand_id, $ships_from, $product_quantity ,$product_variations)
    {
        $this->chunks = $chunks;
        $this->vendor_id = $vendor_id;
        $this->uploadedImages = $uploadedImages;
        $this->uploadedVideos = $uploadedVideos;
        $this->category_id = $category_id;
        $this->brand_id = $brand_id;
        $this->ships_from = $ships_from;
        $this->product_quantity = $product_quantity;
        $this->product_variations = $product_variations;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->chunks as $contant) {
            $productCode = $contant['sku'] ?? '';
            $title = $contant['name'] ?? '';
            $imageURl = $contant['images'] ?? null;
            $vendor_id = $this->vendor_id;

            $existProduct = ProductModel::where('vendor_id', $vendor_id)->where('product_code', $productCode)->first();

            if (!isset($existProduct)) {
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
                    'wholesale_price' => 0.00,
                    'product_short_description' => htmlentities(substr($title, 0, 120), ENT_QUOTES, 'UTF-8'),
                    'product_long_description' => htmlentities($title, ENT_QUOTES, 'UTF-8'),
                    'product_slug' => $this->getProductSlug($title),
                    'product_price' => 0.00,
                    'product_thumbnail' => isset($imageURl) ? $this->uploadImageFromURL(explode(';', $imageURl)[0], self::PRODUCT_IMAGES_PATH) : '',
                    'product_status' => 0,
                    'category_id' => $this->category_id,
                    'sub_category_id' => null,
                    'brand_id' => $this->brand_id,
                    'vendor_id' => $vendor_id,
                    'length' => 1.00,
                    'weight' => 1.00,
                    'height' => 1.00,
                    'width' => 1.00,
                    'ships_from' => $this->ships_from,
                    'product_quantity' => array_sum($this->product_quantity) ?? 0,
                ];

                $insertedProductId = ProductModel::insertGetId($productData);

                if ($insertedProductId) {
                    if ($this->product_variations) {
                        $productVariations = json_decode($this->product_variations, true);
                        foreach ($productVariations as $productVariation) {
                            $jsonEncodedImages = [];
                            if (isset($productVariation['fileIndices']) && count($productVariation['fileIndices']) > 0) {
                                foreach ($productVariation['fileIndices'] as $index) {
                                    $jsonEncodedImages[] = $this->uploadedImages[$index];
                                }
                            }
        
                            $jsonEncodedVideo = [];
                            if (isset($productVariation['videoIndices']) && count($productVariation['videoIndices']) > 0) {
                                foreach ($productVariation['videoIndices'] as $index) {
                                    $jsonEncodedVideo[] = $this->uploadedVideos[$index];
                                }
                            }
        
                            ProductVariation::create([
                                'product_id' => $insertedProductId,
                                'color_id' => 0,
                                'size_id' => 0,
                                'dimention_id' => 0,
                                'size_name' => $productVariation['size_name'] ?? null,
                                'color_name' => $productVariation['color_name'] ?? null,
                                'width' => $productVariation['width'] ?? null,
                                'height' => $productVariation['height'] ?? null,
                                'length' => $productVariation['length'] ?? null,
                                'weight' => $productVariation['weight'] ?? null,
                                'price' => $productVariation['price'] ?? 0,
                                'product_quantity' => $productVariation['quantity'] ?? 0,
                                'whole_sale_price' => $productVariation['whole_sale_price'] ?? 0,
                                'image_url' => json_encode($jsonEncodedImages),
                                'video_url' => json_encode($jsonEncodedVideo),
                            ]);
                        }
                    }
                } else {
                    Log::error('Failed to add product: ' . $contant['Retail title']);
                }
            }
        }
    }

    private function getProductSlug($title)
    {
        return str_replace(' ', '-', strtolower(trim($title))) . uniqid('-');
    }

    private function uploadImageFromURL($url, $path = self::PRODUCT_IMAGES_PATH)
    {
        try {
            $client = new Client();
            if ($url && is_string($url)) {
                $response = $client->get($url);

                if ($response->getStatusCode() == 200) {
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
                return '';
            }

            return ''; // Return empty string if the response status code is not 200
        } catch (\Exception $e) {
            // Log::error($e->getMessage(), [$e]);
            return '';
        }
    }

    private function getVendorId(): int
    {
        return  DB::table('vendor_shop')->where('user_id', '=', Auth::id())->first('vendor_id')->vendor_id;
    }
}
