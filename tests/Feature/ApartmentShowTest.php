<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\City;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Property;
use App\Models\User;
use Tests\TestCase;

class ApartmentShowTest extends TestCase
{
    public function test_apartment_show_loads_apartment_with_facilities(): void
    {
        /** @var User $owner */
        $owner = User::factory()->owner()->create();

        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        /** @var Apartment $apartment */
        $apartment = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        /** @var FacilityCategory $firstCategory */
        $firstCategory = FacilityCategory::factory()->create(['name' => 'First category']);

        /** @var FacilityCategory $secondCategory */
        $secondCategory = FacilityCategory::factory()->create(['name' => 'Second category']);

        /** @var Facility $firstFacility */
        $firstFacility = Facility::factory()->create([
            'category_id' => $firstCategory->id,
            'name' => 'First facility',
        ]);

        /** @var Facility $secondFacility */
        $secondFacility = Facility::factory()->create([
            'category_id' => $firstCategory->id,
            'name' => 'First facility',
        ]);

        /** @var Facility $thirdFacility */
        $thirdFacility = Facility::factory()->create([
            'category_id' => $secondCategory->id,
            'name' => 'First facility',
        ]);

        $apartment->facilities()->attach([$firstFacility->id, $secondFacility->id, $thirdFacility->id]);

        $response = $this->getJson('/api/apartments/' . $apartment->id);
        $response->assertStatus(200);
        $response->assertJsonPath('name', $apartment->name);
        $response->assertJsonCount(2, 'facility_categories');

        $expectedFacilityArray = [
            $firstCategory->name => [
                $firstFacility->name,
                $secondFacility->name,
            ],
            $secondCategory->name => [
                $thirdFacility->name,
            ]
        ];

        $response->assertJsonFragment($expectedFacilityArray);
    }
}
