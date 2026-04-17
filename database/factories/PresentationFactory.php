<?php

namespace Database\Factories;

use App\Models\Presentation;
use App\Models\Contest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresentationFactory extends Factory
{
    protected $model = Presentation::class;

    public function definition(): array
    {
        return [
            'contest_id' => Contest::factory(),
            'competitor_id' => User::factory(),
            'work_title' => $this->faker->words(3, true),
            'status' => 'EM_ANALISE',
            'checkin_realizado' => false,
            'qr_code_hash' => $this->faker->uuid(),
        ];
    }
}
