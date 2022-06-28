<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = Tag::factory()
            ->count(10)
            ->create();

        $currencies = Currency::all();

        $customers = Customer::factory()
            ->count(500)
            ->create();

        $customers->each(function (Customer $customer) use ($tags) {
            $selectedTags = $tags
                ->random(mt_rand(0, $tags->count()))
                ->map(fn ($tag) => $tag->id);
            $customer->tags()->sync($selectedTags);
        });

        $customers->each(function (Customer $customer) use ($currencies) {
            $balances = $currencies->mapWithKeys(fn (Currency $currency) => [$currency->id => mt_rand(0, $currency->top_up_amount)]);
            $customer->setBalances($balances);
        });
    }
}
