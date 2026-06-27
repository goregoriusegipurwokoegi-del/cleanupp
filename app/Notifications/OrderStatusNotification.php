<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     * Note: database channel is handled separately by AppNotification to avoid duplication.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Dalam Antrian',
            'washing' => ($this->order->service->category == 'cleaning' ? 'Sedang Dicuci' : 'Sedang Dikerjakan'),
            'finishing' => ($this->order->service->category == 'cleaning' ? 'Proses Pengeringan' : 'Proses Finishing'),
            'ready' => 'Siap Diambil',
            'uncollected' => 'Belum Diambil',
            'completed' => 'Selesai & Diambil',
            'cancelled' => 'Pesanan Dibatalkan/Ditolak',
        ];

        $statusLabel = $statusLabels[$this->status] ?? $this->status;
        
        // Ensure relation is loaded safely
        $this->order->load('service');

        return (new MailMessage)
                    ->subject('Update Pesanan: ' . strtoupper($statusLabel))
                    ->view('emails.order_status', [
                        'order' => $this->order,
                        'status' => $this->status,
                        'statusLabel' => $statusLabel,
                        'notifiable' => $notifiable,
                        'url' => url('/customer/orders/' . $this->order->id)
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'Menunggu',
            'processing' => 'Dalam Antrian',
            'washing' => ($this->order->service->category == 'cleaning' ? 'Dicuci' : 'Dikerjakan'),
            'finishing' => ($this->order->service->category == 'cleaning' ? 'Pengeringan' : 'Finishing'),
            'ready' => 'SIAP DIAMBIL',
            'uncollected' => 'Belum Diambil',
            'completed' => 'SELESAI',
            'cancelled' => 'DIBATALKAN',
        ];

        $statusLabel = $statusLabels[$this->status] ?? $this->status;

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->status,
            'title' => 'Update Pesanan: ' . strtoupper($statusLabel),
            'message' => 'Pesanan #' . $this->order->order_number . ' (' . $this->order->shoe_name . ') sekarang berstatus: ' . strtolower($statusLabel),
            'shoe_name' => $this->order->shoe_name,
        ];
    }
}
