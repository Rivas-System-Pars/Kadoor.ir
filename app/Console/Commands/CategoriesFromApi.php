<?php

// namespace App\Console\Commands;

// use App\Models\Category;
// use App\Models\Price;
// use App\Models\Product;
// use GuzzleHttp\Client;
// use Illuminate\Console\Command;
// use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Http;

// class CategoriesFromApi extends Command
// {
//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = 'api:categories';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */
//     protected $description = 'Command description';

//     /**
//      * Create a new command instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         parent::__construct();
//     }

//     /**
//      * Execute the console command.
//      *
//      * @return int
//      */
//     public function handle()
//     {
//         try {
//             $res = Http::withHeaders([
//                 'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde'
//             ])->get('http://visitorykadoor.ir/GroupsKala');

//             $data = $res->json();

//             foreach ($data as $groups) {
//                 $McategoryMaliId = 'M-' . $groups['M_groupcode'];
//                 $Mcategory = Category::where('category_mali_id', $McategoryMaliId)->first();

//                 $McategoryPayload = [
//                     'title' => $groups['M_groupname'],
//                     'slug' => $groups['M_groupname'],
//                     'type' => 'productcat',
//                     'image' => $groups['M_image'],
//                     'published' => 1,
//                     'category_id' => null,
//                     'filter_type' => 'inherit',
//                     'category_mali_id' => $McategoryMaliId,
//                 ];

//                 if ($Mcategory) {
//                     $changed = false;
//                     foreach ($McategoryPayload as $key => $value) {
//                         if ($Mcategory->$key != $value) {
//                             $changed = true;
//                             break;
//                         }
//                     }
//                     if ($changed) $Mcategory->update($McategoryPayload);
//                 } else {
//                     $Mcategory = Category::create($McategoryPayload);
//                 }

//                 foreach ($groups['sub_categories'] as $sub_group) {
//                     $ScategoryMaliId = 'S-' . $groups['M_groupcode'] . '-' . $sub_group['S_groupcode'];
//                     $Scategory = Category::where('category_mali_id', $ScategoryMaliId)->first();

//                     $ScategoryPayload = [
//                         'title' => $sub_group['S_groupname'],
//                         'slug' => $sub_group['S_groupname'],
//                         'type' => 'productcat',
//                         'image' => $sub_group['S_image'],
//                         'published' => 1,
//                         'category_id' => $Mcategory->id,
//                         'filter_type' => 'inherit',
//                         'category_mali_id' => $ScategoryMaliId,
//                     ];

//                     if ($Scategory) {
//                         $changed = false;
//                         foreach ($ScategoryPayload as $key => $value) {
//                             if ($Scategory->$key != $value) {
//                                 $changed = true;
//                                 break;
//                             }
//                         }
//                         if ($changed) $Scategory->update($ScategoryPayload);
//                     } else {
//                         $Scategory = Category::create($ScategoryPayload);
//                     }

//                     // گرفتن محصولات مربوط به دسته
//                     $product_res = Http::withHeaders([
//                         'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
//                     ])->post('http://visitorykadoor.ir/ArticleByGroups', [
//                         'M_groupcode' => $groups['M_groupcode'],
//                         'S_groupcode' => $sub_group['S_groupcode'],
//                     ]);

//                     $product_data = $product_res->json();

//                     foreach ($product_data['Articles'] as $products) {
//                         $productCode = $products['FldC_Kala'];

//                         $product_DB = Product::where('code', $productCode)->first();

//                         $productPayload = [
//                             'code' => $productCode,
//                             'title' => $products['FldN_Kala'],
//                             'type' => 'physical',
//                             'category_id' => $Mcategory->id,
//                             'slug' => $products['FldN_Kala'],
//                             'image' => $products['FldImage'],
//                             'unit' => 'تعداد',
//                             'weight' => 0,
//                             'published' => 1,
//                             'Consumer_price' => $products['FldFeeBadAzTakhfif'] ?? $products['FldFee'],
//                             'is_show' => 1
//                         ];

//                         if ($product_DB) {
//                             $changed = false;
//                             foreach ($productPayload as $key => $value) {
//                                 if ($product_DB->$key != $value) {
//                                     $changed = true;
//                                     break;
//                                 }
//                             }
//                             if ($changed) $product_DB->update($productPayload);
//                         } else {
//                             $product_DB = Product::create($productPayload);
//                         }

//                         $pivotExists = DB::table('category_product')
//                             ->where('product_id', $product_DB->id)
//                             ->where('category_id', $Scategory->id)
//                             ->exists();

//                         if (!$pivotExists) {
//                             DB::table('category_product')->insert([
//                                 'product_id' => $product_DB->id,
//                                 'category_id' => $Scategory->id
//                             ]);
//                         }

//                         $price = $products['EndBuyPrice'];
//                         $finalPrice = is_float($price) ? (int) $price : $price;

//                         $priceRecord = Price::where('product_id', $product_DB->id)->first();

