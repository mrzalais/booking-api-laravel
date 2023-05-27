<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySearchResource;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Property $property, Request $request): PropertySearchResource
    {
        $property->load('apartments.facilities');

        if ($request->input('adults') && $request->input('children')) {
            $property->load(['apartments' => function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('capacity_adults', '>=', $request->input('adults'))
                    ->where('capacity_children', '>=', $request->input('children'))
                    ->orderBy('capacity_adults')
                    ->orderBy('capacity_children');
            }, 'apartments.facilities']);
        }

        return new PropertySearchResource($property);
    }
}
