<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'admin_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'event_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'description' => $this->faker->paragraph(),
        ];
    }
}
