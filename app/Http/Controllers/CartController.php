<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        return view('orders.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'shoe_name' => 'required|string|max:255',
            'shoe_size' => 'nullable|string|max:50',
            'shoe_quantity' => 'required|integer|min:1',
            'processing_speed' => 'required|in:regular,express',
        ]);

        $service = Service::findOrFail($request->service_id);
        $cart = Session::get('cart', []);
        
        $cartId = uniqid(); // Unique ID for cart item

        $cart[$cartId] = [
            'id' => $cartId,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'service_image' => $service->image,
            'service_category' => $service->category,
            'shoe_name' => $request->shoe_name,
            'shoe_size' => $request->shoe_size,
            'shoe_quantity' => $request->shoe_quantity,
            'processing_speed' => $request->processing_speed,
            'price' => $service->price,
        ];

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Layanan berhasil ditambahkan ke keranjang.');
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Layanan dihapus dari keranjang.');
    }

    public function checkoutForm(Request $request)
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('services.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Filter cart based on selected items query string (Shopee-like checkbox checkout)
        $selectedItemsStr = $request->query('items');
        if ($selectedItemsStr) {
            $selectedArray = explode(',', $selectedItemsStr);
            $cartFiltered = [];
            foreach ($cart as $key => $item) {
                $itemId = $item['id'] ?? $key;
                if (in_array($itemId, $selectedArray)) {
                    $cartFiltered[$key] = $item;
                }
            }
            $cart = $cartFiltered;
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Silakan pilih minimal satu layanan untuk checkout.');
        }

        // Get user's addresses (load latitude & longitude for real-time fee calculation)
        $addresses = \App\Models\UserAddress::where(['user_id' => auth()->id()])->get();

        // Store location & delivery fee settings (for front-end distance calculation)
        $storeLat              = \App\Models\Setting::where('key', 'store_latitude')->first()?->value ?? '-0.0513462';
        $storeLng              = \App\Models\Setting::where('key', 'store_longitude')->first()?->value ?? '109.3210380';
        $deliveryThresholdKm   = (float) (\App\Models\Setting::where('key', 'delivery_threshold_km')->first()?->value ?? 5);
        $deliveryFeeAmount     = (float) (\App\Models\Setting::where('key', 'delivery_fee_above_threshold')->first()?->value ?? 25000);

        // Get bank accounts and QRIS image
        $bankAccounts = json_decode(\App\Models\Setting::where('key', 'bank_accounts')->first()?->value ?? '[]', true);
        if (empty($bankAccounts)) {
            $bankAccounts = [
                ['bank_name' => 'BCA', 'account_number' => '0292.771.400', 'account_holder' => 'Melitha Anggraeni'],
                ['bank_name' => 'Mandiri', 'account_number' => '146.001.124.9393', 'account_holder' => 'Melitha Anggraeni']
            ];
        }
        $qrisImage = \App\Models\Setting::where('key', 'qris_image')->first()?->value;

        return view('orders.checkout', compact('cart', 'addresses', 'storeLat', 'storeLng', 'deliveryThresholdKm', 'deliveryFeeAmount', 'bankAccounts', 'qrisImage'));
    }
}
