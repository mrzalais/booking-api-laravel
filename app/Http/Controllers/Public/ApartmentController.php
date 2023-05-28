<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentDetailsResource;
use App\Http\Resources\ApartmentSearchResource;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __invoke(Apartment $apartment): ApartmentDetailsResource
    {
        $apartment->load('facilities.category');

        $apartment->setAttribute(
            key: 'facility_categories',
            value: $apartment->facilities->groupBy('category.name')->mapWithKeys(
                fn ($items, $key) => [$key => $items->pluck('name')]
            )
        );

        return new ApartmentDetailsResource($apartment);
    }
}
