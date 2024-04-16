<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Tom Cruise',
            'email' => 't.cruise@gmail.com',
            'password' => bcrypt('tCruise12?3'),
        ]);

        User::factory()->create([
            'name' => 'Mark Black',
            'email' => 'm.black@gmail.com',
            'password' => bcrypt('mBlack12?3'),
        ]);

        Task::factory(10)->create();
    }
}
