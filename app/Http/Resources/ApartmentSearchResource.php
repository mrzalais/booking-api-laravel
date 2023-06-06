<?php

namespace App\Http\Resources;

use App\Models\Apartment;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Apartment|ApartmentSearchResource $this */
        return [
            'name' => $this->name,
            'type' => $this->apartment_type?->name,
            'size' => $this->size,
            'beds_list' => $this->bedsList,
            'bathrooms' => $this->bathrooms,
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'price' => (new PricingService())->calculateApartmentPriceForDates(
                $this->prices,
                $request->input('start_date'),
                $request->input('end_date')
            )
        ];
    }
}
