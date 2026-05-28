<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'additional_services', 'processing_speed', 'order_number', 'queue_number', 'status', 'total_price', 'delivery_fee',
        'payment_method', 'payment_status', 'snap_token', 'complaint', 'handling_notes',
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
