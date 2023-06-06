<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    private const URI = '/api/user/bookings/';

    public function test_user_has_access_to_bookings_feature(): void
    {
        $user = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $response = $this->actingAs($user)->getJson(self::URI);

        $response->assertStatus(200);
    }

    public function test_property_owner_does_not_have_access_to_bookings_feature(): void
    {
        $owner = User::factory()->owner()->create();
        $response = $this->actingAs($owner)->getJson(self::URI);

        $response->assertStatus(403);
    }

    private function create_apartment(): Apartment
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

        return Apartment::factory()->create([
            'name' => 'Apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
    }

    public function test_user_can_book_apartment_successfully_but_not_twice(): void
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

    public function test_user_can_get_only_their_bookings(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        /** @var User $user2 */
        $user2 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $apartment = $this->create_apartment();
        $booking1 = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
        ]);
        $booking2 = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user2->id,
            'start_date' => now()->addDay(3),
            'end_date' => now()->addDays(4),
            'guests_adults' => 2,
            'guests_children' => 1,
        ]);

        $response = $this->actingAs($user1)->getJson(self::URI);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['guests_adults' => 1]);

        $response = $this->actingAs($user1)->getJson(self::URI . $booking1->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['guests_adults' => 1]);

        $response = $this->actingAs($user1)->getJson(self::URI . $booking2->id);
        $response->assertStatus(403);
    }

    public function test_user_can_cancel_their_booking_but_still_view_it(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        /** @var User $user2 */
        $user2 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $apartment = $this->create_apartment();
        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
        ]);

        $response = $this->actingAs($user2)->deleteJson(self::URI . $booking->id);
        $response->assertStatus(403);

        $response = $this->actingAs($user1)->deleteJson(self::URI . $booking->id);
        $response->assertStatus(204);

        $response = $this->actingAs($user1)->getJson(self::URI);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['cancelled_at' => now()->toDateString()]);

        $response = $this->actingAs($user1)->getJson(self::URI . $booking->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['cancelled_at' => now()->toDateString()]);
    }

    public function test_user_can_post_rating_for_their_booking(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        /** @var User $user2 */
        $user2 = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $apartment = $this->create_apartment();
        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guests_adults' => 1,
            'guests_children' => 0,
        ]);

        $response = $this->actingAs($user2)->putJson(self::URI . $booking->id, []);
        $response->assertStatus(403);

        $response = $this->actingAs($user1)->putJson(self::URI . $booking->id, [
            'rating' => 11
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user1)->putJson(self::URI . $booking->id, [
            'rating' => 10,
            'review_comment' => 'Too short comment.'
        ]);
        $response->assertStatus(422);

        $correctData = [
            'rating' => 10,
            'review_comment' => 'Comment with a good length to be accepted.'
        ];
        $response = $this->actingAs($user1)->putJson(self::URI . $booking->id, $correctData);
        $response->assertStatus(200);
        $response->assertJsonFragment($correctData);
    }
}
