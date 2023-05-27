<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\City;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PropertyShowTest extends TestCase
{
    public function test_property_show_loads_property_correctly(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);

        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        /** @var Apartment $mediumApartment */
        $mediumApartment = Apartment::factory()->create([
            'name' => 'Medium apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        /** @var FacilityCategory $facilityCategory */
        $facilityCategory = FacilityCategory::factory()->create();

        /** @var Facility $facility */
        $facility = Facility::factory()->create(['category_id' => $facilityCategory->id]);

        $mediumApartment->facilities()->attach($facility->id);

        $response = $this->getJson('/api/properties/'.$property->id);
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'apartments');
        $response->assertJsonPath('name', $property->name);

        $response = $this->getJson('/api/properties/'.$property->id.'?adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'apartments');
        $response->assertJsonPath('name', $property->name);
        $response->assertJsonPath('apartments.0.facilities.0.name', $facility->name);
        $response->assertJsonCount(0, 'apartments.1.facilities');

        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.facilities', NULL);
    }
}
