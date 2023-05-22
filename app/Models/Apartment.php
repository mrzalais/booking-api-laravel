<?php

namespace App\Models;

use Database\Factories\ApartmentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apartment
 *
 * @property int $id
 * @property int $property_id
 * @property string $name
 * @property int $capacity_adults
 * @property int $capacity_children
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Property $property
 * @method static ApartmentFactory factory($count = null, $state = [])
 * @method static Builder|Apartment newModelQuery()
 * @method static Builder|Apartment newQuery()
 * @method static Builder|Apartment query()
 * @method static Builder|Apartment whereCapacityAdults($value)
 * @method static Builder|Apartment whereCapacityChildren($value)
 * @method static Builder|Apartment whereCreatedAt($value)
 * @method static Builder|Apartment whereId($value)
 * @method static Builder|Apartment whereName($value)
 * @method static Builder|Apartment wherePropertyId($value)
 * @method static Builder|Apartment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'capacity_adults',
        'capacity_children',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
