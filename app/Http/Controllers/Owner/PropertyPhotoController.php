<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Property;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class PropertyPhotoController extends Controller
{
    /**
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
