<?php

namespace Database\Factories;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompletionFactory extends Factory
{
    public function definition(): array
    {
        return ['habit_id' => Habit::factory(), 'completed_on' => now('Asia/Jakarta')->toDateString()];
    }
}
