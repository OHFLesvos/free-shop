<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    use WithoutModelEvents;

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

        Customer::factory()
            ->count(1000)
            ->create()
            ->each(function ($customer) use ($tags, $currencies) {
                $selectedTags = $tags
                    ->random(mt_rand(0, $tags->count()))
                    ->map(fn ($tag) => $tag->id);
                $customer->tags()->sync($selectedTags);

                $ids = $currencies
                    ->mapWithKeys(fn (Currency $currency) => [$currency->id => [
                        'value' => mt_rand(0, $currency->initial_value),
                    ]]);
                $customer->currencies()->sync($ids);
            });
    }
}
