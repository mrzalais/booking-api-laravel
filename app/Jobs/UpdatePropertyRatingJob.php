<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePropertyRatingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Booking $booking)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apartment = $this->booking->apartment;
        /** @var Property $property */
        $property = Property::with('bookings')->find($apartment->property_id);

        if (!$property) {
            return;
        }

        $property->update([
            'bookings_avg_rating' => $property->bookings()->avg('rating')
        ]);
    }
}
