<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $sender;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url', 'http://localhost:6969');
        $this->sender = config('services.whatsapp.sender', 'default');
    }

    /**
     * Send a text message via WhatsApp.
     *
     * @param string $number International format without + (e.g., 628123456789)
     * @param string $message
     * @return bool
     */
    public function sendMessage($number, $message)
    {
        try {
            $response = Http::post("{$this->baseUrl}/send-message", [
                'sender' => $this->sender,
                'number' => $this->formatNumber($number),
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format number to international format without +.
     */
    protected function formatNumber($number)
    {
        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // Convert leading 0 to 62 (Indonesia) - you might want to make this dynamic
        if (strpos($number, '0') === 0) {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }
}
