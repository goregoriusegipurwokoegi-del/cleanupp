<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $whatsAppService;
    protected $midtransService;

    public function __construct(WhatsAppService $whatsAppService, MidtransService $midtransService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->midtransService = $midtransService;
    }
    /**
     * Display a listing of the customer's active orders.
     */
    public function myOrders(Request $request)
    {
        $sort = $request->input('sort', 'desc');
        $search = $request->input('search');
        
        $query = Auth::user()->orders()->with('service')
            ->whereNotIn('status', ['completed', 'cancelled']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shoe_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', $sort)->get();
            
        return view('orders.my-orders', compact('orders', 'sort', 'search'));
    }

    /**
     * Display a listing of the customer's order history.
     */
    public function history(Request $request)
    {
        $sort = $request->input('sort', 'desc');
        $search = $request->input('search');
        $status_filter = $request->input('status_filter', 'completed');
        
        $query = Auth::user()->orders()->with('service');

        if ($status_filter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status_filter === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($status_filter === 'active') {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        } else {
            // 'all': completed and cancelled
            $query->whereIn('status', ['completed', 'cancelled']);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shoe_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', $sort)->get();
            
        return view('orders.history', compact('orders', 'sort', 'search', 'status_filter'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(Request $request)
    {
        $selectedService = null;
        if ($request->has('service_id')) {
            $selectedService = \App\Models\Service::find($request->service_id);
        }
        
        $services = \App\Models\Service::all();
        $bank_name = \App\Models\Setting::where('key', 'bank_name')->first()?->value;
        $bank_account = \App\Models\Setting::where('key', 'bank_account')->first()?->value;
        $bank_holder = \App\Models\Setting::where('key', 'bank_holder')->first()?->value;

        return view('orders.create', compact('services', 'selectedService', 'bank_name', 'bank_account', 'bank_holder'));
    }

    /**
     * Store a new order in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'additional_services' => 'nullable|array',
            'additional_services.*' => 'exists:services,id',
            'processing_speed' => 'required|in:regular,express',
            'payment_method' => 'required|in:cash,transfer',
            'shoe_name' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shoe_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'shoe_photo_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $mainService = \App\Models\Service::findOrFail($request->service_id);
        $totalPrice = $mainService->price;

        // Calculate Additional Services
        if ($request->has('additional_services')) {
            $extras = \App\Models\Service::whereIn('id', $request->additional_services)->get();
            $totalPrice += $extras->sum('price');
        }

        // Processing Speed Premium
        if ($request->processing_speed === 'express') {
            $totalPrice += 25000;
        }

        // Handle Photo Upload
        $photoPath = null;
        if ($request->hasFile('shoe_photo')) {
            $photoPath = $request->file('shoe_photo')->store('orders/photos', 'public');
        }

        $photoPath2 = null;
        if ($request->hasFile('shoe_photo_2')) {
            $photoPath2 = $request->file('shoe_photo_2')->store('orders/photos', 'public');
        }

        // Generate Order Number & Queue Number
        $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ((int) substr($lastOrder->queue_number, 1)) + 1 : 1;
        $queueNumber = 'Q' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        Order::create([
            'user_id' => Auth::id(),
            'service_id' => $mainService->id,
            'additional_services' => $request->additional_services,
            'processing_speed' => $request->processing_speed,
            'order_number' => $orderNumber,
            'queue_number' => $queueNumber,
            'status' => 'pending',
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
            'reception_date' => now(),
            'shoe_name' => $order_data['shoe_name'] ?? $request->shoe_name,
            'shoe_size' => $order_data['shoe_size'] ?? $request->shoe_size,
            'photo_before' => $photoPath,
            'photo_before_2' => $photoPath2,
        ]);

        $order = Order::latest()->first(); // Get the created order

        // Metode pembayaran hanya cash atau transfer manual, tidak perlu Midtrans

        // Send WhatsApp Notification to Customer
        if ($order->user->phone) {
            $paymentInstruction = "";
            if ($order->payment_method == 'cash') {
                $paymentInstruction = "Metode Pembayaran: TUNAI (Bayar di Tempat)\n";
            } else {
                $paymentInstruction = "Metode Pembayaran: TRANSFER BANK\n" .
                                     "Silakan transfer ke rekening outlet dan konfirmasi ke admin.\n";
            }

            $message = "Halo *" . $order->user->name . "*, pesanan CleanUP Shoes Anda telah diterima! 👟✨\n\n" .
                       "No. Pesanan: #" . $order->order_number . "\n" .
                       "No. Antrian: " . $order->queue_number . "\n" .
                       "Sepatu: " . $order->shoe_name . "\n" .
                       $paymentInstruction . 
                       "\nSilakan antar sepatu Anda ke outlet kami.\n" .
                       "Cek detail pesanan: " . route('orders.show', $order->id) . "\n\n" .
                       "Terima kasih!";
            $this->whatsAppService->sendMessage($order->user->phone, $message);
        }


        // Notify Admins and Employees
        $staff = User::whereIn('role', ['admin', 'employee'])->get();
        $notificationData = [
            'title' => 'Pesanan Baru!',
            'message' => 'Pesanan baru #' . $orderNumber . ' telah masuk untuk ' . $request->shoe_name,
            'icon' => 'shopping-bag',
            'color' => 'blue',
            'url' => Auth::user()->role == 'admin' ? route('admin.orders.index') : route('employee.orders.index'),
            'type' => 'new_order',
        ];
        
        foreach ($staff as $user) {
            /** @var User $user */
            $user->notify(new \App\Notifications\AppNotification($notificationData));
        }

        return redirect()->route('orders.my-orders')->with('success', 'Pesanan Anda berhasil dibuat! Silakan bawa sepatu Anda ke outlet kami.');
    }

    public function remindPayment(Order $order)
    {
        if (!$order->user->phone) {
            return back()->with('error', 'Nomor WhatsApp pelanggan tidak ditemukan.');
        }

        $message = "Halo *" . $order->user->name . "*, ini adalah pengingat pembayaran untuk pesanan CleanUP Shoes Anda.\n\n" .
                   "No. Pesanan: #" . $order->order_number . "\n" .
                   "Total Tagihan: Rp " . number_format($order->total_price, 0, ',', '.') . "\n" .
                   "Status: Belum Dibayar\n\n" .
                   "Segera selesaikan pembayaran agar pesanan Anda dapat kami proses.\n" .
                   "Klik di sini untuk membayar: " . route('orders.show', $order->id) . "\n\n" .
                   "Terima kasih!";
                   
        $this->whatsAppService->sendMessage($order->user->phone, $message);

        return back()->with('success', 'Pengingat pembayaran telah dikirim ke WhatsApp pelanggan.');
    }


    /**
     * Display the specified order detail.
     */
    public function show(Order $order)
    {
        // Security check: only the owner or an admin/employee can view the details
        if ($order->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'employee'])) {
            abort(403);
        }

        $bank_name = \App\Models\Setting::where('key', 'bank_name')->first()?->value;
        $bank_account = \App\Models\Setting::where('key', 'bank_account')->first()?->value;
        $bank_holder = \App\Models\Setting::where('key', 'bank_holder')->first()?->value;
        $midtrans_client_key = config('services.midtrans.client_key');

        return view('orders.show', compact('order', 'bank_name', 'bank_account', 'bank_holder', 'midtrans_client_key'));
    }

    /**
     * Display the digital receipt for the order.
     */
    public function receipt(Order $order)
    {
        // Security check: only the owner or an admin/employee can view the receipt
        if ($order->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'employee'])) {
            abort(403);
        }

        if ($order->payment_status !== 'paid') {
            return redirect()->route('orders.show', $order->id)->with('error', 'Struk hanya tersedia untuk pesanan yang sudah lunas.');
        }

        return view('orders.receipt', compact('order'));
    }

    /**
     * Administrative view: List all orders.
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                  ->orWhere('queue_number', 'like', "%$search%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%$search%");
                  });
            });
        }

        $orders = $query->get();
            
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Employee view: List all assigned/active orders.
     */
    public function employeeIndex(Request $request)
    {
        $status = $request->input('status');
        $category = $request->input('category');
        
        $query = Order::with(['user', 'service']);

        if ($status) {
            $query->where('status', $status);
        } else {
            // Default to pending to only show orders that need validation (belum divalidasi)
            $query->where('status', 'pending');
        }

        if ($category) {
            $query->whereHas('service', function($q) use ($category) {
                $q->where('category', $category);
            });
        }

        $orders = $query->latest()->get();
            
        return view('employee.orders.index', compact('orders', 'status'));
    }

    /**
     * Show the scan page for item collection.
     */
    public function scan(Request $request)
    {
        $search = $request->input('search');
        $order = null;

        if ($search) {
            $order = Order::with(['user', 'service'])
                ->where('order_number', $search)
                ->orWhere('queue_number', $search)
                ->first();
        }

        return view('employee.orders.scan', compact('order', 'search'));
    }

    /**
     * Process the scan and mark order as collected.
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($request->has('storage_location') && $request->storage_location) {
            $order->storage_location = $request->storage_location;
        }

        $order->status = 'completed';
        
        if (!$order->completion_date) {
            $order->completion_date = now();
        }
        
        $order->save();

        return redirect()->route('employee.orders.scan')->with('success', 'Pesanan ' . $order->order_number . ' berhasil ditandai sebagai sudah diambil.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!in_array(Auth::user()->role, ['admin', 'employee'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string'
        ]);

        if ($request->status == 'cancelled') {
            $customer = $order->user;
            
            // 1. Notify Customer via App (Point to dashboard since order will be deleted)
            $customer->notify(new \App\Notifications\AppNotification([
                'title' => 'PESANAN DITOLAK',
                'message' => 'Mohon maaf, pesanan Anda #' . $order->order_number . ' (' . $order->shoe_name . ') telah ditolak oleh admin.',
                'icon' => 'x-circle',
                'color' => 'red',
                'url' => route('customer.dashboard'),
                'type' => 'status_update',
            ]));

            // 2. Send WhatsApp Notification
            if ($customer->phone) {
                $waMessage = "Halo " . $customer->name . ",\n\n" .
                             "Kami informasikan bahwa pesanan Anda #" . $order->order_number . " (" . $order->shoe_name . ") *TELAH DITOLAK* oleh admin.\n\n" .
                             "Silakan hubungi admin kami untuk informasi lebih lanjut.\n" .
                             "Terima kasih.";
                $this->whatsAppService->sendMessage($customer->phone, $waMessage);
            }

            // 3. Forcefully delete from database
            $order->delete();
            
            return back()->with('success', 'Pesanan telah ditolak dan data telah dihapus.');
        }

        if ($request->status == 'processing' && $order->status == 'pending') {
            if ($order->payment_method == 'cash') {
                $order->payment_status = 'paid';
            }
        }

        $order->status = $request->status;
        $order->save();

        if ($request->has('storage_location') && $request->storage_location) {
            $order->update(['storage_location' => $request->storage_location]);
        }

        // Assign employee if not already assigned and user is staff
        if (!$order->employee_id && in_array(Auth::user()->role, ['admin', 'employee'])) {
            $order->update(['employee_id' => Auth::id()]);
        }

        // Automatically set dates
        if ($request->status == 'completed' && !$order->completion_date) {
            $order->update(['completion_date' => now()]);
        }

        // Send Notification to Customer and Admin
        try {
            $statusLabels = [
                'pending' => 'Menunggu',
                'in_progress' => 'Sedang Dicuci',
                'repairing' => 'Sedang Direparasi',
                'finishing' => 'Finishing',
                'completed' => 'Siap Diambil',
                'cancelled' => 'Dibatalkan',
            ];

            $label = $statusLabels[$request->status] ?? $request->status;

            // Notify Customer via App
            /** @var User $customer */
            $customer = $order->user;
            $customer->notify(new \App\Notifications\AppNotification([
                'title' => 'Update Pesanan: ' . strtoupper($label),
                'message' => 'Pesanan #' . $order->order_number . ' (' . $order->shoe_name . ') sekarang berstatus: ' . $label,
                'icon' => 'refresh-cw',
                'color' => 'indigo',
                'url' => route('orders.show', $order->id),
                'type' => 'status_update',
            ]));

            // Send WhatsApp Notification to Customer
            if ($customer->phone) {
                $waMessage = "Halo " . $customer->name . ", status pesanan Anda #" . $order->order_number . " telah diperbarui.\n\n" .
                             "Status Sekarang: *" . strtoupper($label) . "*\n\n" .
                             "Terima kasih telah menggunakan jasa CleanUP Shoes!";
                $this->whatsAppService->sendMessage($customer->phone, $waMessage);
            }

            // Notify Admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                /** @var User $admin */
                $admin->notify(new \App\Notifications\AppNotification([
                    'title' => 'Aktivitas: Update Status',
                    'message' => Auth::user()->name . ' mengubah status #' . $order->order_number . ' ke ' . $label,
                    'icon' => 'activity',
                    'color' => 'gray',
                    'url' => route('admin.orders.index'),
                    'type' => 'status_update',
                ]));
            }

        } catch (\Exception $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Submit a rating and review for an order.
     */
    public function submitReview(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $order->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // Notify Admins about new review
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var User $admin */
            $admin->notify(new \App\Notifications\AppNotification([
                'title' => 'Testimoni Baru ⭐' . $request->rating,
                'message' => 'Pelanggan memberikan ulasan: "' . substr($request->review, 0, 50) . '..."',
                'icon' => 'star',
                'color' => 'yellow',
                'url' => route('admin.testimonials.index'),
                'type' => 'new_testimonial',
            ]));
        }

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * Confirm cash payment (COD).
     */
    public function confirmPayment(Order $order)
    {
        if (!in_array(Auth::user()->role, ['admin', 'employee'])) {
            abort(403);
        }

        $order->update(['payment_status' => 'paid']);

        // Send WhatsApp Receipt to Customer
        if ($order->user->phone) {
            $message = "🧾 *STRUK PEMBAYARAN - CleanUP Shoes*\n\n" .
                       "No. Pesanan: #" . $order->order_number . "\n" .
                       "Tanggal: " . now()->format('d/m/Y H:i') . "\n" .
                       "Pelanggan: " . $order->user->name . "\n" .
                       "--------------------------\n" .
                       "Layanan: " . $order->service->name . "\n" .
                       "Sepatu: " . $order->shoe_name . "\n" .
                       "Total Bayar: Rp " . number_format($order->total_price, 0, ',', '.') . "\n" .
                       "Metode: " . strtoupper($order->payment_method) . "\n" .
                       "Status: *LUNAS*\n" .
                       "--------------------------\n" .
                       "Terima kasih atas pembayaran Anda!\n" .
                       "Pesanan Anda akan segera diproses.";
            
            $this->whatsAppService->sendMessage($order->user->phone, $message);
        }

        return back()->with('success', 'Pembayaran tunai berhasil dikonfirmasi!');
    }
}
