<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {      // 1. Create Statuses first
       $statuses =  $this->call(StatusSeeder::class);

        // 2. Create Users
       $users =  User::factory(5)->create();

        // 3. Create Tickets last (because they need both users and statuses)
        Ticket::factory(20)->create([
            'user_id' => fn() => $users->random()->id,
            'assigned_user_id' => fn() => $users->random()->id,
            'status_id' => fn() => $statuses->random()->id,
        ]);
        }
}
