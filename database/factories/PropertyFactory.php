<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->create(['role_id' => Role::ROLE_OWNER]),
            'name' => $this->faker->text(20),
            'city_id' => City::factory()->create(),
            'address_street' => $this->faker->streetAddress,
            'address_postcode' => $this->faker->postcode,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
        ];
    }
}