//                         if ($priceRecord) {
//                             if (
//                                 $priceRecord->price != $price ||
//                                 $priceRecord->discount_price != $finalPrice
//                             ) {
//                                 $priceRecord->update([
//                                     'price' => $price,
//                                     'discount_price' => $finalPrice,
//                                     'stock' => null,
//                                 ]);
//                             }
//                         } else {
//                             Price::create([
//                                 'price' => $price,
//                                 'product_id' => $product_DB->id,
//                                 'discount_price' => $finalPrice,
//                                 'stock' => null,
//                             ]);
//                         }
//                     }
//                 }
//             }
//         } catch (\Throwable $e) {
//             dd($e);
//             logger($e);
//         }
//         return 0;
//     }
// }<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CategoriesFromApi extends Command
{
    protected $signature = 'api:categories';
    protected $description = 'Fetch and sync categories, products and prices from external API';

    public function handle()
    {
        try {
            $mainCatIdsFromApi = [];
            $subCatIdsFromApi = [];
            $productCodesFromApi = [];

            $res = Http::withHeaders([
                'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde'
            ])->get('http://visitorykadoor.ir/GroupsKala');

            $data = $res->json();

            foreach ($data as $group) {
                $mainCatId = 'M-' . $group['M_groupcode'];
                $mainCatIdsFromApi[] = $mainCatId;

                $mainCatPayload = [
                    'title' => $group['M_groupname'],
                    'slug' => $group['M_groupname'],
                    'type' => 'productcat',
                    'image' => $group['M_image'],
                    'published' => 1,
                    'category_id' => null,
                    'filter_type' => 'inherit',
                    'category_mali_id' => $mainCatId,
                ];

                $mainCategory = Category::where('category_mali_id', $mainCatId)->first();
                if ($mainCategory) {
                    if ($this->hasChanges($mainCategory, $mainCatPayload)) {
                        $mainCategory->update($mainCatPayload);
                    }
                } else {
                    $mainCategory = Category::create($mainCatPayload);
                }

                foreach ($group['sub_categories'] as $sub) {
                    $subCatId = 'S-' . $group['M_groupcode'] . '-' . $sub['S_groupcode'];
                    $subCatIdsFromApi[] = $subCatId;

                    $subCatPayload = [
                        'title' => $sub['S_groupname'],
                        'slug' => $sub['S_groupname'],
                        'type' => 'productcat',
                        'image' => $sub['S_image'],
                        'published' => 1,
                        'category_id' => $mainCategory->id,
                        'filter_type' => 'inherit',
                        'category_mali_id' => $subCatId,
                    ];

                    $subCategory = Category::where('category_mali_id', $subCatId)->first();
                    if ($subCategory) {
                        if ($this->hasChanges($subCategory, $subCatPayload)) {
                            $subCategory->update($subCatPayload);
                        }
                    } else {
                        $subCategory = Category::create($subCatPayload);
                    }

                    // Fetch products
                    $productRes = Http::withHeaders([
                        'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
                    ])->post('http://visitorykadoor.ir/ArticleByGroups', [
                        'M_groupcode' => $group['M_groupcode'],
                        'S_groupcode' => $sub['S_groupcode'],
                    ]);

                    $productData = $productRes->json();

                    foreach ($productData['Articles'] ?? [] as $p) {
                        $code = $p['FldC_Kala'];
                        $productCodesFromApi[] = $code;

                        $price = $p['EndBuyPrice'];
                        $discountPrice = is_float($price) ? (int)$price : $price;

                        $productPayload = [
                            'code' => $code,
                            'title' => $p['FldN_Kala'],
                            'type' => 'physical',
                            'category_id' => $mainCategory->id,
                            'slug' => $p['FldN_Kala'],
                            'image' => $p['FldImage'],
                            'unit' => 'تعداد',
                            'weight' => 0,
                            'published' => 1,
                            'Consumer_price' => $p['FldFeeBadAzTakhfif'] ?? $p['FldFee'],
                            'is_show' => 1
                        ];

                        $product = Product::where('code', $code)->first();
                        if ($product) {
                            if ($this->hasChanges($product, $productPayload)) {
                                $product->update($productPayload);
                            }
                        } else {
                            $product = Product::create($productPayload);
                        }

                        // pivot check
                        $pivot = DB::table('category_product')
                            ->where('product_id', $product->id)
                            ->where('category_id', $subCategory->id)
                            ->exists();

                        if (!$pivot) {
                            DB::table('category_product')->insert([
                                'product_id' => $product->id,
                                'category_id' => $subCategory->id
                            ]);
                        }

                        // Price sync
                        $priceRecord = Price::where('product_id', $product->id)->first();
                        if ($priceRecord) {
                            if (
                                $priceRecord->price != $price ||
                                $priceRecord->discount_price != $discountPrice
                            ) {
                                $priceRecord->update([
                                    'price' => $price,
                                    'discount_price' => $discountPrice,
                                    'stock' => null,
                                ]);
                            }
                        } else {
                            Price::create([
                                'price' => $price,
                                'product_id' => $product->id,
                                'discount_price' => $discountPrice,
                                'stock' => null,
                            ]);
                        }
                    }
                }
            }

            // 🧹 حذف موارد حذف‌شده از سرور
            Category::whereNotIn('category_mali_id', array_merge($mainCatIdsFromApi, $subCatIdsFromApi))->delete();
            Product::whereNotIn('code', $productCodesFromApi)->delete();

            $this->info("Sync completed successfully.");
        } catch (\Throwable $e) {
            logger($e);
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }

    protected function hasChanges($model, $data): bool
    {
        foreach ($data as $key => $value) {
            if ($model->$key != $value) {
                return true;
            }
        }
        return false;
    }
}
