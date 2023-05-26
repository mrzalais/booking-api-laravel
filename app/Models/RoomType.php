<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoomType
 *
 * @method static Builder|RoomType newModelQuery()
 * @method static Builder|RoomType newQuery()
 * @method static Builder|RoomType query()
 * @mixin Eloquent
 */
class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
