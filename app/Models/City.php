<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\City
 *
 * @property-read Country|null $country
 * @method static Builder|City newModelQuery()
 * @method static Builder|City newQuery()
 * @method static Builder|City query()
 * @mixin Eloquent
 */
class City extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name', 'lat', 'long'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
