<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'id_number' => $this->faker->randomNumber(9, true),
            'phone' => $this->faker->e164PhoneNumber,
            'credit' => $this->faker->numberBetween(0, 30),
            'remarks' => $this->faker->optional(0.2)->text,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
