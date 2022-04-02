<?php

use App\Models\Product;
use Gumlet\ImageResize;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
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
            ->filter(fn ($path) => is_file($path))
            ->each(function ($path) {
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
};
