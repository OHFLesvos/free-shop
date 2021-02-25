<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lastLogin = $this->faker->boolean(30);
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'timezone' => $this->faker->optional(0.7)->timezone,
            'notify_via_email' => false,
            'last_login_at' => $lastLogin ? now() : null,
            'last_login_ip' => $lastLogin ? $this->faker->ipv4 : null,
            'last_login_user_agent' => $lastLogin ? $this->faker->userAgent : null,
        ];
    }
}
