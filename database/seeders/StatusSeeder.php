<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pending',
                'order' => 1
            ],
            [
                'name' => 'Ongoing',
                'order' => 2
            ],
            [
                'name' => 'Finished',
                'order' => 3
            ]
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    
    }
}
