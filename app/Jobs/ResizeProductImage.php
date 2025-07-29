<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use App\Models\Product;

class ResizeProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	public $image_url;
	
	public $product;
	
	public function __construct($image_url,$product)
    {
        $this->image_url = $image_url;
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$imageDetails=getimagesize($this->image_url);
		if($imageDetails){
			$width = 200;
			$height = 200;
			$name = uniqid() . '_' . $this->product->id . '.' . basename($this->image_url);
			$image  = Image::make($this->image_url);
			if ($imageDetails[0] > $width) { 
				$image->resize($width, null, function ($constraint) {
					$constraint->aspectRatio();
				});
			}
			if ($imageDetails[1] > $height) {
				$image->resize(null, $height, function ($constraint) {
					$constraint->aspectRatio();
				}); 
			}
			$image->resizeCanvas($width, $height, 'center', false, '#ffffff')->save(public_path('/uploads/products/'). '/' .$name);
			$this->product->update(['image'=>'uploads/products/'.$name]);
			
			$name2 = uniqid() . '_' . $this->product->id . '.' . basename($this->image_url);
			$width2 = 500;
			$height2 = 500;
			$image2  = Image::make($this->image_url);
			if ($imageDetails[0] > $width2) { 
				$image2->resize($width2, null, function ($constraint) {
					$constraint->aspectRatio();
				});
			}
			if ($imageDetails[1] > $height2) {
				$image2->resize(null, $height2, function ($constraint) {
					$constraint->aspectRatio();
				}); 
			}
			$image2->resizeCanvas($width2, $height2, 'center', false, '#ffffff')->save(public_path('/uploads/products/'). '/' .$name2);
			$this->product->gallery()->create([
				'image'=>'/uploads/products/' .$name2,
				'ordering'=>intval(optional($this->product->gallery()->orderBy('ordering', 'DESC')->first())->ordering) + 1,
			]);
		}
    }
}
