<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property string|null $estimated_time
 * @property string $category
 * @property string|null $icon
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Service extends Model
{
    protected $fillable = ['name', 'description', 'price', 'estimated_time', 'category', 'icon', 'image'];
}
