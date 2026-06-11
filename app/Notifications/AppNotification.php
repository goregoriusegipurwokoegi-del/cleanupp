<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    protected $details;

    /**
     * Create a new notification instance.
     * 
     * $details = [
     *    'title' => 'Title',
     *    'message' => 'Message body',
     *    'icon' => 'shopping-cart', // feather icon name or custom
     *    'color' => 'blue', // blue, green, red, yellow
     *    'url' => '/path/to/resource',
     *    'type' => 'order_update', // order_update, task_assigned, system_alert
     * ]
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (env('NOVU_API_KEY')) {
            try {
                $this->triggerNovuNotification($notifiable);
            } catch (\Exception $e) {
                \Log::error('Novu Notification Trigger Failed: ' . $e->getMessage());
            }
        }

        $channels = ['database'];

        // Send email for non-status_update notifications only.
        // Order status updates use OrderStatusNotification for a richer email template.
        $type = $this->details['type'] ?? 'general';
        if ($type !== 'status_update' && !empty($notifiable->email) && !preg_match('/@(cleanup\.com|example\.com)$/i', $notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Trigger Novu Notification Event
     */
    protected function triggerNovuNotification(object $notifiable)
    {
        $apiKey = env('NOVU_API_KEY');
        // Maps to the specific workflow type (e.g. order_update, task_assigned, system_alert)
        // or defaults to 'cleanup-shoes-general' in the Novu dashboard.
        $workflowId = $this->details['type'] ?? 'cleanup-shoes-general';

        \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'ApiKey ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.novu.co/v1/events/trigger', [
            'name' => $workflowId,
            'to' => [
                'subscriberId' => (string) $notifiable->id,
                'email' => $notifiable->email,
                'phone' => $notifiable->phone ?? '',
                'firstName' => $notifiable->name,
            ],
            'payload' => [
                'title' => $this->details['title'] ?? 'Pemberitahuan Baru',
                'message' => $this->details['message'],
                'url' => url($this->details['url'] ?? '/dashboard'),
                'icon' => $this->details['icon'] ?? 'bell',
                'color' => $this->details['color'] ?? 'blue',
            ],
        ])->throw();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->details['title'] ?? 'Notifikasi CleanUP Shoes')
                    ->view('emails.notification', [
                        'title' => $this->details['title'] ?? 'Notifikasi CleanUP Shoes',
                        'body' => $this->details['message'],
                        'url' => url($this->details['url'] ?? '/dashboard'),
                        'notifiable' => $notifiable
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = $this->details['url'] ?? '/dashboard';
        
        // Convert absolute URLs to relative paths to prevent session loss across local domains (e.g. localhost vs 127.0.0.1)
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '/dashboard';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
            $url = $path . $query . $fragment;
        }

        return [
            'title' => $this->details['title'] ?? 'Pemberitahuan',
            'message' => $this->details['message'],
            'icon' => $this->details['icon'] ?? 'bell',
            'color' => $this->details['color'] ?? 'blue',
            'url' => $url,
            'type' => $this->details['type'] ?? 'general',
        ];
    }
}
