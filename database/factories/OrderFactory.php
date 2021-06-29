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
        $status = $this->faker->randomElement([
            'new',
            'ready',
            'completed',
            'cancelled',
        ]);
        $created = $this->faker->dateTimeBetween('-1 month', 'now');
        return [
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'remarks' => $this->faker->optional(0.2)->text,
            'created_at' => $created,
            'status' => $status,
            'completed_at' => $status == 'completed' ? $this->faker->dateTimeBetween($created, 'now') : null
        ];
    }
}
