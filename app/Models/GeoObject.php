<?php

namespace App\Models;

use Database\Factories\GeoObjectFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\GeoObject
 *
 * @method static Builder|GeoObject newModelQuery()
 * @method static Builder|GeoObject newQuery()
 * @method static Builder|GeoObject query()
 * @property int $id
 * @property int|null $city_id
 * @property string $name
 * @property string|null $lat
 * @property string|null $long
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|GeoObject whereCityId($value)
 * @method static Builder|GeoObject whereCreatedAt($value)
 * @method static Builder|GeoObject whereId($value)
 * @method static Builder|GeoObject whereLat($value)
 * @method static Builder|GeoObject whereLong($value)
 * @method static Builder|GeoObject whereName($value)
 * @method static Builder|GeoObject whereUpdatedAt($value)
 * @method static GeoObjectFactory factory($count = null, $state = [])
 * @mixin Eloquent
 */
class GeoObject extends Model
{
    use HasFactory;

    protected $fillable = ['city_id', 'name', 'lat', 'long'];
}
