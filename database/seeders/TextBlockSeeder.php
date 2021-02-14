<?php

namespace Database\Seeders;

use App\Models\TextBlock;
use Illuminate\Database\Seeder;

class TextBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TextBlock::factory()
            ->count(3)
            ->create();
    }
}
