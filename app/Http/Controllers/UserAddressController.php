<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $orderByCol = 'is_main_address';
        $addresses = $user->addresses()->orderBy($orderByCol, 'desc')->latest()->get();
        return view('profile.addresses.index', compact('addresses'));
    }

    public function create()
    {
        $address = new UserAddress();
        return view('profile.addresses.form', compact('address'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);
        
        // If it's the first address or set as main, make sure others are not main
        if ($request->has('is_main_address') || Auth::user()->addresses()->count() === 0) {
            Auth::user()->addresses()->update(['is_main_address' => false]);
            $validated['is_main_address'] = true;
        } else {
            $validated['is_main_address'] = false;
        }

        Auth::user()->addresses()->create($validated);

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(UserAddress $address)
    {
        if ((int)$address->user_id !== (int)Auth::id()) abort(403);
        
        return view('profile.addresses.form', compact('address'));
    }

    public function update(Request $request, UserAddress $address)
    {
        if ((int)$address->user_id !== (int)Auth::id()) abort(403);
        
        $validated = $this->validateAddress($request);

        if ($request->has('is_main_address')) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->addresses()->where([['id', '!=', $address->id]])->update(['is_main_address' => false]);
            $validated['is_main_address'] = true;
        } else {
            // Can't unset main address if it's the only one
            if (Auth::user()->addresses()->count() === 1) {
                $validated['is_main_address'] = true;
            } else {
                $validated['is_main_address'] = false;
            }
        }

        $address->update($validated);

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(UserAddress $address)
    {
        if ((int)$address->user_id !== (int)Auth::id()) abort(403);
        
        $wasMain = $address->is_main_address;
        $address->delete();

        // If we deleted the main address, assign main to the most recently updated one
        if ($wasMain) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $orderByCol = 'updated_at';
            $newMain = $user->addresses()->orderBy($orderByCol, 'desc')->first();
            if ($newMain) {
                $newMain->update(['is_main_address' => true]);
            }
        }

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil dihapus.');
    }

    private function validateAddress(Request $request)
    {
        return $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_label' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'full_address' => ['required', 'string'],
            'address_landmark' => ['nullable', 'string'],
            'latitude' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
        ]);
    }
}
