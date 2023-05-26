<?php

namespace App\Models;

use Database\Factories\RoomTypeFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\RoomType
 *
 * @method static Builder|RoomType newModelQuery()
 * @method static Builder|RoomType newQuery()
 * @method static Builder|RoomType query()
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static RoomTypeFactory factory($count = null, $state = [])
 * @method static Builder|RoomType whereCreatedAt($value)
 * @method static Builder|RoomType whereId($value)
 * @method static Builder|RoomType whereName($value)
 * @method static Builder|RoomType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
