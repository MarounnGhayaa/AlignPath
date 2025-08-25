<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quest;

class QuestSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Quest::factory(5)->create();
    }
}
