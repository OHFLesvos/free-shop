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
            ->count(35)
            ->create()
            ->each(function ($order) use ($products) {
                $products->filter(fn ($product) => $product->available_for_customer_amount > 0)
                    ->random(mt_rand(1, $products->count() - 1))
                    ->each(function ($product) use ($order) {
                        $order->products()->attach($product, [
                            'amount' => $product->limit_per_order !== null
                                ? mt_rand(1, $product->limit_per_order)
                                : mt_rand(1, 10)
                        ]);
                    });
            });
    }
}
