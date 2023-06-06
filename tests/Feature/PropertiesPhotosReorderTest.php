<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Media;
use App\Models\Property;
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
        $owner = User::factory()->owner()->create();

        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->withImages()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        $mediaCollection = $property->getMedia('images');

        /** @var Media $photo1 */
        $photo1 = $mediaCollection->first();
        /** @var Media $photo2 */
        $photo2 = $mediaCollection->last();

        $newOrder = $photo1->order_column + 1;

        $response = $this->actingAs($owner)->postJson(str_replace(
                search: ['{property}', '{photo}', '{newOrder}'],
                replace: [$property->id, $photo1->id, $newOrder],
                subject: self::URI
        ));
        $response->assertStatus(200);
        $response->assertJsonFragment(['newOrder' => $newOrder]);

        $this->assertDatabaseHas('media', ['file_name' => 'photo1.png', 'order_column' => $photo2->order_column]);
        $this->assertDatabaseHas('media', ['file_name' => 'photo2.png', 'order_column' => $photo1->order_column]);
    }
}
