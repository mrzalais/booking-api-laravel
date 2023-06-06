<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentDetailsResource;
use App\Http\Resources\ApartmentSearchResource;
use App\Models\Apartment;
use Illuminate\Http\Request;

/**
 * @group Public
 * @subgroup Apartments
 */
class ApartmentController extends Controller
{
    /**
     * Get apartment details
     *
     * [Returns details about a specific apartment]
     *
     * @response {"name":"Large apartment","type":null,"size":null,"beds_list":"","bathrooms":0,"facility_categories":{"First category":["First facility","Second facility"],"Second category":["Third facility"]}}
     */
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
