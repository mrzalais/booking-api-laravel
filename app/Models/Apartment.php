<?php

namespace App\Models;

use Database\Factories\ApartmentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

/**
 * App\Models\Apartment
 *
 * @property int $id
 * @property int|null $apartment_type_id
 * @property int $property_id
 * @property string $name
 * @property int $capacity_adults
 * @property int $capacity_children
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $size
 * @property int $bathrooms
 * @property-read ApartmentType|null $apartment_type
 * @property-read Property $property
 * @property-read Collection<int, Room> $rooms
 * @property-read int|null $rooms_count
 * @property-read Collection<int, Bed> $beds
 * @property-read int|null $beds_count
 * @property-read Attribute $bedsList
 * @property-read Collection<int, Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read Collection $facility_categories
 * @property-read Attribute $beds_list
 * @method static ApartmentFactory factory($count = null, $state = [])
 * @method static Builder|Apartment newModelQuery()
 * @method static Builder|Apartment newQuery()
 * @method static Builder|Apartment query()
 * @method static Builder|Apartment whereApartmentTypeId($value)
 * @method static Builder|Apartment whereBathrooms($value)
 * @method static Builder|Apartment whereCapacityAdults($value)
 * @method static Builder|Apartment whereCapacityChildren($value)
 * @method static Builder|Apartment whereCreatedAt($value)
 * @method static Builder|Apartment whereId($value)
 * @method static Builder|Apartment whereName($value)
 * @method static Builder|Apartment wherePropertyId($value)
 * @method static Builder|Apartment whereSize($value)
 * @method static Builder|Apartment whereUpdatedAt($value)
 * @property-read Collection<int, ApartmentPrice> $prices
 * @property-read int|null $prices_count
 * @mixin Eloquent
 */
class Apartment extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $fillable = [
        'property_id',
        'apartment_type_id',
        'name',
        'capacity_adults',
        'capacity_children',
        'size',
        'bathrooms',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function apartment_type(): BelongsTo
    {
        return $this->belongsTo(ApartmentType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function beds(): HasManyThrough
    {
        return $this->hasManyThrough(Bed::class, Room::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ApartmentPrice::class);
    }

    public function calculatePriceForDates(Carbon|string|null $startDate, Carbon|string|null $endDate): int
    {
        // Convert to Carbon if not already
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $cost = 0;

        while ($startDate->lte($endDate)) {
            $cost += $this->prices->where(function (ApartmentPrice $price) use ($startDate) {
                return $price->start_date->lte($startDate) && $price->end_date->gte($startDate);
            })->value('price');
            $startDate->addDay();
        }

        return $cost;
    }

    public function bedsList(): Attribute
    {
        $allBeds = $this->beds;
        $bedsByType = $allBeds->groupBy('bed_type.name');
        $bedsList = '';

        if ($bedsByType->count() == 1) {
            $bedsList = $allBeds->count() . ' ' . str($bedsByType->keys()[0])->plural($allBeds->count());
        } elseif ($bedsByType->count() > 1) {
            $bedsList = $allBeds->count() . ' ' . str('bed')->plural($allBeds->count());
            $bedsListArray = [];
            foreach ($bedsByType as $bedType => $beds) {
                $bedsListArray[] = $beds->count() . ' ' . str($bedType)->plural($beds->count());
            }
            $bedsList .= ' (' . implode(', ', $bedsListArray) . ')';
        }

        return new Attribute(
            get: fn() => $bedsList
        );
    }
}
