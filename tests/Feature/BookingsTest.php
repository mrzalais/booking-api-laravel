<?php

namespace Tests\Feature;

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
}
