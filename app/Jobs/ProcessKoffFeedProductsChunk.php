<?php

namespace App\Jobs;

use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\MyHelpers;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessKoffFeedProductsChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private const PRODUCT_IMAGES_PATH = 'uploads/images/product';
    protected $chunk;
    public $timeout = 300;
    public function __construct(array $chunk)
    {
        $this->chunk = $chunk;
    }

    public function handle()
    {
        foreach ($this->chunk as $item) {
            $productData = [
                'product_name' => $item['Retail title'],
                'product_code' => '"'.random_int(1000, 9999).'"',
                'product_tags' => implode(',', explode(' ', "asdf dsfsad f3213")), // Example tags
                'product_colors' => json_encode([]),
                'admin_approved' => 0,
                'returns_allowed' => 1,
                'available_regions' => json_encode(["global"]),
                'wholesale_available' => 1,
                'retail_available' => 1,
                'wholesale_price' => 4.98,
                'product_short_description' => htmlentities(substr($item['Retail title'], 0, 120), ENT_QUOTES, 'UTF-8'),
                'product_long_description' => htmlentities($item['Retail title'], ENT_QUOTES, 'UTF-8'),
                'product_slug' => $this->getProductSlug($item['Retail title']),
                'product_price' => (float) $item['RRP'] ?? 0,
                'product_thumbnail' =>  $this->uploadImageFromURL((string) explode(';', $item['Images'])[0], self::PRODUCT_IMAGES_PATH) ?? '',
                'product_status' => $item['Availability'] == "Active" ? 1 : 0,
                'category_id' => 1,
                'sub_category_id' => null,
                'brand_id' => 8,
                'vendor_id' => 16,
                'length' => 1.00,
                'weight' => 1.00,
                'height' => 1.00,
                'width' => 1.00,
                'ships_from' => 2,
                'product_quantity' => (int) $item["Stock"],
            ];
            $insertedProductId = ProductModel::insertGetId($productData);

            if ($insertedProductId) {
                ProductVariation::create([
                    'product_id' => $insertedProductId,
                    'color_id' => 0,
                    'size_id' => 0,
                    'dimention_id' => 0,
                    'price' => (float) $item['RRP'] ?? 0,
                    'product_quantity' =>  (int) $item["Stock"],
                    'whole_sale_price' => 0,
                ]);
            } else {
                Log::error('Failed to add product: ' . $item['Retail title']);
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

    private function getVendorId(): int
    {
        return  DB::table('vendor_shop')->where('user_id', '=', Auth::id())->first('vendor_id')->vendor_id;
    }
}
