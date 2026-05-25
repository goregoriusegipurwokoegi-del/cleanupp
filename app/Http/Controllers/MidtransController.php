<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class MidtransController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function callback(Request $request)
    {
        try {
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            $order = Order::where('order_number', $orderId)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $order->update(['payment_status' => 'pending']);
                    } else {
                        $this->markAsPaid($order);
                    }
                }
            } else if ($transaction == 'settlement') {
                $this->markAsPaid($order);
            } else if ($transaction == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $order->update(['payment_status' => 'failed']);
            }

            return response()->json(['message' => 'Notification processed']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    protected function markAsPaid($order)
    {
        $order->update(['payment_status' => 'paid']);

        // Send WhatsApp Receipt
        if ($order->user->phone) {
            $message = "🧾 *STRUK PEMBAYARAN OTOMATIS - CleanUP Shoes*\n\n" .
                       "No. Pesanan: #" . $order->order_number . "\n" .
                       "Tanggal: " . now()->format('d/m/Y H:i') . "\n" .
                       "Pelanggan: " . $order->user->name . "\n" .
                       "--------------------------\n" .
                       "Layanan: " . $order->service->name . "\n" .
                       "Sepatu: " . $order->shoe_name . "\n" .
                       "Total Bayar: Rp " . number_format($order->total_price, 0, ',', '.') . "\n" .
                       "Metode: MIDTRANS\n" .
                       "Status: *LUNAS*\n" .
                       "--------------------------\n" .
                       "Terima kasih atas pembayaran Anda!\n" .
                       "Pesanan Anda akan segera diproses.";
            
            $this->whatsAppService->sendMessage($order->user->phone, $message);
        }

        // Notify Admins and Employees
        $staff = User::whereIn('role', ['admin', 'employee'])->get();
        $notificationData = [
            'title' => 'Pembayaran Berhasil! ✅',
            'message' => 'Pembayaran untuk #' . $order->order_number . ' (' . $order->shoe_name . ') telah diterima via Midtrans.',
            'icon' => 'check-circle',
            'color' => 'green',
            'url' => route('admin.orders.index'),
            'type' => 'payment_success',
        ];

        foreach ($staff as $user) {
            /** @var User $user */
            $user->notify(new \App\Notifications\AppNotification($notificationData));
        }
    }
}
