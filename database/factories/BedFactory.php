<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed>
 */
class BedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory()->create(),
            'bed_type_id' => BedType::all()->random(),
            'name' => $this->faker->name,
        ];
    }
}
