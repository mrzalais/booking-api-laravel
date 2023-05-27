<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Facility
 *
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FacilityCategory|null $category
 * @method static Builder|Facility newModelQuery()
 * @method static Builder|Facility newQuery()
 * @method static Builder|Facility query()
 * @method static Builder|Facility whereCategoryId($value)
 * @method static Builder|Facility whereCreatedAt($value)
 * @method static Builder|Facility whereId($value)
 * @method static Builder|Facility whereName($value)
 * @method static Builder|Facility whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }
}
