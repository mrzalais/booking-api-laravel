<?php

namespace App\Models;

use App\Traits\ValidForRange;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ApartmentPrice
 *
 * @property int $id
 * @property int $apartment_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ApartmentPrice newModelQuery()
 * @method static Builder|ApartmentPrice newQuery()
 * @method static Builder|ApartmentPrice query()
 * @method static Builder|ApartmentPrice validForRange(array $range = [])
 * @method static Builder|ApartmentPrice whereApartmentId($value)
 * @method static Builder|ApartmentPrice whereCreatedAt($value)
 * @method static Builder|ApartmentPrice whereEndDate($value)
 * @method static Builder|ApartmentPrice whereId($value)
 * @method static Builder|ApartmentPrice wherePrice($value)
 * @method static Builder|ApartmentPrice whereStartDate($value)
 * @method static Builder|ApartmentPrice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ApartmentPrice extends Model
{
    use HasFactory;
    use ValidForRange;

    protected $fillable = ['apartment_id', 'start_date', 'end_date', 'price'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
