<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Property::with('city')
            ->when($request->input('city'), function (Builder $query) use ($request) {
                $query->where('city_id', $request->input('city'));
            })
            ->when($request->input('country'), function (Builder $query) use ($request) {
                $query->whereHas(
                    'city',
                    fn($query) => $query->where('country_id', $request->input('country'))
                );
            })
            ->when($request->input('geoObject'), function($query) use ($request) {
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
            ->get();
    }
}
