<?php

namespace Database\Seeders;

use App\Models\Customer;
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
        $products = Product::limit(mt_rand(1, Product::count()))
            ->get();
        Customer::limit(mt_rand(1, Customer::count()))
            ->get()
            ->each(function ($customer) use ($products) {
                Order::factory()
                    ->count(mt_rand(1, 5))
                    ->for($customer)
                    ->create()
                    ->each(function ($order) use ($products) {
                        $products->filter(fn ($product) => $product->quantity_available_for_customer > 0)
                            ->random(mt_rand(1, $products->count() - 1))
                            ->each(function ($product) use ($order) {
                                $order->products()->attach($product, [
                                    'quantity' => $product->limit_per_order !== null
                                        ? mt_rand(1, $product->limit_per_order)
                                        : mt_rand(1, 10)
                                ]);
                            });
                    });
            });
    }
}
