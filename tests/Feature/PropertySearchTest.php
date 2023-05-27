<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\City;
use App\Models\Country;
use App\Models\GeoObject;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Tests\TestCase;

class PropertySearchTest extends TestCase
{
    public function test_property_search_by_city_returns_correct_results(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);

        /** @var City $cityA */
        $cityA = City::factory()->create();

        /** @var City $cityB */
        $cityB = City::factory()->create();


        /** @var Property $propertyInCity */
        $propertyInCity = Property::factory()->create([
            'name' => $cityA->name,
            'owner_id' => $owner->id,
            'city_id' => $cityA->id
        ]);

        Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityB->id
        ]);

        $response = $this->getJson('/api/search?city=' . $cityA->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $propertyInCity->id]);
    }

    public function test_property_search_by_country_returns_correct_results(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);

        /** @var Country $countryA */
        $countryA = Country::factory()->create();

        /** @var Country $countryB */
        $countryB = Country::factory()->create();

        /** @var City $cityA */
        $cityA = City::factory()->create([
            'country_id' => $countryA->id,
        ]);

        /** @var City $cityB */
        $cityB = City::factory()->create([
            'country_id' => $countryB->id,
        ]);

        /** @var Property $propertyInCountry */
        $propertyInCountry = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityA->id
        ]);

        Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityB->id
        ]);

        $response = $this->getJson('/api/search?country=' . $countryA->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $propertyInCountry->id]);
    }

    public function test_property_search_by_geoobject_returns_correct_results(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        /** @var City $city */
        $city = City::factory()->create();
        $geoObject = GeoObject::first();

        /** @var Property $propertyNear */
        $propertyNear = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
            'lat' => $geoObject->lat,
            'long' => $geoObject->long,
        ]);

        /** @var Property $propertyFar */
        $propertyFar = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
            'lat' => $geoObject->lat + 10,
            'long' => $geoObject->long + 10,
        ]);

        $response = $this->getJson('/api/search?geoObject=' . $geoObject->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $propertyNear->id]);
        $response->assertJsonMissing(['id' => $propertyFar->id]);
    }

    public function test_property_search_by_capacity_returns_correct_results(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $propertyWithSmallApartment */
        $propertyWithSmallApartment = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        Apartment::factory()->create([
            'property_id' => $propertyWithSmallApartment->id,
            'capacity_adults' => 1,
            'capacity_children' => 1,
        ]);

        /** @var Property $propertyWithLargeApartment */
        $propertyWithLargeApartment = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        Apartment::factory()->create([
            'property_id' => $propertyWithLargeApartment->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $propertyWithLargeApartment->id]);
        $response->assertJsonMissing(['id' => $propertyWithSmallApartment->id]);
    }

    public function test_property_search_by_capacity_returns_only_suitable_apartments(): void
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

        /** @var Apartment $smallApartment */
        $smallApartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        /** @var Apartment $largeApartment */
        $largeApartment = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.name', $largeApartment->name);
        $response->assertJsonMissing(['name' => $smallApartment->name]);
    }

    public function test_bed_list_is_empty_if_no_beds(): void
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
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonCount(1, '0.apartments');
        $response->assertJsonPath('0.apartments.0.beds_list', '');
    }

    public function test_bed_list_has_one_bed(): void
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

        /** @var Apartment $apartment */
        $apartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
            'apartment_id' => $apartment->id,
            'name' => 'Bedroom',
        ]);

        /** @var BedType $bedType */
        $bedType = BedType::factory()->create(['name' => 'Small test bed']);

        Bed::factory()->create([
            'room_id' => $room->id,
            'bed_type_id' => $bedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.beds_list', '1 ' . $bedType->name);
    }

    public function test_bed_list_has_two_beds(): void
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

        /** @var Apartment $apartment */
        $apartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
            'apartment_id' => $apartment->id,
            'name' => 'Bedroom',
        ]);

        /** @var BedType $bedType */
        $bedType = BedType::factory()->create(['name' => 'Small test bed']);

        Bed::factory()->count(2)->create([
            'room_id' => $room->id,
            'bed_type_id' => $bedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.beds_list', '2 ' . str($bedType->name)->plural());
    }

    public function test_bed_list_has_multiple_beds_within_multiple_rooms(): void
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

        /** @var Apartment $apartment */
        $apartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
            'apartment_id' => $apartment->id,
            'name' => 'Bedroom',
        ]);

        /** @var BedType $bedType */
        $bedType = BedType::factory()->create(['name' => 'Small test bed']);

        Bed::factory()->count(2)->create([
            'room_id' => $room->id,
            'bed_type_id' => $bedType->id,
        ]);

        /** @var Room $secondRoom */
        $secondRoom = Room::factory()->create([
            'apartment_id' => $apartment->id,
            'name' => 'Living room',
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.beds_list', '2 ' . str($bedType->name)->plural());

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $bedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.beds_list', '3 ' . str($bedType->name)->plural());

        /** @var BedType $secondBedType */
        $secondBedType = BedType::factory()->create(['name' => 'Medium size test bed']);

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $secondBedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath(
            path: '0.apartments.0.beds_list',
            expect: '4 beds (3 ' . str($bedType->name)->plural() . ', 1 ' . $secondBedType->name . ')'
        );

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $secondBedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath(
            path: '0.apartments.0.beds_list',
            expect: '5 beds (3 ' . str($bedType->name)->plural() . ', 2 ' . str($secondBedType->name)->plural() . ')'
        );
    }

}
