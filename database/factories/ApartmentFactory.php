<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\ApartmentType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_type_id' => ApartmentType::all()->random(),
            'property_id' => Property::factory()->create(),
            'name' => $this->faker->text(20),
            'capacity_adults' => $this->faker->numberBetween(1, 4),
            'capacity_children' => $this->faker->numberBetween(0, 4),
            'size' => $this->faker->numberBetween(50, 200),
            'bathrooms' => $this->faker->numberBetween(1, 3),
        ];
    }
}
