<?php

namespace App\Console\Commands;

use App\Models\Statistics;
use App\Models\Viewer;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Back\ProductController;

class ApiPro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://webcomapi.ir/api/Store/StartData',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'apiKey: 7a2a1be2*d422*4d70*8b61*affdde'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $products = Product::all()->pluck('code')->toArray();
        foreach (json_decode($response)->article as $article){
            try{
                ProductController::create_product($article, in_array($article->fldC_Kala, $products));
            }catch (\Exception $e){
                continue;
            }
        }
    }
}
