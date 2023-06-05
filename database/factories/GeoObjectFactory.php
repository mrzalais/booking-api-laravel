<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class GeoObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory()->create(),
            'name' => $this->faker->city,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

