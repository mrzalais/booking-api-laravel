<?php

namespace Database\Seeders;

use App\Models\BedType;
use Illuminate\Database\Seeder;

class BedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomNames = [
            'Single bed',
            'Large double bed',
            'Extra large double bed',
            'Sofa bed',
        ];

        foreach ($roomNames as $roomName) {
            BedType::create(['name' => $roomName]);
        }
    }
}
