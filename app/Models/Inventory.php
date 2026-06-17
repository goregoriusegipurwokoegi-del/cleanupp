<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $stock
 * @property string $unit
 * @property int $min_stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Inventory extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'unit',
        'min_stock',
    ];
}
