<?php

namespace App\Models;

use Database\Factories\ApartmentTypeFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ApartmentType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ApartmentType newModelQuery()
 * @method static Builder|ApartmentType newQuery()
 * @method static Builder|ApartmentType query()
 * @method static Builder|ApartmentType whereCreatedAt($value)
 * @method static Builder|ApartmentType whereId($value)
 * @method static Builder|ApartmentType whereName($value)
 * @method static Builder|ApartmentType whereUpdatedAt($value)
 * @method static ApartmentTypeFactory factory($count = null, $state = [])
 * @mixin Eloquent
 */
class ApartmentType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
