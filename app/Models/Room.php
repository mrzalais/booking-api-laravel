<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Room
 *
 * @property-read Collection<int, Bed> $beds
 * @property-read int|null $beds_count
 * @property-read RoomType|null $room_type
 * @method static Builder|Room newModelQuery()
 * @method static Builder|Room newQuery()
 * @method static Builder|Room query()
 * @mixin Eloquent
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = ['apartment_id', 'room_type_id', 'name'];

    public function room_type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
}
