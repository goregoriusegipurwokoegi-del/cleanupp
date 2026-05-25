<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create([
            'name' => 'Deep Cleaning',
            'category' => 'cleaning',
            'price' => 50000,
            'description' => 'Pencucian menyeluruh luar dan dalam untuk sepatu yang sangat kotor.',
        ]);

        Service::create([
            'name' => 'Fast Cleaning',
            'category' => 'cleaning',
            'price' => 30000,
            'description' => 'Pencucian cepat bagian luar sepatu, cocok untuk perawatan rutin.',
        ]);

        Service::create([
            'name' => 'Reglue Standard',
            'category' => 'repair',
            'price' => 45000,
            'description' => 'Pengeleman ulang sol sepatu yang lepas menggunakan lem berkualitas.',
        ]);

        Service::create([
            'name' => 'Unhyellowing',
            'category' => 'cleaning',
            'price' => 65000,
            'description' => 'Menghilangkan noda kuning pada midsole agar kembali putih bersih.',
        ]);
    }
}
