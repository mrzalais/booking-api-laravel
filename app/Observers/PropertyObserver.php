<?php

namespace App\Observers;

use App\Models\Property;
use App\Services\GeocoderService;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        if (auth()->check()) {
            $property->owner_id = auth()->id();
        }

        if (is_null($property->lat) && is_null($property->long)) {
            $fullAddress = $property->address_street . ', '
                . $property->address_postcode . ', '
                . $property->city->name . ', '
                . $property->city->country->name;

            /** @var GeocoderService $geocoderService */
            $geocoderService = app(GeocoderService::class);

            $result = $geocoderService->getCoordinatesForAddress($fullAddress);

            $property->lat = data_get($result, 'lat');
            $property->long =  data_get($result, 'lng');
        }
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        //
    }
}
