<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Problem>
 */
class ProblemFactory extends Factory {
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
            'topic' => fake()->randomElement([
                'algorithms',
                'data structures',
                'databases',
                'web development',
                'security',
                'math'
            ]),
            'points' => fake()->numberBetween(10, 100),
        ];
    }
}
