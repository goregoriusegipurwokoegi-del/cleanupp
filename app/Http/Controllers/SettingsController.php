<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'profil-toko');
        
        // Ambil semua pengaturan
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $user = auth()->user();

        return view('admin.settings.index', compact('tab', 'settings', 'user'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'tab']);
        $tab = $request->input('tab', 'profil-toko');

        foreach ($data as $key => $value) {
            $valueToSave = is_array($value) ? json_encode($value) : $value;
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $valueToSave]
            );
        }

        return redirect()->route('admin.settings.index', ['tab' => $tab])->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function updateAdmin(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:12|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->password_plain = $request->password;
        }
        $user->save();

        return redirect()->route('admin.settings.index', ['tab' => 'akun-admin'])->with('success', 'Akun admin berhasil diperbarui!');
    }
}
