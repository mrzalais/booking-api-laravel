<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\FacilityCategory
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FacilityCategory newModelQuery()
 * @method static Builder|FacilityCategory newQuery()
 * @method static Builder|FacilityCategory query()
 * @method static Builder|FacilityCategory whereCreatedAt($value)
 * @method static Builder|FacilityCategory whereId($value)
 * @method static Builder|FacilityCategory whereName($value)
 * @method static Builder|FacilityCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FacilityCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'category_id');
    }
}
