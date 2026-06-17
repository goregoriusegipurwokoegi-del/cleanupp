<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $date
 * @property string|null $clock_in
 * @property string|null $clock_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Attendance extends Model
{
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
