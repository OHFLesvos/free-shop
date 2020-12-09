<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'customer_name' => $this->faker->name,
            'customer_id_number' => $this->faker->randomNumber(9, true),
            'customer_phone' => $this->faker->e164PhoneNumber,
            'customer_ip_address' => $this->faker->ipv4,
            'customer_user_agent' => $this->faker->userAgent,
            'remarks' => $this->faker->optional(0.2)->text,
        ];
    }
}
