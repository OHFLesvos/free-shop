<?php

namespace Database\Seeders;

use App\Models\BlockedPhoneNumber;
use Illuminate\Database\Seeder;

class BlockedPhoneNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BlockedPhoneNumber::factory()
            ->count(25)
            ->create();
    }
}
