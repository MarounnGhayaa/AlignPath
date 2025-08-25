<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Path>
 */
class PathFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'name' => fake()->randomElement([
                'software engineer',
                'designer',
                'instructor'
            ]),

            'tag' => fake()->randomElement([
                'development',
                'designing',
                'teaching'
            ]),
        ];
    }
}
