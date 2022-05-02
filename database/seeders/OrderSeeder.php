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
        $products = Product::limit(mt_rand(1, Product::count()))->get();
        $customers = Customer::limit(mt_rand(1, Customer::count()))->get();

        $customers->each(function (Customer $customer) use ($products) {
            Order::factory()
                ->count(mt_rand(1, 5))
                ->for($customer)
                ->create()
                ->each(function (Order $order) use ($products) {
                    $availableProducts = $products->filter(fn (Product $product) => $product->getQuantityAvailableForOrdering() > 0);
                    if ($availableProducts->count() > 0) {
                        $availableProducts->random(mt_rand(1, $availableProducts->count()))
                            ->each(function (Product $product) use ($order) {
                                $order->products()->attach($product, [
                                    'quantity' => $product->limit_per_order !== null
                                        ? mt_rand(1, $product->limit_per_order)
                                        : mt_rand(1, 10)
                                ]);
                            });
                    }
                });
        });
    }
}
