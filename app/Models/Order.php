<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int|null $employee_id
 * @property array|null $additional_services
 * @property string $processing_speed
 * @property string $order_number
 * @property string $queue_number
 * @property string $status
 * @property float $total_price
 * @property float $delivery_fee
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $status_pembayaran
 * @property string|null $snap_token
 * @property string|null $complaint
 * @property string|null $handling_notes
 * @property string|null $photo_before
 * @property string|null $photo_before_2
 * @property string|null $photo_after
 * @property string|null $reception_date
 * @property string|null $completion_date
 * @property int|null $rating
 * @property string|null $review
 * @property string|null $shoe_name
 * @property string|null $shoe_size
 * @property string|null $storage_location
 * @property bool $is_delivery
 * @property string|null $delivery_address
 * @property int $shoe_quantity
 * @property string|null $latitude
 * @property string|null $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\User|null $employee
 */
class Order extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'additional_services', 'processing_speed', 'order_number', 'queue_number', 'status', 'total_price', 'delivery_fee',
        'payment_method', 'payment_status', 'status_pembayaran', 'snap_token', 'complaint', 'handling_notes',
        'photo_before', 'photo_before_2', 'photo_after', 'reception_date', 'completion_date',
        'rating', 'review', 'shoe_name', 'shoe_size', 'employee_id', 'storage_location',
        'is_delivery', 'delivery_address', 'shoe_quantity', 'latitude', 'longitude'
    ];

    protected $casts = [
        'additional_services' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
