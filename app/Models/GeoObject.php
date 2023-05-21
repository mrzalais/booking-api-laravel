<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GeoObject
 *
 * @method static Builder|GeoObject newModelQuery()
 * @method static Builder|GeoObject newQuery()
 * @method static Builder|GeoObject query()
 * @mixin Eloquent
 */
class GeoObject extends Model
{
    use HasFactory;

    protected $fillable = ['city_id', 'name', 'lat', 'long'];
}
