<?php

use App\Models\Product;
use Gumlet\ImageResize;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ResizeProductImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Product::whereNotNull('picture')
            ->get()
            ->filter(fn ($product) => !preg_match('#^http[s]?://#', $product->picture))
            ->map(fn ($product) => Storage::path($product->picture))
            ->each(function($path) {
                $image = new ImageResize($path);
                $image->resizeToWidth(config('shop.product.max_picture_width'));
                $image->save($path);
            });
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
