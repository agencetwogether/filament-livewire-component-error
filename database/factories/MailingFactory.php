<?php

namespace Database\Factories;

use App\Models\Mailing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mailing>
 */
class MailingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
        ];
    }
}
