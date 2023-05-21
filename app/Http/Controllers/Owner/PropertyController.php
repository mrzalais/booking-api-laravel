<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Property;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('properties-manage');

        return response()->json(['success' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StorePropertyRequest $request): Property
    {
        $this->authorize('properties-manage');

        return Property::create($request->validated());
    }
}
