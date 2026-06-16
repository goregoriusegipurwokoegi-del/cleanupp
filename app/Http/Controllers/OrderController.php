<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    /**
     * Display a listing of the customer's active orders.
     */
    public function myOrders(Request $request)
    {
        $sort = $request->input('sort', 'desc');
        $search = $request->input('search');
        
        $query = Auth::user()->orders()->with('service')
            ->whereNotIn('status', ['completed', 'cancelled', 'dikirim']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shoe_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', $sort)->get()->groupBy(function ($order) {
            return $order->group_id ?: 'single_' . $order->id;
        });
            
        return view('orders.my-orders', compact('orders', 'sort', 'search'));
    }

    /**
     * Display a listing of the customer's order history.
     */
    public function history(Request $request)
    {
        $sort = $request->input('sort', 'desc');
        $search = $request->input('search');
        $status_filter = $request->input('status_filter', 'all');
        
        $query = Auth::user()->orders()->with('service');

        if ($status_filter === 'unpaid') {
            $query->where('payment_status', 'unpaid');
        } elseif ($status_filter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status_filter === 'processing') {
            $query->whereIn('status', ['processing', 'finishing', 'ready', 'uncollected']);
        } elseif ($status_filter === 'dikirim') {
            $query->where('status', 'dikirim');
        } elseif ($status_filter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status_filter === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shoe_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', $sort)->get()->groupBy(function ($order) {
            return $order->group_id ?: 'single_' . $order->id;
        });
            
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

        $storeLat = \App\Models\Setting::where('key', 'store_latitude')->first()?->value ?? '-0.0513462';
        $storeLng = \App\Models\Setting::where('key', 'store_longitude')->first()?->value ?? '109.3210380';
        $deliveryThresholdKm = \App\Models\Setting::where('key', 'delivery_threshold_km')->first()?->value ?? 5;
        $deliveryFeeAboveThreshold = \App\Models\Setting::where('key', 'delivery_fee_above_threshold')->first()?->value ?? 25000;

        $mainAddress = Auth::user()->addresses()->where('is_main_address', true)->first();
        $isProfileComplete = $mainAddress && 
                             !empty($mainAddress->recipient_name) && 
                             !empty($mainAddress->phone) && 
                             !empty($mainAddress->full_address) && 
                             !empty($mainAddress->kecamatan) && 
                             !empty($mainAddress->postal_code) &&
                             !empty($mainAddress->latitude) &&
                             !empty($mainAddress->longitude);

        return view('orders.create', compact('services', 'selectedService', 'bank_name', 'bank_account', 'bank_holder', 'storeLat', 'storeLng', 'deliveryThresholdKm', 'deliveryFeeAboveThreshold', 'mainAddress', 'isProfileComplete'));
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
            'is_delivery' => 'required|boolean',
            'delivery_address' => 'required_if:is_delivery,1|nullable|string',
            'shoe_quantity' => 'required_if:is_delivery,1|integer|min:1',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'shoe_name' => 'required_if:is_delivery,0|nullable|string|max:255',
            'shoe_size' => 'required_if:is_delivery,0|nullable|string|max:10',
            'shoe_photo' => 'required_if:is_delivery,0|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shoe_photo_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika antar jemput, validasi kelengkapan profil (dari alamat utama)
        if ($request->is_delivery) {
            $mainAddress = Auth::user()->addresses()->where('is_main_address', true)->first();
            $isProfileComplete = $mainAddress &&
                                 !empty($mainAddress->recipient_name) &&
                                 !empty($mainAddress->phone) &&
                                 !empty($mainAddress->full_address) &&
                                 !empty($mainAddress->kecamatan) &&
                                 !empty($mainAddress->postal_code) &&
                                 !empty($mainAddress->latitude) &&
                                 !empty($mainAddress->longitude);

            if (!$isProfileComplete) {
                return redirect()->route('addresses.index')
                    ->with('warning', 'Profil pengiriman Anda belum lengkap. Silakan tambah dan lengkapi Alamat Utama Anda terlebih dahulu (Nama, No. WhatsApp, Alamat, Kecamatan, Kode Pos, dan Pin Lokasi).');
            }
        }

        $mainService = \App\Models\Service::findOrFail($request->service_id);
        $shoeQuantity = $request->input('shoe_quantity', 1);
        $totalPrice = $mainService->price * $shoeQuantity;

        // Calculate Additional Services
        if ($request->has('additional_services')) {
            $extras = \App\Models\Service::whereIn('id', $request->additional_services)->get();
            $totalPrice += ($extras->sum('price') * $shoeQuantity);
        }

        // Processing Speed Premium
        if ($request->processing_speed === 'express') {
            $totalPrice += (25000 * $shoeQuantity);
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

        // Calculate Delivery Fee based on distance (pakai koordinat dari alamat utama)
        $deliveryFee = 0;
        
        if ($request->is_delivery) {
            $mainAddress = Auth::user()->addresses()->where('is_main_address', true)->first();
            $userLat = $mainAddress ? $mainAddress->latitude : null;
            $userLng = $mainAddress ? $mainAddress->longitude : null;
            
            if ($userLat && $userLng) {
            $storeLat = \App\Models\Setting::where('key', 'store_latitude')->first()?->value ?? '-0.0513462';
            $storeLng = \App\Models\Setting::where('key', 'store_longitude')->first()?->value ?? '109.3210380';
            $deliveryThresholdKm = \App\Models\Setting::where('key', 'delivery_threshold_km')->first()?->value ?? 5;
            $deliveryFeeAboveThreshold = \App\Models\Setting::where('key', 'delivery_fee_above_threshold')->first()?->value ?? 25000;
            
            $earthRadius = 6371; // Radius of the earth in km
            $latFrom = deg2rad((float) str_replace(',', '.', $storeLat));
            $lonFrom = deg2rad((float) str_replace(',', '.', $storeLng));
            $latTo = deg2rad((float) str_replace(',', '.', $userLat));
            $lonTo = deg2rad((float) str_replace(',', '.', $userLng));
            
            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;
            
            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
            $distance = $angle * $earthRadius;
            
            if ($distance > $deliveryThresholdKm) {
                $deliveryFee = $deliveryFeeAboveThreshold;
                $totalPrice += $deliveryFee;
            }
        }
        }

        // Generate Order Number & Queue Number
        $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ((int) substr($lastOrder->queue_number, 1)) + 1 : 1;
        $queueNumber = 'Q' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Jika antar jemput, gunakan alamat & koordinat dari alamat utama
        $deliveryAddress = null;
        $orderLat = null;
        $orderLng = null;
        if ($request->is_delivery) {
            $mainAddress = Auth::user()->addresses()->where('is_main_address', true)->first();
            if ($mainAddress) {
                $deliveryAddress = $mainAddress->full_address . ', ' . $mainAddress->village . ', ' . $mainAddress->kecamatan . ', ' . $mainAddress->city . ', ' . $mainAddress->province . ' ' . $mainAddress->postal_code . ' (Penerima: ' . $mainAddress->recipient_name . ' - ' . $mainAddress->phone . ')';
                if ($mainAddress->address_landmark) $deliveryAddress .= ' [Patokan: ' . $mainAddress->address_landmark . ']';
                $orderLat = $mainAddress->latitude;
                $orderLng = $mainAddress->longitude;
            }
        }

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
            'shoe_name' => $request->shoe_name,
            'shoe_size' => $request->shoe_size,
            'photo_before' => $photoPath,
            'photo_before_2' => $photoPath2,
            'is_delivery' => $request->is_delivery,
            'delivery_address' => $deliveryAddress,
            'shoe_quantity' => $shoeQuantity,
            'latitude' => $orderLat,
            'longitude' => $orderLng,
            'delivery_fee' => $deliveryFee,
        ]);

        $order = Order::latest()->first(); // Get the created order


        // Send WhatsApp Notification to Customer
        if ($order->user->phone) {
            $paymentInstruction = "";
            if ($order->payment_method == 'cash') {
                $paymentInstruction = "Metode Pembayaran: TUNAI (Bayar di Tempat)\n";
            } else {
                $paymentInstruction = "Metode Pembayaran: TRANSFER BANK\n" .
                                     "Silakan transfer ke rekening outlet dan konfirmasi ke admin.\n";
            }

            $storeLat = env('STORE_LATITUDE', '-0.0513462');
            $storeLng = env('STORE_LONGITUDE', '109.3210380');
            $mapLink = "https://maps.google.com/?q={$storeLat},{$storeLng}";

            $deliveryInstruction = $order->is_delivery 
                                 ? "\nKami akan segera mengambil sepatu ke alamat Anda:\n" . $order->delivery_address . "\n"
                                 : "\nSilakan antar sepatu Anda ke outlet kami.\nLokasi Toko: " . $mapLink . "\n";

            $message = "Halo *" . $order->user->name . "*, pesanan CleanUP Shoes Anda telah diterima! 👟✨\n\n" .
                       "No. Pesanan: #" . $order->order_number . "\n" .
                       "No. Antrian: " . $order->queue_number . "\n" .
                       "Layanan: " . ($order->is_delivery ? 'Antar Jemput (' . $order->shoe_quantity . ' Sepatu)' : 'Drop-off (' . ($order->shoe_name ?: '1 Sepatu') . ')') . "\n" .
                       $paymentInstruction . 
                       $deliveryInstruction . 
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
            'type' => 'new_order',
        ];
        
        foreach ($staff as $user) {
            /** @var \App\Models\User $user */
            $notificationData['url'] = $user->role == 'admin' 
                ? route('admin.orders.index', [], false) 
                : route('employee.orders.index', [], false);
            $user->notify(new \App\Notifications\AppNotification($notificationData));
        }


        return redirect()->route('orders.my-orders')->with('success', 'Pesanan Anda berhasil dibuat! Silakan bawa sepatu Anda ke outlet kami.');
    }

    public function storeCheckout(Request $request)
    {
        $cart = \Illuminate\Support\Facades\Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('services.index')->with('error', 'Keranjang Anda kosong.');
        }

        $request->validate([
            'is_delivery' => 'required|boolean',
            'address_id' => 'required_if:is_delivery,1|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,transfer',
            'shoe_photo' => 'required|image|max:2048',
            'shoe_photo_2' => 'required|image|max:2048',
            'payment_proof' => 'nullable|image|max:4096',
            'checkout_items' => 'required|array|min:1',
        ]);

        $checkoutItems = $request->input('checkout_items', []);
        $cartToCheckout = [];
        foreach ($cart as $key => $item) {
            $itemId = $item['id'] ?? $key;
            if (in_array($itemId, $checkoutItems)) {
                $item['id'] = $itemId; // Ensure 'id' key is populated for fallback unsetting
                $cartToCheckout[$key] = $item;
            }
        }

        if (empty($cartToCheckout)) {
            return redirect()->route('cart.index')->with('error', 'Silakan pilih minimal satu layanan untuk checkout.');
        }

        $photoPath = $request->file('shoe_photo')->store('orders/photos', 'public');
        $photoPath2 = $request->file('shoe_photo_2')->store('orders/photos', 'public');

        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('orders/payments', 'public');
        }

        $groupId = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));

        $deliveryAddress = null;
        $orderLat = null;
        $orderLng = null;
        $deliveryFee = 0;

        if ($request->is_delivery && $request->address_id) {
            $address = \App\Models\UserAddress::where('id', $request->address_id)->where('user_id', Auth::id())->firstOrFail();
            $deliveryAddress = $address->full_address . ', ' . $address->village . ', ' . $address->kecamatan . ', ' . $address->city . ', ' . $address->province . ' ' . $address->postal_code . ' (Penerima: ' . $address->recipient_name . ' - ' . $address->phone . ')';
            $orderLat = $address->latitude;
            $orderLng = $address->longitude;

            // Calculate Delivery Fee once for the entire group
            if ($orderLat && $orderLng) {
                $storeLat = \App\Models\Setting::where('key', 'store_latitude')->first()?->value ?? '-0.0513462';
                $storeLng = \App\Models\Setting::where('key', 'store_longitude')->first()?->value ?? '109.3210380';
                $deliveryThresholdKm = \App\Models\Setting::where('key', 'delivery_threshold_km')->first()?->value ?? 5;
                $deliveryFeeAboveThreshold = \App\Models\Setting::where('key', 'delivery_fee_above_threshold')->first()?->value ?? 25000;
                
                $earthRadius = 6371;
                $latFrom = deg2rad((float) str_replace(',', '.', $storeLat));
                $lonFrom = deg2rad((float) str_replace(',', '.', $storeLng));
                $latTo = deg2rad((float) str_replace(',', '.', $orderLat));
                $lonTo = deg2rad((float) str_replace(',', '.', $orderLng));
                
                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;
                
                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                $distance = $angle * $earthRadius;
                
                if ($distance > $deliveryThresholdKm) {
                    $deliveryFee = $deliveryFeeAboveThreshold;
                }
            }
        }

        $orders = [];
        $firstOrder = true;

        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ((int) substr($lastOrder->queue_number, 1)) + 1 : 1;
        $queueNumber = 'Q' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        foreach ($cartToCheckout as $item) {
            $itemPrice = $item['price'] * $item['shoe_quantity'];
            if ($item['processing_speed'] == 'express') {
                $itemPrice += (25000 * $item['shoe_quantity']);
            }

            // Apply delivery fee ONLY to the first order in the group to avoid double charging
            $itemDeliveryFee = $firstOrder ? $deliveryFee : 0;
            $firstOrder = false;

            $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));

            $order = Order::create([
                'group_id' => $groupId,
                'user_id' => Auth::id(),
                'service_id' => $item['service_id'],
                'processing_speed' => $item['processing_speed'],
                'order_number' => $orderNumber,
                'queue_number' => $queueNumber,
                'status' => 'pending',
                'total_price' => $itemPrice + $itemDeliveryFee,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentProofPath ? 'paid' : 'unpaid',
                'reception_date' => now(),
                'shoe_name' => $item['shoe_name'],
                'shoe_size' => $item['shoe_size'],
                'photo_before' => $photoPath,
                'photo_before_2' => $photoPath2,
                'is_delivery' => $request->is_delivery,
                'delivery_address' => $deliveryAddress,
                'shoe_quantity' => $item['shoe_quantity'],
                'latitude' => $orderLat,
                'longitude' => $orderLng,
                'delivery_fee' => $itemDeliveryFee,
                'payment_proof' => $paymentProofPath,
                'status_pembayaran' => $paymentProofPath ? 'Menunggu Validasi' : 'pending',
            ]);

            $orders[] = $order;
        }

        // Clear checked out items from cart
        foreach ($cartToCheckout as $item) {
            unset($cart[$item['id']]);
        }
        \Illuminate\Support\Facades\Session::put('cart', $cart);

        // Send WhatsApp Notification for the first order as representative
        $repOrder = $orders[0];
        if ($repOrder->user->phone) {
            $paymentInstruction = "";
            if ($repOrder->payment_method == 'cash') {
                $paymentInstruction = "Metode Pembayaran: TUNAI (Bayar di Tempat)\n";
            } else {
                $paymentInstruction = "Metode Pembayaran: TRANSFER BANK\n" .
                                     "Silakan transfer ke rekening outlet dan konfirmasi ke admin.\n";
            }

            $storeLat = env('STORE_LATITUDE', '-0.0513462');
            $storeLng = env('STORE_LONGITUDE', '109.3210380');
            $mapLink = "https://maps.google.com/?q={$storeLat},{$storeLng}";

            $deliveryInstruction = $repOrder->is_delivery 
                                 ? "\nKami akan segera mengambil sepatu ke alamat Anda:\n" . $repOrder->delivery_address . "\n"
                                 : "\nSilakan antar sepatu Anda ke outlet kami.\nLokasi Toko: " . $mapLink . "\n";

            $message = "Halo *" . $repOrder->user->name . "*, pesanan CleanUP Shoes (Total " . count($orders) . " Layanan) Anda telah diterima! 👟✨\n\n" .
                       "Kode Invoice: #" . $groupId . "\n" .
                       $paymentInstruction . 
                       $deliveryInstruction . 
                       "Cek detail pesanan: " . route('orders.my-orders') . "\n\n" .
                       "Terima kasih!";
            $this->whatsAppService->sendMessage($repOrder->user->phone, $message);
        }

        // Notify Staff
        $staff = User::whereIn('role', ['admin', 'employee'])->get();
        $notificationData = [
            'title' => 'Pesanan Group Baru!',
            'message' => count($orders) . ' pesanan baru (Grup ' . $groupId . ') telah masuk.',
            'icon' => 'shopping-bag',
            'color' => 'blue',
            'type' => 'new_order',
        ];
        
        foreach ($staff as $user) {
            /** @var \App\Models\User $user */
            $notificationData['url'] = $user->role == 'admin' 
                ? route('admin.orders.index', [], false) 
                : route('employee.orders.index', [], false);
            $user->notify(new \App\Notifications\AppNotification($notificationData));
        }

        return redirect()->route('orders.my-orders')->with('success', 'Semua pesanan Anda berhasil dibuat!');
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
        
        $groupTotal = $order->group_id ? Order::where('group_id', $order->group_id)->sum('total_price') : $order->total_price;
        $groupOrders = $order->group_id ? Order::where('group_id', $order->group_id)->get() : collect([$order]);

        return view('orders.show', compact('order', 'bank_name', 'bank_account', 'bank_holder', 'groupTotal', 'groupOrders'));
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

        $groupOrders = $order->group_id ? Order::with('service')->where('group_id', $order->group_id)->get() : collect([$order]);
        $groupTotal = $order->group_id ? Order::where('group_id', $order->group_id)->sum('total_price') : $order->total_price;

        return view('orders.receipt', compact('order', 'groupOrders', 'groupTotal'));
    }

    /**
     * Administrative view: List all orders.
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'service'])->latest();

        if ($request->has('queue')) {
            $query->whereNotIn('status', ['completed', 'cancelled']);
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        } elseif ($request->has('delivery')) {
            $query->where('is_delivery', true)
                  ->whereNotIn('status', ['completed', 'cancelled']);
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        } else {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } elseif (!$request->has('status')) {
                // Default to active orders when accessed from sidebar (no query params)
                $query->whereNotIn('status', ['completed', 'cancelled']);
            }
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

        $orders = $query->get()->groupBy(function ($order) {
            return $order->group_id ?: 'single_' . $order->id;
        });
        $customers = User::where('role', 'customer')->get();
        $services = \App\Models\Service::all();
            
        return view('admin.orders.index', compact('orders', 'customers', 'services'));
    }

    /**
     * Store a new order created by admin.
     */
    public function adminStore(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required_without:service_ids|nullable|exists:services,id',
            'service_ids' => 'required_without:service_id|nullable|array',
            'service_ids.*' => 'exists:services,id',
            'processing_speed' => 'required|in:regular,express',
            'shoe_name' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shoe_quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer',
            'payment_status' => 'required|in:paid,unpaid',
            'status' => 'required|string',
            'shoe_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $shoeQuantity = $request->input('shoe_quantity', 1);
        $groupId = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));

        $photoPath = null;
        if ($request->hasFile('shoe_photo')) {
            $photoPath = $request->file('shoe_photo')->store('orders/photos', 'public');
        }

        $customer = User::findOrFail($request->user_id);
        $customerName = $customer->name;
        $customerPhone = $customer->phone;
        $customerAddress = $customer->address;
        
        $mainAddress = $customer->addresses()->where('is_main_address', true)->first();
        if ($mainAddress) {
            $customerName = $mainAddress->recipient_name;
            $customerPhone = $mainAddress->phone;
            $customerAddress = $mainAddress->full_address . ', ' . $mainAddress->village . ', ' . $mainAddress->kecamatan . ', ' . $mainAddress->city;
        }

        $serviceIds = $request->has('service_ids') ? $request->service_ids : [$request->service_id];

        $groupTotalPrice = 0;
        foreach ($serviceIds as $serviceId) {
            if (!$serviceId) continue;
            $mainService = \App\Models\Service::findOrFail($serviceId);
            $itemPrice = $mainService->price * $shoeQuantity;
            if ($request->processing_speed === 'express') {
                $itemPrice += (25000 * $shoeQuantity);
            }
            $groupTotalPrice += $itemPrice;
        }

        $cashAmount = $request->input('cash_amount');
        $changeAmount = null;
        if ($cashAmount !== null && $cashAmount !== '') {
            $cashAmount = (double) $cashAmount;
            $changeAmount = max(0, $cashAmount - $groupTotalPrice);
        }

        foreach ($serviceIds as $serviceId) {
            if (!$serviceId) continue;
            
            $mainService = \App\Models\Service::findOrFail($serviceId);
            $totalPrice = $mainService->price * $shoeQuantity;

            if ($request->processing_speed === 'express') {
                $totalPrice += (25000 * $shoeQuantity);
            }

            $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $nextNumber = $lastOrder ? ((int) substr($lastOrder->queue_number, 1)) + 1 : 1;
            $queueNumber = 'Q' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            Order::create([
                'group_id' => $groupId,
                'order_number' => $orderNumber,
                'user_id' => $request->user_id,
                'service_id' => $mainService->id,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'processing_speed' => $request->processing_speed,
                'queue_number' => $queueNumber,
                'status' => $request->status,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status_pembayaran' => $request->payment_status === 'paid' ? 'Sudah Divalidasi' : 'pending',
                'reception_date' => now(),
                'shoe_name' => $request->shoe_name,
                'shoe_size' => $request->shoe_size,
                'photo_before' => $photoPath,
                'shoe_quantity' => $shoeQuantity,
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan baru berhasil dibuat oleh admin!');
    }

    /**
     * Update an order edited by admin.
     */
    public function adminUpdate(Request $request, Order $order)
    {
        if (!in_array(Auth::user()->role, ['admin', 'employee'])) {
            abort(403);
        }

        $request->validate([
            'queue_number' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'processing_speed' => 'required|in:regular,express',
            'shoe_name' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shoe_quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer',
            'payment_status' => 'required|in:paid,unpaid',
            'status' => 'required|string',
            'shoe_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo_after' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $mainService = \App\Models\Service::findOrFail($request->service_id);
        $shoeQuantity = $request->input('shoe_quantity', 1);
        $totalPrice = $mainService->price * $shoeQuantity;

        if ($request->processing_speed === 'express') {
            $totalPrice += (25000 * $shoeQuantity);
        }

        $groupTotalPrice = 0;
        if ($order->group_id) {
            $groupOrders = Order::where('group_id', $order->group_id)->get();
            foreach ($groupOrders as $gOrder) {
                $gService = $gOrder->service;
                $gTotalPrice = $gService->price * $shoeQuantity;
                if ($request->processing_speed === 'express') {
                    $gTotalPrice += (25000 * $shoeQuantity);
                }
                if ($gOrder->delivery_fee > 0) {
                    $gTotalPrice += $gOrder->delivery_fee;
                }
                $groupTotalPrice += $gTotalPrice;
            }
        } else {
            $groupTotalPrice = $totalPrice;
        }

        $cashAmount = $request->input('cash_amount');
        $changeAmount = null;
        if ($cashAmount !== null && $cashAmount !== '') {
            $cashAmount = (double) $cashAmount;
            $changeAmount = max(0, $cashAmount - $groupTotalPrice);
        }

        $data = [
            'queue_number' => $request->queue_number,
            'service_id' => $request->service_id,
            'processing_speed' => $request->processing_speed,
            'shoe_name' => $request->shoe_name,
            'shoe_size' => $request->shoe_size,
            'shoe_quantity' => $shoeQuantity,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status_pembayaran' => $request->payment_status === 'paid' ? 'Sudah Divalidasi' : 'pending',
            'status' => $request->status,
            'total_price' => $totalPrice,
            'cash_amount' => $cashAmount,
            'change_amount' => $changeAmount,
        ];

        if ($request->hasFile('shoe_photo')) {
            $data['photo_before'] = $request->file('shoe_photo')->store('orders/photos', 'public');
        }

        if ($request->hasFile('photo_after')) {
            $data['photo_after'] = $request->file('photo_after')->store('orders/photos', 'public');
        }

        if ($order->group_id) {
            $groupOrders = Order::where('group_id', $order->group_id)->get();
            /** @var \App\Models\Order $gOrder */
            foreach ($groupOrders as $gOrder) {
                $gService = $gOrder->service;
                $gTotalPrice = $gService->price * $shoeQuantity;
                if ($request->processing_speed === 'express') {
                    $gTotalPrice += (25000 * $shoeQuantity);
                }
                if ($gOrder->delivery_fee > 0) {
                    $gTotalPrice += $gOrder->delivery_fee;
                }
                
                $gData = [
                    'queue_number' => $request->queue_number,
                    'processing_speed' => $request->processing_speed,
                    'shoe_name' => $request->shoe_name,
                    'shoe_size' => $request->shoe_size,
                    'shoe_quantity' => $shoeQuantity,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_status,
                    'status_pembayaran' => $request->payment_status === 'paid' ? 'Sudah Divalidasi' : 'pending',
                    'status' => $request->status,
                    'total_price' => $gTotalPrice,
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                ];
                
                if (isset($data['photo_before'])) {
                    $gData['photo_before'] = $data['photo_before'];
                }
                if (isset($data['photo_after'])) {
                    $gData['photo_after'] = $data['photo_after'];
                }
                
                $gOrder->update($gData);
            }
        } else {
            $order->update($data);
        }

        return back()->with('success', 'Pesanan berhasil diperbarui!');
    }

    /**
     * Delete an order.
     */
    public function adminDestroy(Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $order->delete();

        return back()->with('success', 'Pesanan berhasil dihapus.');
    }


    /**
     * Employee view: List all assigned/active orders.
     */
    public function employeeIndex(Request $request)
    {
        $status = $request->input('status');
        $category = $request->input('category');
        $delivery = $request->input('delivery');
        
        $query = Order::with(['user', 'service']);

        if ($request->has('queue')) {
            $query->whereNotIn('status', ['pending', 'completed', 'cancelled']);
            if ($status) {
                $query->where('status', $status);
            }
        } elseif ($delivery) {
            $query->where('is_delivery', true)
                  ->whereNotIn('status', ['completed', 'cancelled']);
            if ($status) {
                $query->where('status', $status);
            }
        } else {
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to pending orders (not yet validated)
                $query->where('status', 'pending');
            }
        }

        if ($category) {
            $query->whereHas('service', function($q) use ($category) {
                $q->where('category', $category);
            });
        }

        $orders = $query->latest()->get()->groupBy(function ($order) {
            return $order->group_id ?: 'single_' . $order->id;
        });
        $customers = User::where('role', 'customer')->get();
        $services = \App\Models\Service::all();
            
        return view('employee.orders.index', compact('orders', 'status', 'customers', 'services'));
    }

    /**
     * Store a new order created by employee.
     */
    public function employeeStore(Request $request)
    {
        if (Auth::user()->role !== 'employee') {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required_without:service_ids|nullable|exists:services,id',
            'service_ids' => 'required_without:service_id|nullable|array',
            'service_ids.*' => 'exists:services,id',
            'processing_speed' => 'required|in:regular,express',
            'shoe_name' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shoe_quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer',
            'payment_status' => 'required|in:paid,unpaid',
            'status' => 'required|string',
            'shoe_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $shoeQuantity = $request->input('shoe_quantity', 1);
        $groupId = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));

        $photoPath = null;
        if ($request->hasFile('shoe_photo')) {
            $photoPath = $request->file('shoe_photo')->store('orders/photos', 'public');
        }

        $customer = User::findOrFail($request->user_id);
        $customerName = $customer->name;
        $customerPhone = $customer->phone;
        $customerAddress = $customer->address;
        
        $mainAddress = $customer->addresses()->where('is_main_address', true)->first();
        if ($mainAddress) {
            $customerName = $mainAddress->recipient_name;
            $customerPhone = $mainAddress->phone;
            $customerAddress = $mainAddress->full_address . ', ' . $mainAddress->village . ', ' . $mainAddress->kecamatan . ', ' . $mainAddress->city;
        }

        $serviceIds = $request->has('service_ids') ? $request->service_ids : [$request->service_id];

        $groupTotalPrice = 0;
        foreach ($serviceIds as $serviceId) {
            if (!$serviceId) continue;
            $mainService = \App\Models\Service::findOrFail($serviceId);
            $itemPrice = $mainService->price * $shoeQuantity;
            if ($request->processing_speed === 'express') {
                $itemPrice += (25000 * $shoeQuantity);
            }
            $groupTotalPrice += $itemPrice;
        }

        $cashAmount = $request->input('cash_amount');
        $changeAmount = null;
        if ($cashAmount !== null && $cashAmount !== '') {
            $cashAmount = (double) $cashAmount;
            $changeAmount = max(0, $cashAmount - $groupTotalPrice);
        }

        foreach ($serviceIds as $serviceId) {
            if (!$serviceId) continue;
            
            $mainService = \App\Models\Service::findOrFail($serviceId);
            $totalPrice = $mainService->price * $shoeQuantity;

            if ($request->processing_speed === 'express') {
                $totalPrice += (25000 * $shoeQuantity);
            }

            $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $nextNumber = $lastOrder ? ((int) substr($lastOrder->queue_number, 1)) + 1 : 1;
            $queueNumber = 'Q' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            Order::create([
                'group_id' => $groupId,
                'order_number' => $orderNumber,
                'user_id' => $request->user_id,
                'service_id' => $mainService->id,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'processing_speed' => $request->processing_speed,
                'queue_number' => $queueNumber,
                'status' => $request->status,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status_pembayaran' => $request->payment_status === 'paid' ? 'Sudah Divalidasi' : 'pending',
                'reception_date' => now(),
                'shoe_name' => $request->shoe_name,
                'shoe_size' => $request->shoe_size,
                'photo_before' => $photoPath,
                'shoe_quantity' => $shoeQuantity,
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
            ]);
        }

        return redirect()->route('employee.orders.index')->with('success', 'Pesanan baru berhasil dibuat oleh karyawan!');
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
            
            // 1. Notify Customer via App (Point to order detail page since order is not deleted anymore)
            $customer->notify(new \App\Notifications\AppNotification([
                'title' => 'PESANAN DITOLAK',
                'message' => 'Mohon maaf, pesanan Anda #' . $order->order_number . ' (' . $order->shoe_name . ') telah ditolak oleh admin.',
                'icon' => 'x-circle',
                'color' => 'red',
                'url' => route('orders.show', $order->id, false),
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

            // 3. Update status to cancelled in database (do not delete)
            if ($order->group_id) {
                Order::where('group_id', $order->group_id)->update(['status' => 'cancelled']);
            } else {
                $order->update(['status' => 'cancelled']);
            }
            
            return back()->with('success', 'Pesanan telah ditolak.');
        }

        if ($order->group_id) {
            $updateData = [
                'status' => $request->status,
            ];
            if ($request->status == 'processing' && $order->status == 'pending') {
                $updateData['payment_status'] = 'paid';
                $updateData['status_pembayaran'] = 'Sudah Divalidasi';
            }
            if ($request->has('storage_location') && $request->storage_location) {
                $updateData['storage_location'] = $request->storage_location;
            }
            if (!$order->employee_id && in_array(Auth::user()->role, ['admin', 'employee'])) {
                $updateData['employee_id'] = Auth::id();
            }
            if ($request->status == 'completed' && !$order->completion_date) {
                $updateData['completion_date'] = now();
            }
            Order::where('group_id', $order->group_id)->update($updateData);
            $order->refresh();
        } else {
            if ($request->status == 'processing' && $order->status == 'pending') {
                $order->payment_status = 'paid';
                $order->status_pembayaran = 'Sudah Divalidasi';
            }
            $order->status = $request->status;
            $order->save();

            if ($request->has('storage_location') && $request->storage_location) {
                $order->update(['storage_location' => $request->storage_location]);
            }
            if (!$order->employee_id && in_array(Auth::user()->role, ['admin', 'employee'])) {
                $order->update(['employee_id' => Auth::id()]);
            }
            if ($request->status == 'completed' && !$order->completion_date) {
                $order->update(['completion_date' => now()]);
            }
        }

        // Send Notification to Customer and Admin
        try {
            $statusLabels = [
                'pending' => 'Menunggu',
                'processing' => 'Sedang Dikerjakan',
                'finishing' => 'Finishing',
                'ready' => 'Siap Diambil',
                'dikirim' => 'Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            $label = $statusLabels[$request->status] ?? $request->status;

            // Notify Customer via App + Email (menggunakan OrderStatusNotification untuk email yang lebih informatif)
            /** @var User $customer */
            $customer = $order->user;
            
            // In-app notification
            $customer->notify(new \App\Notifications\AppNotification([
                'title' => 'Update Pesanan: ' . strtoupper($label),
                'message' => 'Pesanan #' . $order->order_number . ' (' . $order->shoe_name . ') sekarang berstatus: ' . $label,
                'icon' => 'refresh-cw',
                'color' => 'indigo',
                'url' => route('orders.show', $order->id, false),
                'type' => 'status_update',
            ]));

            // Email notification with beautiful order status template
            if (!empty($customer->email) && !preg_match('/@(cleanup\.com|example\.com)$/i', $customer->email)) {
                $customer->notify(new \App\Notifications\OrderStatusNotification($order, $request->status));
            }

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
                    'url' => route('admin.orders.index', [], false),
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

        if ($order->group_id) {
            Order::where('group_id', $order->group_id)->update([
                'payment_status' => 'paid',
                'status_pembayaran' => 'Sudah Divalidasi',
                'status' => 'processing'
            ]);
        } else {
            $order->update([
                'payment_status' => 'paid',
                'status_pembayaran' => 'Sudah Divalidasi',
                'status' => 'processing' // Langsung dikonfirmasi tanpa validasi
            ]);
        }

        // Notifikasi ke Admin & Karyawan jika manual QRIS/Cash
        $staff = User::whereIn('role', ['admin', 'employee'])->get();
        foreach ($staff as $user) {
            /** @var User $user */
            $user->notify(new \App\Notifications\AppNotification([
                'title' => '💳 Pembayaran ' . strtoupper($order->payment_method) . ' Berhasil',
                'message' => 'Pembayaran pesanan #' . $order->order_number . ' sebesar Rp ' . number_format($order->total_price, 0, ',', '.') . ' telah dikonfirmasi. Nominal otomatis masuk pendapatan dan status pesanan langsung diproses.',
                'icon' => 'check-circle',
                'color' => 'green',
                'url' => $user->role == 'admin' ? route('admin.orders.index', [], false) : route('employee.orders.index', [], false),
                'type' => 'payment_success',
            ]));
        }

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
                       "Catatan: Pembayaran ini berlaku untuk seluruh pesanan dalam grup invoice yang sama (jika ada).\n" .
                       "--------------------------\n" .
                       "Terima kasih atas pembayaran Anda!\n" .
                       "Pesanan Anda akan segera diproses.";
            
            $this->whatsAppService->sendMessage($order->user->phone, $message);
        }

        return back()->with('success', 'Pembayaran tunai berhasil dikonfirmasi!');
    }

    /**
     * Cancel the specified order by customer.
     */
    public function cancel(Order $order)
    {
        // Security check: only the owner can cancel
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow cancellation of 'pending' orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan yang sedang diproses tidak dapat dibatalkan.');
        }

        // Update status to cancelled for the entire group if grouped
        if ($order->group_id) {
            Order::where('group_id', $order->group_id)->update([
                'status' => 'cancelled'
            ]);
        } else {
            $order->update([
                'status' => 'cancelled'
            ]);
        }

        // Notify Admins and Customer
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                /** @var User $admin */
                $admin->notify(new \App\Notifications\AppNotification([
                    'title' => 'Pesanan Dibatalkan',
                    'message' => 'Pesanan #' . $order->order_number . ' telah dibatalkan oleh pelanggan (' . Auth::user()->name . ')',
                    'icon' => 'x-circle',
                    'color' => 'red',
                    'url' => route('admin.orders.index', [], false),
                    'type' => 'status_update',
                ]));
            }

            if ($order->user->phone) {
                $waMessage = "Halo " . $order->user->name . ",\n\n" .
                             "Pesanan Anda #" . $order->order_number . " (" . $order->shoe_name . ") *TELAH BERHASIL DIBATALKAN*.\n\n" .
                             "Terima kasih.";
                $this->whatsAppService->sendMessage($order->user->phone, $waMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Cancellation notification failed: ' . $e->getMessage());
        }

        return redirect()->route('orders.my-orders')->with('success', 'Pesanan Anda berhasil dibatalkan.');
    }
    public function uploadPaymentProof(Request $request, Order $order)
    {
        // Security check: only the owner can upload
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('orders/payments', 'public');
            
            if ($order->group_id) {
                Order::where('group_id', $order->group_id)->update([
                    'payment_proof' => $proofPath,
                    'status_pembayaran' => 'Menunggu Validasi',
                    'payment_status' => 'paid'
                ]);
            } else {
                $order->update([
                    'payment_proof' => $proofPath,
                    'status_pembayaran' => 'Menunggu Validasi',
                    'payment_status' => 'paid'
                ]);
            }

            // Notify Admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                /** @var \App\Models\User $admin */
                $admin->notify(new \App\Notifications\AppNotification([
                    'title' => 'Bukti Pembayaran Baru',
                    'message' => 'Pelanggan ' . Auth::user()->name . ' telah mengunggah bukti pembayaran untuk pesanan #' . $order->order_number,
                    'icon' => 'file-text',
                    'color' => 'blue',
                    'url' => route('admin.orders.index', [], false),
                    'type' => 'payment_proof',
                ]));
            }

            return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu validasi admin.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
