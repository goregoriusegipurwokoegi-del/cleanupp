<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string|null $reason
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Loan extends Model
{
    protected $fillable = ['user_id', 'amount', 'reason', 'status', 'admin_note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
