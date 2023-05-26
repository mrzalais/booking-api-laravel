<?php

namespace Database\Factories;

use App\Models\BedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BedType>
 */
class BedTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
