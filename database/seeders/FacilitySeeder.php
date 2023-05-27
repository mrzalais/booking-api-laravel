<?php

namespace Database\Seeders;

use App\Enums\FacilityCategoryEnum;
use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            [
                'category_id' => FacilityCategoryEnum::Bedroom,
                'name' => 'Linen',
            ],
            [
                'category_id' => FacilityCategoryEnum::Bedroom,
                'name' => 'Wardrobe or closet',
            ],
            [
                'category_id' => FacilityCategoryEnum::Kitchen,
                'name' => 'Electric kettle',
            ],
            [
                'category_id' => FacilityCategoryEnum::Kitchen,
                'name' => 'Microwave',
            ],
            [
                'category_id' => FacilityCategoryEnum::Kitchen,
                'name' => 'Washing machine',
            ],
            [
                'category_id' => FacilityCategoryEnum::Bathroom,
                'name' => 'Private bathroom',
            ],
            [
                'category_id' => FacilityCategoryEnum::Bathroom,
                'name' => 'Shower',
            ],
            [
                'category_id' => FacilityCategoryEnum::Bathroom,
                'name' => 'Towels',
            ],
            [
                'category_id' => FacilityCategoryEnum::RoomAmenities,
                'name' => 'Drying rack for clothing',
            ],
            [
                'category_id' => FacilityCategoryEnum::General,
                'name' => 'No smoking',
            ],
            [
                'category_id' => FacilityCategoryEnum::General,
                'name' => 'Fan',
            ],
            [
                'category_id' => FacilityCategoryEnum::MediaAndTechnology,
                'name' => 'WiFi',
            ],
            [
                'category_id' => FacilityCategoryEnum::MediaAndTechnology,
                'name' => 'TV',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create([
                'category_id' => data_get($facility, 'category_id'),
                'name' => data_get($facility, 'name'),
            ]);
        }
    }
}
