<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource for customers.
     */
    public function index(Request $request)
    {
        $query = Service::query();
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $services = $query->get();

        // Get the last added item in cart to pre-fill shoe name and size
        $cart = session()->get('cart', []);
        $lastCartItem = null;
        if (!empty($cart)) {
            $lastCartItem = end($cart);
        }

        return view('services.index', compact('services', 'lastCartItem'));
    }

    /**
     * Display a listing for admin management.
     */
    public function adminIndex(Request $request)
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        $services = $query->get();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'estimated_time' => 'nullable|string|max:255',
            'category' => 'required|in:cleaning,repair',
            'icon' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        Service::create($data);

        return redirect()->back()->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'estimated_time' => 'nullable|string|max:255',
            'category' => 'required|in:cleaning,repair',
            'icon' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        $service->update($data);

        return redirect()->back()->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Layanan berhasil dihapus!');
    }
}
