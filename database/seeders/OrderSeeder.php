<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        Order::factory()
            ->count(25)
            ->create()
            ->each(function ($order) use ($products) {
                $products->random(mt_rand(1, $products->count()))
                    ->each(function ($product) use ($order) {
                        $order->products()->attach($product, [
                            'amount' => $product->customer_limit !== null
                                ? mt_rand(1, $product->customer_limit)
                                : mt_rand(1, 10)
                        ]);
                    });
            });
    }
}
