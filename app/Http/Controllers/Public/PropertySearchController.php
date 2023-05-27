<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySearchResource;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertySearchController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $properties = Property::query()
        ->with([
            'city',
            'apartments.apartment_type',
            'apartments.rooms.beds.bed_type'
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
            ->get();

        return PropertySearchResource::collection($properties);
    }
}
