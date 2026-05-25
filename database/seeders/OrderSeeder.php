<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = \App\Models\User::where('role', 'customer')->first();
        $services = \App\Models\Service::all();

        if (!$customer || $services->isEmpty()) return;

        foreach ($services as $index => $service) {
            \App\Models\Order::create([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'queue_number' => 'Q' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'pending',
                'total_price' => $service->price,
                'payment_method' => 'cash',
                'payment_status' => 'unpaid',
            ]);
        }
    }
}
