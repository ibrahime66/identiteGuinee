<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRequestFactory extends Factory
{
    public function definition()
    {
        $documentTypes = ['cni', 'passeport', 'permis'];
        $statuses = ['en cours', 'validée', 'rejetée'];
        $priorities = ['normal', 'urgent'];
        
        $documentType = fake()->randomElement($documentTypes);
        $status = fake()->randomElement($statuses);
        $priority = fake()->randomElement($priorities);

        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'reference' => strtoupper($documentType) . '-' . fake()->year() . '-' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => User::factory()->citizen(),
            'document_type' => $documentType,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'birth_place' => fake()->randomElement(['Conakry', 'Kankan', 'Labé', 'Nzérékoré', 'Kindia', 'Mamou', 'Faranah', 'Boké']),
            'address' => fake()->address(),
            'phone' => '+224 ' . fake()->phoneNumber(),
            'status' => $status,
            'priority' => $priority,
            'notes' => fake()->sentence(10),
            'rejection_reason' => $status === 'rejetée' ? fake()->sentence(8) : null,
            'validated_at' => $status === 'validée' ? fake()->dateTimeThisYear() : null,
            'rejected_at' => $status === 'rejetée' ? fake()->dateTimeThisYear() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en cours',
            'validated_at' => null,
            'rejected_at' => null,
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'validée',
            'validated_at' => fake()->dateTimeThisYear(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejetée',
            'validated_at' => null,
            'rejected_at' => fake()->dateTimeThisYear(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'normal',
        ]);
    }

    public function cni(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'cni',
        ]);
    }

    public function passport(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'passeport',
        ]);
    }

    public function permit(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'permis',
        ]);
    }
}
