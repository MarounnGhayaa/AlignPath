<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'path_id' => 1,
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(), 
            'type' => fake()->randomElement(['documentation', 'video', 'community']),
            'url' => fake()->url(),
        ];
    }
}
