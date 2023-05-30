<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class PropertiesPhotosReorderTest extends TestCase
{
    private const URI = '/api/owner/properties/{property}/photos/{photo}/reorder/{newOrder}';

    public function test_property_owner_can_reorder_photos_in_property(): void
    {
        Storage::fake();

        /** @var User $owner */
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);

        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        $photo1 = $this->actingAs($owner)->postJson('/api/owner/properties/' . $property->id . '/photos', [
            'photo' => UploadedFile::fake()->image('photo1.png')
        ]);
        $photo2 = $this->actingAs($owner)->postJson('/api/owner/properties/' . $property->id . '/photos', [
            'photo' => UploadedFile::fake()->image('photo2.png')
        ]);

        $newOrder = $photo1->json('order') + 1;

        $response = $this->actingAs($owner)->postJson(str_replace(
                search: ['{property}', '{photo}', '{newOrder}'],
                replace: [$property->id, data_get($photo1->getOriginalContent(), 'photo_id'), $newOrder],
                subject: self::URI
        ));
        $response->assertStatus(200);
        $response->assertJsonFragment(['newOrder' => $newOrder]);

        $this->assertDatabaseHas('media', ['file_name' => 'photo1.png', 'order_column' => $photo2->json('order')]);
        $this->assertDatabaseHas('media', ['file_name' => 'photo2.png', 'order_column' => $photo1->json('order')]);
    }
}
