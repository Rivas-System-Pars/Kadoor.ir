<?php

namespace Database\Seeders;

use App\Models\Banner;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        Storage::disk('public')->makeDirectory('uploads/banners');

        if (config('front.bannerGroups')) {
            foreach (config('front.bannerGroups') as $bannerGroup) {
                for ($i = 0; $i < $bannerGroup['count']; $i++) {

                    $image = $faker->image(Storage::disk('public')->path('uploads/banners'), $bannerGroup['width'], $bannerGroup['height']);
                    $image = substr($image, strpos($image, 'uploads'));
                    
                    Banner::create([
                        'link'        => '#',
                        'group'       => $bannerGroup['group'],
                        'published'   => true,
                        'image'       => str_replace('public', '', $image)
                    ]);
                }
            }
        }
    }
}
