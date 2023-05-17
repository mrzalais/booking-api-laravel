<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    private const URI = '/api/owner/properties';

    public function test_property_owner_has_access_to_properties_feature(): void
    {
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        $response = $this->actingAs($owner)->getJson(self::URI);

        $response->assertStatus(200);
    }

    public function test_user_does_not_have_access_to_properties_feature(): void
    {
        $user = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $response = $this->actingAs($user)->getJson(self::URI);

        $response->assertStatus(403);
    }
}
