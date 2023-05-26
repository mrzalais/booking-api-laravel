<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Bed
 *
 * @property-read BedType|null $bed_type
 * @property-read Room|null $room
 * @method static Builder|Bed newModelQuery()
 * @method static Builder|Bed newQuery()
 * @method static Builder|Bed query()
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
