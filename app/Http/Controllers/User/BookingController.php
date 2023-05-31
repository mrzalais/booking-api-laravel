<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('bookings-manage');

        return response()->json(['success' => true]);
    }

    public function store(StoreBookingRequest $request): BookingResource
    {
        /** @var User $user */
        $user = auth()->user();
        $booking = $user->bookings()->create($request->validated());

        return new BookingResource($booking);
    }
}
