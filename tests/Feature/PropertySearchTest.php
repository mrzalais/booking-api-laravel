<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
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
        $response->assertJsonCount(1, 'properties.data');
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
        $response->assertJsonCount(1, 'properties.data');
        $response->assertJsonFragment(['id' => $propertyInCountry->id]);
    }

    public function test_property_search_by_geo_object_returns_correct_results(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        /** @var City $city */
        $city = City::factory()->create();
        /** @var GeoObject $geoObject */
        $geoObject = GeoObject::factory()->create();

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
        $response->assertJsonPath('properties.data.0.apartments.0.name', $largeApartment->name);
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
        $response->assertJsonCount(1, 'properties.data');
        $response->assertJsonCount(1, 'properties.data.0.apartments');
        $response->assertJsonPath('properties.data.0.apartments.0.beds_list', '');
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
        $response->assertJsonPath('properties.data.0.apartments.0.beds_list', '1 ' . $bedType->name);
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
        $response->assertJsonPath('properties.data.0.apartments.0.beds_list', '2 ' . str($bedType->name)->plural());
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
        $response->assertJsonPath('properties.data.0.apartments.0.beds_list', '2 ' . str($bedType->name)->plural());

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $bedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.data.0.apartments.0.beds_list', '3 ' . str($bedType->name)->plural());

        /** @var BedType $secondBedType */
        $secondBedType = BedType::factory()->create(['name' => 'Medium size test bed']);

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $secondBedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath(
            path: 'properties.data.0.apartments.0.beds_list',
            expect: '4 beds (3 ' . str($bedType->name)->plural() . ', 1 ' . $secondBedType->name . ')'
        );

        Bed::factory()->create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $secondBedType->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id);
        $response->assertStatus(200);
        $response->assertJsonPath(
            path: 'properties.data.0.apartments.0.beds_list',
            expect: '5 beds (3 ' . str($bedType->name)->plural() . ', 2 ' . str($secondBedType->name)->plural() . ')'
        );
    }

    public function test_property_search_returns_one_best_apartment_per_property(): void
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

        /** @var Property $property2 */
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);
        Apartment::factory()->create([
            'name' => 'Large apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
        Apartment::factory()->create([
            'name' => 'Mid size apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        Apartment::factory()->create([
            'name' => 'Small apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties.data.0.apartments');
        $response->assertJsonPath('properties.data.0.apartments.0.name', $mediumApartment->name);
    }

    public function test_property_search_filters_by_facilities(): void
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

        /** @var Property $property2 */
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        // First case - no facilities exist
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties.data');

        // Second case - filter by facility, 0 properties returned
        /** @var Facility $facility */
        $facility = Facility::factory()->create(['name' => 'First facility']);
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'properties.data');

        // Third case - attach facility to property, filter by facility, 1 property returned
        $property->facilities()->attach($facility->id);
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties.data');

        // Fourth case - attach facility to a DIFFERENT property, filter by facility, 2 properties returned
        $property2->facilities()->attach($facility->id);
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties.data');
    }

    public function test_property_search_filters_by_price()
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
        /** @var Apartment $cheapApartment */
        $cheapApartment = Apartment::factory()->create([
            'name' => 'Cheap apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $cheapApartment->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 70,
        ]);
        /** @var Property $property2 */
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);
        $expensiveApartment = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $expensiveApartment->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 130,
        ]);

        // First case - no price range: both returned
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties.data');

        // First case - min price set: 1 returned
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&price_from=100');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties.data');

        // Second case - max price set: 1 returned
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&price_to=100');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties.data');

        // Third case - both min and max price set: 2 returned
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&price_from=50&price_to=150');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties.data');

        // Fourth case - both min and max price set narrow: 0 returned
        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1&price_from=80&price_to=100');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'properties.data');
    }

    public function test_properties_show_correct_rating_and_ordered_by_it()
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
        /** @var Apartment $apartment1 */
        $apartment1 = Apartment::factory()->create([
            'name' => 'Cheap apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        /** @var Property $property2 */
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);
        /** @var Apartment $apartment2 */
        $apartment2 = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        /** @var User $user1 */
        $user1 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        /** @var User $user2 */
        $user2 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        Booking::create([
            'apartment_id' => $apartment1->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
            'rating' => 7
        ]);
        Booking::create([
            'apartment_id' => $apartment2->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
            'rating' => 9
        ]);
        Booking::create([
            'apartment_id' => $apartment2->id,
            'user_id' => $user2->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
            'rating' => 7
        ]);

        $response = $this->getJson('/api/search?city=' . $city->id . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties.data');
        $this->assertEquals(8, $response->json('properties.data')[0]['avg_rating']);
        $this->assertEquals(7, $response->json('properties.data')[1]['avg_rating']);
    }
}
