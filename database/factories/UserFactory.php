<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['citizen', 'admin']),
            'phone' => '+224 ' . fake()->phoneNumber(),
            'cni_number' => 'CNI-' . fake()->year() . '-' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'birth_place' => fake()->randomElement(['Conakry', 'Kankan', 'Labé', 'Nzérékoré', 'Kindia', 'Mamou', 'Faranah', 'Boké']),
            'address' => fake()->address(),
            'profession' => fake()->jobTitle(),
            'nationality' => 'Guinéenne',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function citizen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'citizen',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
