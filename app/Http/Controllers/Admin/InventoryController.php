<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::orderBy('name')->get();
        return view('admin.inventories.index', compact('inventories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventories.index')->with('success', 'Barang berhasil ditambahkan ke inventaris.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventories.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('admin.inventories.index')->with('success', 'Barang berhasil dihapus.');
    }
}
