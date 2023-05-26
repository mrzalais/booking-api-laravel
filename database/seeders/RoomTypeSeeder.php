<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomNames = [
            'Bedroom',
            'Living room',
        ];

        foreach ($roomNames as $roomName) {
            RoomType::create(['name' => $roomName]);
        }
    }
}
