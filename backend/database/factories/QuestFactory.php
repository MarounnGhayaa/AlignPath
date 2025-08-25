<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'path_id' => 1,
            'title' => fake()->sentence(3),
            'subtitle' => fake()->sentence(6),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'duration' => fake()->randomFloat(2, 0.5, 20),
        ];
    }
}
