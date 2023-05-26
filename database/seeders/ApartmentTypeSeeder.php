<?php

namespace Database\Seeders;

use App\Models\ApartmentType;
use Illuminate\Database\Seeder;

class ApartmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apartmentNames = [
            'Entire apartment',
            'Entire studio',
            'Private suite',
        ];

        foreach ($apartmentNames as $apartmentName) {
            ApartmentType::create(['name' => $apartmentName]);
        }
    }
}
