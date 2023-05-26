<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserProfile
 *
 * @method static Builder|UserProfile newModelQuery()
 * @method static Builder|UserProfile newQuery()
 * @method static Builder|UserProfile query()
 * @property int $id
 * @property int $user_id
 * @property string|null $invoice_address
 * @property string|null $invoice_postcode
 * @property string|null $invoice_city
 * @property int|null $invoice_country_id
 * @property string|null $gender
 * @property string|null $birth_date
 * @property int|null $nationality_country_id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserProfile whereBirthDate($value)
 * @method static Builder|UserProfile whereCreatedAt($value)
 * @method static Builder|UserProfile whereDescription($value)
 * @method static Builder|UserProfile whereGender($value)
 * @method static Builder|UserProfile whereId($value)
 * @method static Builder|UserProfile whereInvoiceAddress($value)
 * @method static Builder|UserProfile whereInvoiceCity($value)
 * @method static Builder|UserProfile whereInvoiceCountryId($value)
 * @method static Builder|UserProfile whereInvoicePostcode($value)
 * @method static Builder|UserProfile whereNationalityCountryId($value)
 * @method static Builder|UserProfile whereUpdatedAt($value)
 * @method static Builder|UserProfile whereUserId($value)
 * @mixin Eloquent
 */
class UserProfile extends Model
{
    use HasFactory;
}
