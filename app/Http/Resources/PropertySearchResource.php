<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertySearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Property $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'long' => $this->long,
            'apartments' => ApartmentSearchResource::collection($this->apartments),
            'photos' => $this->media->map(fn($media) => $media->getUrl('thumbnail')),
        ];
    }
}
