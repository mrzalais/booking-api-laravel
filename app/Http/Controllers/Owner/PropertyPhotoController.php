<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Property;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * @group Owner
 * @subgroup Property photo management
 */
class PropertyPhotoController extends Controller
{
    /**
     * Add a photo to a property
     *
     * [Adds a photo to a property and returns the filename, thumbnail and position of the photo]
     *
     * @authenticated
     *
     * @response {"filename": "http://localhost:8000/storage/properties/1/photos/1/IMG_20190601_123456.jpg", "thumbnail": "http://localhost:8000/storage/properties/1/photos/1/conversions/thumbnail.jpg", "position": 1}
     * @response 422 {"message":"The photo must be an image.","errors":{"photo":["The photo must be an image."]}}
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Property $property, Request $request): array
    {
        $request->validate(['photo' => ['image', 'max:5000']]);

        if ($property->owner_id !== auth()->id()) {
            abort(403);
        }

        /** @var Media $photo */
        $photo = $property->addMediaFromRequest('photo')->toMediaCollection('photos');

        return [
            'filename' => $photo->getUrl(),
            'thumbnail' => $photo->getUrl('thumbnail'),
            'order' => $photo->order_column,
            'photo_id' => $photo->id,
        ];
    }

    /**
     * Reorder photos of a property
     *
     * [Reorders photos of a property and returns the new position of the photo]
     *
     * @authenticated
     *
     * @urlParam newPosition integer The new position of the photo. Example: 2
     *
     * @response {"newPosition": 2}
     */
    public function reorder(Property $property, Media $photo, int $newOrder): array
    {
        if ($property->owner_id !== auth()->id() || $photo->model_id !== $property->id) {
            abort(403);
        }

        $query = Media::query()
            ->where('model_type', 'App\Models\Property')
            ->where('model_id', $photo->model_id);
        $oldOrder = $photo->order_column;

        if ($newOrder < $oldOrder) {
            $query->whereBetween('order_column', [$newOrder, $oldOrder - 1])->increment('order_column');
        } else {
            $query->whereBetween('order_column', [$oldOrder + 1, $newOrder])->decrement('order_column');
        }

        $photo->order_column = $newOrder;
        $photo->save();

        return ['newOrder' => $photo->order_column];
    }
}
