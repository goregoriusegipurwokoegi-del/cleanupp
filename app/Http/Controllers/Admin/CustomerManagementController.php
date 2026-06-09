<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerManagementController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->get();
        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role' => 'customer',
        ]);

        return back()->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'required|string|max:15|unique:users,phone,' . $customer->id,
            'password' => 'nullable|string|min:8',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        
        if ($request->password) {
            $customer->password = Hash::make($request->password);
            // Simpan plain text jika diperlukan (tidak disarankan di production, 
            // tapi mengikuti pola yang sama dengan karyawan di sistem ini)
            $customer->password_plain = $request->password;
        }

        $customer->save();

        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(403);
        }
        
        $customer->delete();
        return back()->with('success', 'Data pelanggan telah dihapus.');
    }
}
