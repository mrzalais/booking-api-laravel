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
            'owner_id' => User::factory()->create(['role_id' => Role::ROLE_OWNER])->value('id'),
            'name' => fake()->text(20),
            'city_id' => City::factory()->create()->value('id'),
            'address_street' => fake()->streetAddress,
            'address_postcode' => fake()->postcode,
            'lat' => fake()->latitude,
            'long' => fake()->longitude,
        ];
    }
}
