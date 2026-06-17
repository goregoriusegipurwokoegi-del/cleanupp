<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $recipient_name
 * @property string $phone
 * @property string|null $address_label
 * @property string|null $province
 * @property string|null $city
 * @property string|null $kecamatan
 * @property string|null $village
 * @property string|null $postal_code
 * @property string|null $full_address
 * @property string|null $address_landmark
 * @property string|null $latitude
 * @property string|null $longitude
 * @property bool $is_main_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UserAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
