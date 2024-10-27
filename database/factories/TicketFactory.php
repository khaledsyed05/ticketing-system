<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status_id' => Status::factory(),
            'deadline' => fake()->dateTimeBetween('now', '+3 days'),
            'username' => fake()->userName(),
            'user_id' => User::factory(),
            'assigned_user_id' => User::factory(),
        ];
    }
}
