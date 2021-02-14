<?php

namespace Database\Factories;

use App\Models\TextBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

class TextBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TextBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => strtolower($this->faker->unique()->word),
            'content' => $this->faker->text,
        ];
    }
}
