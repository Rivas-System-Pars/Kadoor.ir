<?php

namespace Database\Seeders;

use App\Models\Slider;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ordering = 1;

        $faker = Factory::create();
        Storage::disk('public')->makeDirectory('uploads/sliders');

        if (config('front.sliderGroups')) {
            foreach (config('front.sliderGroups') as $sliderGroup) {
                for ($i = 0; $i < $sliderGroup['count']; $i++) {

                    $image = $faker->image(Storage::disk('public')->path('uploads/sliders'), $sliderGroup['width'], $sliderGroup['height']);
                    $image = substr($image, strpos($image, 'uploads'));

                    Slider::create([
                        'link'        => '#',
                        'group'       => $sliderGroup['group'],
                        'published'   => true,
                        'image'       => str_replace('public', '', $image),
                        'ordering'    => $ordering++
                    ]);
                }
            }
        }
    }
}
