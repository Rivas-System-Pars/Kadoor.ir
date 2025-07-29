<?php

namespace App\Console\Commands;

use App\Models\Category;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CategoriesFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:categories';

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
                'apiKey'=>'7a2a1be2*d422*4d70*8b61*affdde'
            ])->get('https://webcomapi.ir/api/Store/StartData');
            $categories = Arr::get($res->json(), 'sGroup', []);
            $mainCategories = Arr::get($res->json(), 'mGroup', []);
            if ($mainCategories && count($mainCategories)) {
                DB::transaction(function () use ($mainCategories, $categories) {
                    foreach ($mainCategories as $mainCategory) {
                        $category = Category::query()->firstOrCreate([
                            'title' => $mainCategory['groupName'],
                        ], [
                            'title' => $mainCategory['groupName'],
                            'link' => $mainCategory['link'],
                            'type' => "productcat",
                            'slug' => $mainCategory['groupName'],
                        ]);
                        foreach (collect($categories)->where('fldC_M_GroohKala', $mainCategory['groupId']) as $sCategory) {
                            Category::query()->firstOrCreate([
								'category_id' => $category->id,
                                'title' => $sCategory['fldN_S_GroohKala'],
                            ], [
                                'category_id' => $category->id,
                                'title' => $sCategory['fldN_S_GroohKala'],
                                'link' => $sCategory['fldLink'],
                                'type' => "productcat",
                                'slug' => $sCategory['fldN_S_GroohKala'],
                            ]);
                        }
                    }
                });
            }
        } catch (\Throwable $e) {
            logger($e);
        }
        return 0;
    }
}
