<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Problem;

class ProblemSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Problem::factory(5)->create();
    }
}
