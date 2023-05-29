<?php

namespace App\Models;

use App\Observers\PropertyObserver;
use Database\Factories\PropertyFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * App\Models\Property
 *
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property int $city_id
 * @property string $address_street
 * @property string|null $address_postcode
 * @property string|null $lat
 * @property string|null $long
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Apartment> $apartments
 * @property-read int|null $apartments_count
 * @property-read City $city
 * @property-read Attribute $address
 * @method static PropertyFactory factory($count = null, $state = [])
 * @method static Builder|Property newModelQuery()
 * @method static Builder|Property newQuery()
 * @method static Builder|Property query()
 * @method static Builder|Property whereAddressPostcode($value)
 * @method static Builder|Property whereAddressStreet($value)
 * @method static Builder|Property whereCityId($value)
 * @method static Builder|Property whereCreatedAt($value)
 * @method static Builder|Property whereId($value)
 * @method static Builder|Property whereLat($value)
 * @method static Builder|Property whereLong($value)
 * @method static Builder|Property whereName($value)
 * @method static Builder|Property whereOwnerId($value)
 * @method static Builder|Property whereUpdatedAt($value)
 * @property-read int|null $facilities_count
 * @mixin Eloquent
 */
class Property extends Model implements HasMedia
{
    use HasFactory;
    use HasEagerLimit;
    use InteractsWithMedia;

    protected $fillable = [
        'owner_id',
        'name',
        'city_id',
        'address_street',
        'address_postcode',
        'lat',
        'long',
    ];

    public static function booted(): void
    {
        parent::booted();

        self::observe(PropertyObserver::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function address(): Attribute
    {
        return new Attribute(
            fn() => implode(', ', [$this->address_street, $this->address_postcode, $this->city->name])
        );
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->width(800);
    }
}
