<?php

namespace Database\Seeders;

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

        Customer::factory()
            ->count(250)
            ->create()
            ->each(function ($customer) use ($tags) {
                $selectedTags = $tags
                    ->random(mt_rand(0, $tags->count()))
                    ->map(fn ($tag) => $tag->id);
                $customer->tags()->sync($selectedTags);
            });
    }
}
