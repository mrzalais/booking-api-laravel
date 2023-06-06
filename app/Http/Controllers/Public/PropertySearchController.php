<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySearchResource;
use App\Models\ApartmentPrice;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @group Public
 * @subgroup Property search
 */
class PropertySearchController extends Controller
{
    /**
     * Search properties
     *
     * [Returns a list of filtered properties]
     *
     * @queryParam city int City ID. Example: 1
     * @queryParam country int Country ID. Example: 4
     * @queryParam geoobject int Geoobject ID. Example: 1
     * @queryParam adults int Number of adults. Example: 2
     * @queryParam children int Number of children. Example: 1
     * @queryParam facilities array List of facility IDs. Example: [1, 2, 3]
     * @queryParam price_from int Minimum price. Example: 100
     * @queryParam price_to int Maximum price. Example: 200
     * @queryParam start_date date Start date. Example: 2024-01-01
     * @queryParam end_date date End date. Example: 2024-01-03
     *
     * @response {"properties":{"data":[{"id":2,"name":"Qui velit ea.","address":"2392 Zemlak Port Suite 655, 16225-4383, New York","lat":"-54.8191470","long":"-70.2183380","apartments":[{"name":"Mid size apartment","type":null,"size":null,"beds_list":"","bathrooms":0,"price":0}],"photos":[],"avg_rating":8},{"id":1,"name":"Provident enim est.","address":"1487 Ignacio Alley Suite 794, 74215, New York","lat":"13.2359740","long":"-74.2809120","apartments":[{"name":"Cheap apartment","type":null,"size":null,"beds_list":"","bathrooms":0,"price":0}],"photos":[],"avg_rating":7}],"links":{"first":"http:\/\/booking-com-simulation-laravel.test\/api\/search?city=1&adults=2&children=1&page=1","last":"http:\/\/booking-com-simulation-laravel.test\/api\/search?city=1&adults=2&children=1&page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/booking-com-simulation-laravel.test\/api\/search?city=1&adults=2&children=1&page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"path":"http:\/\/booking-com-simulation-laravel.test\/api\/search","per_page":10,"to":2,"total":2}},"facilities":[]}
     */
    public function __invoke(Request $request): array
    {
        $propertyQuery = Property::query()
            ->with([
                'city',
                'apartments.apartment_type',
                'apartments.beds.bed_type',
                'apartments.prices' => function ($query) use ($request) {
                    /** @var Builder|ApartmentPrice $query */
                    $query->validForRange([
                        $request->input('start_date') ?? now()->addDay()->toDateString(),
                        $request->input('end_date') ?? now()->addDays(2)->toDateString(),
                    ]);
                },
                'facilities',
                'media' => fn($query) => $query->orderBy('order_column'),
            ])
            ->when($request->input('city'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('city_id', $request->input('city'));
            })
            ->when($request->input('country'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->whereHas(
                    'city',
                    fn($query) => $query->where('country_id', $request->input('country'))
                );
            })
            ->when($request->input('geoObject'), function (Builder $query) use ($request) {
                /** @var GeoObject $geoObject */
                $geoObject = Geoobject::find($request->input('geoObject'));
                if ($geoObject) {
                    $condition = "(
                        6371 * acos(
                            cos(radians(" . $geoObject->lat . "))
                            * cos(radians(`lat`))
                            * cos(radians(`long`) - radians(" . $geoObject->long . "))
                            + sin(radians(" . $geoObject->lat . ")) * sin(radians(`lat`))
                        ) < 10
                    )";
                    $query->whereRaw($condition);
                }
            })
            ->when($request->input('adults') && $request->input('children'), function ($query) use ($request) {
                $query->withWhereHas('apartments', function ($query) use ($request) {
                    /** @var Builder $query */
                    $query->where('capacity_adults', '>=', $request->input('adults'))
                        ->where('capacity_children', '>=', $request->input('children'))
                        ->orderBy('capacity_adults')
                        ->orderBy('capacity_children')
                        ->take(1);
                });
            })
            ->when($request->input('facilities'), function ($query) use ($request) {
                $query->whereHas('facilities', function ($query) use ($request) {
                    /** @var Builder $query */
                    $query->whereIn('facilities.id', $request->input('facilities'));
                });
            })
            ->when($request->input('price_from'), function ($query) use ($request) {
                $query->whereHas('apartments.prices', function ($query) use ($request) {
                    $query->where('price', '>=', $request->input('price_from'));
                });
            })
            ->when($request->input('price_to'), function ($query) use ($request) {
                $query->whereHas('apartments.prices', function ($query) use ($request) {
                    $query->where('price', '<=', $request->input('price_to'));
                });
            });

        $facilities = Facility::query()
            ->withCount([
                'properties' => function ($property) use ($propertyQuery) {
                    $property->whereIn('id', $propertyQuery->pluck('id'));
                }
            ])
            ->get()
            ->where('properties_count', '>', 0)
            ->sortByDesc('properties_count')
            ->pluck('properties_count', 'name');

        $properties = $propertyQuery
            ->orderBy('bookings_avg_rating', 'desc')
            ->paginate(10)
            ->withQueryString();

        return [
            'properties' => PropertySearchResource::collection($properties)
                ->response()
                ->getData(true),
            'facilities' => $facilities,
        ];
    }
}
