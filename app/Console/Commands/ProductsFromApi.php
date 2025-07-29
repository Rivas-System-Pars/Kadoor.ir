<?php

namespace App\Console\Commands;

use App\Http\Controllers\Back\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Jobs\ResizeProductImage2;

class ProductsFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $res = Http::withHeaders([
                'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde'
            ])->get('https://webcomapi.ir/api/Store/StartData');
            $products = Arr::get($res->json(), 'article', []);
            $categories = Arr::get($res->json(), 'sGroup', []);

            if (count($products)) {
                DB::transaction(function () use ($products, $categories) {
					$product_show_ids=[];
					$product_not_show_ids=[];
                    foreach ($products as $productItem) {
                        /** @var Product $product */
                        $product = ProductController::create_product($productItem, in_array($productItem['fldC_Kala'], Product::all()->pluck('code')->toArray()));
                        
						if($productItem['fldShow'] == "true" && !$product->show){
							$product_show_ids[]=$product->id;
						}else if($productItem['fldShow'] != "true" && $product->show){
							$product_not_show_ids[]=$product->id;
						}
						$image_url=collect(array_values(explode(',',$productItem['fldLink'])))->last();
						$image_url = $image_url ? $image_url : 'https://wwebcomvip.ir/FTP/IMGwebcom/webcom.png';
						$image_original_name=collect(explode("/",$image_url))->last();
						if(!$product->image_original_name || $image_original_name != $product->image_original_name){
							$product->update(['image_original_name'=>$image_original_name]);
							ResizeProductImage2::dispatch($image_url,$product);
						}
                        $categoryIds = str_split(substr($productItem['fldC_Kala'], 0, 4), 2);
                        if (count($categoryIds) == 2 && $item = collect($categories)->where('fldC_M_GroohKala', $categoryIds[0])->firstWhere('fldC_S_GroohKala', $categoryIds[1])) {
                            $cats = Category::query()->where('title', $item['fldN_S_GroohKala'])->orderBy(DB::raw('ISNULL(category_id), category_id'), 'ASC')->get();
                            if ($cats && $cats->count()) {
                                $product->update(['category_id'=>$cats->first()->id]);
                                $product->categories()->syncWithoutDetaching($cats->pluck('id')->toArray());
                            }
                        }
                    }
					if(count($product_show_ids)){
						Product::whereIn('id',$product_show_ids)->update(['is_show'=>true]);
					}
					if(count($product_not_show_ids)){
						Product::whereIn('id',$product_not_show_ids)->update(['is_show'=>false]);
					}
                });
            }
        } catch (\Throwable $e) {
            logger($e);
        }
        return 0;
    }
}
