<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    private const URI = '/api/user/bookings';

    public function test_user_has_access_to_bookings_feature(): void
    {
        $user = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $response = $this->actingAs($user)->getJson(self::URI);

        $response->assertStatus(200);
    }

    public function test_property_owner_does_not_have_access_to_bookings_feature(): void
    {
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        $response = $this->actingAs($owner)->getJson(self::URI);

        $response->assertStatus(403);
    }

    private function create_apartment(): Apartment
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

        return Apartment::factory()->create([
            'name' => 'Apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
    }

    public function test_user_can_book_apartment_successfully_but_not_twice()
    {
        $user = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $apartment = $this->create_apartment();

        $bookingParameters = [
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDay()->toDateTimeString(),
            'end_date' => now()->addDays(2)->toDateTimeString(),
            'guests_adults' => 2,
            'guests_children' => 1,
        ];
        $response = $this->actingAs($user)->postJson(self::URI, $bookingParameters);
        $response->assertStatus(201);

        $response = $this->actingAs($user)->postJson(self::URI, $bookingParameters);
        $response->assertStatus(422);

        $bookingParameters['start_date'] = now()->addDays(3);
        $bookingParameters['end_date'] = now()->addDays(4);
        $bookingParameters['guests_adults'] = 5;
        $response = $this->actingAs($user)->postJson(self::URI, $bookingParameters);
        $response->assertStatus(422);
    }
}
