<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::debug('PropertyPhotoController::store', [$property->owner_id, auth()->id()]);
            abort(403);
        }

        $photo = $property->addMediaFromRequest('photo')->toMediaCollection('photos');

        return [
            'filename' => $photo->getUrl(),
            'thumbnail' => $photo->getUrl('thumbnail'),
        ];
    }
}
