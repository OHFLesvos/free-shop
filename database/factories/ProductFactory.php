<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->catchPhrase;
        return [
            'name' => $name,
            'category' => ucfirst($this->faker->word),
            'picture' => $this->faker->boolean(70) ? 'https://picsum.photos/seed/' . md5($name) . '/300/150' : null,
            'description' => $this->faker->optional(0.9)->text,
            'price' => $this->faker->numberBetween(0, 10),
            'stock' => $this->faker->numberBetween(0, 1000),
            'limit_per_order' => $this->faker->optional(0.2)->numberBetween(0, 10),
            'is_available' => $this->faker->boolean(80),
        ];
    }
}
