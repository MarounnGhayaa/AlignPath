<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            UserSeeder::class
        ]);

        $this->call([
            PathSeeder::class
        ]);

        $this->call([
            SkillSeeder::class
        ]);

        $this->call([
            ResourceSeeder::class
        ]);

        $this->call([
            QuestSeeder::class
        ]);

        $this->call([
            ProblemSeeder::class
        ]);
    }
}
