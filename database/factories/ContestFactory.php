<?php

namespace Database\Factories;

use App\Models\Contest;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContestFactory extends Factory
{
    protected $model = Contest::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->words(2, true),
            'status' => 'AGENDADO',
            'current_presentation_id' => null,
        ];
    }
}
