<?php

namespace App\Models;

use Database\Factories\BedFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Bed
 *
 * @property-read BedType|null $bed_type
 * @property-read Room|null $room
 * @method static Builder|Bed newModelQuery()
 * @method static Builder|Bed newQuery()
 * @method static Builder|Bed query()
 * @property int $id
 * @property int $room_id
 * @property int $bed_type_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static BedFactory factory($count = null, $state = [])
 * @method static Builder|Bed whereBedTypeId($value)
 * @method static Builder|Bed whereCreatedAt($value)
 * @method static Builder|Bed whereId($value)
 * @method static Builder|Bed whereName($value)
 * @method static Builder|Bed whereRoomId($value)
 * @method static Builder|Bed whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Bed extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'bed_type_id', 'name'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bed_type(): BelongsTo
    {
        return $this->belongsTo(BedType::class);
    }
}
