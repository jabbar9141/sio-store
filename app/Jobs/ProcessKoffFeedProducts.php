<?php

namespace App\Jobs;

use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;


class ProcessKoffFeedProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 300;
    public function __construct()
    {
        //
    }

    public function handle()
    {
        $url = 'https://shop.koff.ro/feed/csv';
        $client = new Client();

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer phQlvOmyJ5DgG97yfRvsydAhEemSOjWj_-QXAVns_L1xMVM1VzQKW2e2D_Kxc9kG'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $contentType = $response->getHeader('Content-Type')[0];

                $csvContent = $response->getBody()->getContents();
                $rows = array_map('str_getcsv', explode("\n", $csvContent));
                $header = array_shift($rows);
                $csvArray = [];
                foreach ($rows as $row) {
                    if (count($row) > 1) {
                        $csvArray[] = array_combine($header, $row);
                    }
                }
                
                
                $filteredData = [];

                foreach ($csvArray as $item) {
                    if ($item["Stock"] != "0" && $item['Images'] !== "") {
                        $filteredData[] = $item;
                    }
                }
           
                $chunks = array_chunk($filteredData, 20);
                foreach ($chunks as $chunk) {
                    ProcessKoffFeedProductsChunk::dispatch($chunk);
                }

                return response(['msg' => 'Job dispatched successfully.'], 200);
            }

            return response('Unexpected status code: ' . $response->getStatusCode(), $response->getStatusCode());
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }

   
}

