<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApartmentType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApartmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApartmentType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
