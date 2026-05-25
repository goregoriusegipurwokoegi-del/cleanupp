<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\AppNotification;

class TestNovu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'novu:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi uji coba ke Novu untuk memverifikasi API Key dan alur kerja.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('NOVU_API_KEY');

        if (!$apiKey) {
            $this->error('ERROR: NOVU_API_KEY belum dikonfigurasi di file .env Anda.');
            $this->info('Silakan tambahkan: NOVU_API_KEY=ApiKey_Anda ke dalam file .env');
            return self::FAILURE;
        }

        $userId = $this->argument('user_id');
        $user = $userId ? User::find($userId) : User::first();

        if (!$user) {
            $this->error('ERROR: Tidak ada user yang ditemukan di database untuk dikirimi notifikasi.');
            return self::FAILURE;
        }

        $this->info("Menghubungkan ke Novu menggunakan API Key: " . substr($apiKey, 0, 10) . "...");
        $this->info("Mengirim notifikasi uji coba ke User: {$user->name} ({$user->email})");

        try {
            $user->notify(new AppNotification([
                'title' => 'Uji Coba Koneksi Novu 🚀',
                'message' => 'Selamat! Integrasi Novu pada CleanUP Shoes Anda berhasil terhubung dan berjalan dengan lancar.',
                'url' => '/dashboard',
                'icon' => 'check-circle',
                'color' => 'green',
                'type' => 'cleanup-shoes-general'
            ]));

            $this->newLine();
            $this->info('========================================================================');
            $this->info('✅ SUKSES! Notifikasi uji coba telah dipicu melalui AppNotification.');
            $this->info('Silakan periksa Novu Dashboard Anda (https://dashboard.novu.co/)');
            $this->info('untuk melihat apakah event "cleanup-shoes-general" telah diterima.');
            $this->info('========================================================================');
            
            return self::SUCCESS;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $this->newLine();
            $this->error('🔴 Gagal mengirim ke Novu (API Error):');
            $this->error('Status Code: ' . $e->response->status());
            $this->error('Response Body: ' . $e->response->body());
            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('🔴 Gagal mengirim ke Novu (General Error): ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
