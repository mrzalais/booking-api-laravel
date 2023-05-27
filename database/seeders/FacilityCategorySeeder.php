<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilityNames = [
            'Bedroom',
            'Kitchen',
            'Bathroom',
            'Room Amenities',
            'General',
            'Media & Technology',
        ];

        foreach ($facilityNames as $facilityName) {
            FacilityCategory::create(['name' => $facilityName]);
        }
    }
}
