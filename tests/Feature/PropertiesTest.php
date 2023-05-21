<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Services\GeocoderService;
use Mockery;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    private const URI = '/api/owner/properties';
    private string $streetAddress = '';

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

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

    public function test_property_owner_can_add_property(): void
    {
        $this->streetAddress = $this->faker->streetAddress;

        $this->app->bind(GeocoderService::class, function () {
            $mock = Mockery::mock(GeocoderService::class);

            $mock->shouldReceive('getCoordinatesForAddress')
                ->andReturn($this->getTestCoordinates());

            return $mock;
        });

        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        $response = $this->actingAs($owner)->postJson(self::URI, [
            'name' => 'My property',
            'city_id' => City::value('id'),
            'address_street' => $this->streetAddress,
            'address_postcode' => $this->faker->postcode,
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['name' => 'My property']);
        $response->assertJsonFragment(['address_street' => $this->streetAddress]);
    }

    private function getTestCoordinates(): array
    {
        return [
            [
                'lat' => 40.7127753,
                'lng' => -74.0059728,
                'accuracy' => 'ROOFTOP',
                'formatted_address' => $this->streetAddress . ', 22945 New York, United States',
                'viewport' => [
                    'northeast' => [
                        'lat' => 40.7127753,
                        'lng' => -74.0059728,
                    ],
                    'southwest' => [
                        'lat' => 40.7127753,
                        'lng' => -74.0059728,
                    ],
                ],
                'address_components' => [],
                'partial_match' => false,
                'place_id' => null,
            ],
        ];
    }
}
