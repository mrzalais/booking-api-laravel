<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private const URI = '/api/auth/register';

    public function test_registration_fails_with_admin_role(): void
    {
        $password = $this->faker->password(8);

        $response = $this->postJson(
            uri: self::URI,
            data: [
                'name' => $this->faker->firstName,
                'email' => $this->faker->email,
                'password' => $password,
                'password_confirmation' => $password,
                'role_id' => Role::ROLE_ADMINISTRATOR,
            ]
        );

        $response->assertStatus(422);
    }

    public function test_registration_succeeds_with_owner_role(): void
    {
        $password = $this->faker->password(8);

        $response = $this->postJson(
            uri: self::URI,
            data: [
                'name' => $this->faker->firstName,
                'email' => $this->faker->email,
                'password' => $password,
                'password_confirmation' => $password,
                'role_id' => Role::ROLE_OWNER,
            ]
        );

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
        ]);
    }

    public function test_registration_succeeds_with_user_role(): void
    {
        $password = $this->faker->password(8);

        $response = $this->postJson(
            uri: self::URI,
            data: [
                'name' => $this->faker->firstName,
                'email' => $this->faker->email,
                'password' => $password,
                'password_confirmation' => $password,
                'role_id' => Role::ROLE_USER,
            ]
        );

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
        ]);
    }
}
