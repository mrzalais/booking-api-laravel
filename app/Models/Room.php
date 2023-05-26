<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Room
 *
 * @property int $id
 * @property int $apartment_id
 * @property int|null $room_type_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Bed> $beds
 * @property-read int|null $beds_count
 * @property-read RoomType|null $room_type
 * @method static RoomFactory factory($count = null, $state = [])
 * @method static Builder|Room newModelQuery()
 * @method static Builder|Room newQuery()
 * @method static Builder|Room query()
 * @method static Builder|Room whereApartmentId($value)
 * @method static Builder|Room whereCreatedAt($value)
 * @method static Builder|Room whereId($value)
 * @method static Builder|Room whereName($value)
 * @method static Builder|Room whereRoomTypeId($value)
 * @method static Builder|Room whereUpdatedAt($value)
 * @property-read Apartment $apartment
 * @mixin Eloquent
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = ['apartment_id', 'room_type_id', 'name'];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function room_type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
}
