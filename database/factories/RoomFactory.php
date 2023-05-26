<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::factory()->create(),
            'room_type_id' => RoomType::all()->random(),
            'name' => $this->faker->name,
        ];
    }
}
