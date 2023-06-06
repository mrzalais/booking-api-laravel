<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Media;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Storage;
use Tests\TestCase;

class PropertiesPhotosTest extends TestCase
{
    private const URI = '/api/owner/properties/{property}/photos';

    public function test_property_owner_can_add_photo_to_property(): void
    {
        Storage::fake();

        /** @var User $owner */
        $owner = User::factory()->owner()->create();

        /** @var City $city */
        $city = City::factory()->create();

        /** @var Property $property */
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($owner)->postJson(str_replace('{property}', $property->id, self::URI), [
            'photo' => UploadedFile::fake()->image('photo.png')
        ]);

        /** @var Media $upload */
        $upload = SpatieMedia::query()->where(['model_id' => $property->id])->first();

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'filename' => config('app.url') . '/storage/' . $upload->id . '/photo.png',
            'thumbnail' => config('app.url') . '/storage/' . $upload->id . '/conversions/photo-thumbnail.jpg',
        ]);
    }
}
