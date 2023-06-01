<?php

namespace App\Models;

use App\Traits\ValidForRange;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property int $apartment_id
 * @property int $user_id
 * @property string $start_date
 * @property string $end_date
 * @property int $guests_adults
 * @property int $guests_children
 * @property int $total_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Apartment $apartment
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking onlyTrashed()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereApartmentId($value)
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereDeletedAt($value)
 * @method static Builder|Booking whereEndDate($value)
 * @method static Builder|Booking whereGuestsAdults($value)
 * @method static Builder|Booking whereGuestsChildren($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking whereStartDate($value)
 * @method static Builder|Booking whereTotalPrice($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 * @method static Builder|Booking whereUserId($value)
 * @method static Builder|Booking withTrashed()
 * @method static Builder|Booking withoutTrashed()
 * @method static Builder|Booking validForRange(array $range = [])
 * @property int|null $rating
 * @property string|null $review_comment
 * @method static Builder|Booking whereRating($value)
 * @method static Builder|Booking whereReviewComment($value)
 * @mixin Eloquent
 */
class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidForRange;

    protected $fillable = [
        'apartment_id',
        'user_id',
        'start_date',
        'end_date',
        'guests_adults',
        'guests_children',
        'total_price',
        'rating',
        'review_comment',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
