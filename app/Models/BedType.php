<?php

namespace App\Models;

use Database\Factories\BedTypeFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BedType
 *
 * @method static Builder|BedType newModelQuery()
 * @method static Builder|BedType newQuery()
 * @method static Builder|BedType query()
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static BedTypeFactory factory($count = null, $state = [])
 * @method static Builder|BedType whereCreatedAt($value)
 * @method static Builder|BedType whereId($value)
 * @method static Builder|BedType whereName($value)
 * @method static Builder|BedType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BedType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
