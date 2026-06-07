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

    public function checkoutForm()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('services.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Get user's addresses
        $addresses = \App\Models\UserAddress::where('user_id', auth()->id())->get();

        return view('orders.checkout', compact('cart', 'addresses'));
    }
}
