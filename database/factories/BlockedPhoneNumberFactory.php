<?php

namespace Database\Factories;

use App\Models\BlockedPhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockedPhoneNumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BlockedPhoneNumber::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone' => $this->faker->e164PhoneNumber,
            'reason' => $this->faker->text,
        ];
    }
}
