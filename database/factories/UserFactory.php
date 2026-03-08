<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(), // Make sure this exists
            'lastname' => $this->faker->lastName(),
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
             'role' => $this->faker->randomElement(['admin', 'reviewer', 'researcher']),
            'profile_picture' => null, 
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function reviewer()
    {
        return $this->state([
            'role' => 'reviewer',
        ]);
    }

    public function researcher()
    {
        return $this->state([
            'role' => 'researcher',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
