<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Jobs\UpdatePropertyRatingJob;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group User
 * @subgroup Bookings
 */
class BookingController extends Controller
{
    /**
     * List of user bookings
     *
     * [Returns preview list of all user bookings]
     *
     * @authenticated
     *
     * @response {"id":1,"apartment_name":"Fugiat saepe sed.: Apartment","start_date":"2023-05-11","end_date":"2023-05-12","guests_adults":1,"guests_children":0,"total_price":0,"cancelled_at":null,"rating":null,"review_comment":null}
     *
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('bookings-manage');

        /** @var User $user */
        $user = auth()->user();

        $bookings = $user->bookings()
            ->with('apartment.property')
            ->withTrashed()
            ->orderBy('start_date')
            ->get();

        return BookingResource::collection($bookings);
    }

    /**
     * Create new booking
     *
     * [Creates new booking for authenticated user]
     *
     * @authenticated
     *
     * @response 201 {"id":1,"apartment_name":"Hic consequatur qui.: Apartment","start_date":"2023-05-11 08:00:51","end_date":"2023-05-12 08:00:51","guests_adults":2,"guests_children":1,"total_price":0,"cancelled_at":null,"rating":null,"review_comment":null}
     */
    public function store(StoreBookingRequest $request): BookingResource
    {
        /** @var User $user */
        $user = auth()->user();
        $booking = $user->bookings()->create($request->validated());

        return new BookingResource($booking);
    }

    /**
     * View booking
     *
     * [Returns details about a booking]
     *
     * @authenticated
     *
     * @response {"id":1,"apartment_name":"Hic consequatur qui.: Apartment","start_date":"2023-05-11 08:00:51","end_date":"2023-05-12 08:00:51","guests_adults":2,"guests_children":1,"total_price":0,"cancelled_at":null,"rating":null,"review_comment":null}
     *
     * @throws AuthorizationException
     */
    public function show(Booking $booking): BookingResource
    {
        $this->authorize('bookings-manage');

        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        return new BookingResource($booking);
    }

    /**
     * Delete booking
     *
     * [Deletes a booking]
     *
     * @authenticated
     *
     * @response {}
     *
     * @throws AuthorizationException
     */
    public function destroy(Booking $booking): Response
    {
        $this->authorize('bookings-manage');

        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        $booking->delete();

        return response()->noContent();
    }

    /**
     * Update existing booking rating
     *
     * [Updates booking with new details]
     *
     * @authenticated
     *
     * @response {"id":1,"apartment_name":"Hic consequatur qui.: Apartment","start_date":"2023-05-11 08:00:51","end_date":"2023-05-12 08:00:51","guests_adults":2,"guests_children":1,"total_price":0,"cancelled_at":null,"rating":null,"review_comment":null}
     */
    public function update(Booking $booking, UpdateBookingRequest $request): BookingResource
    {
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }

        $booking->update($request->validated());

        dispatch(new UpdatePropertyRatingJob($booking));

        return new BookingResource($booking);
    }
}
