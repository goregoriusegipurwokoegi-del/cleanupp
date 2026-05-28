<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $bank_name = Setting::where('key', 'bank_name')->first()?->value;
        $bank_account = Setting::where('key', 'bank_account')->first()?->value;
        $bank_holder = Setting::where('key', 'bank_holder')->first()?->value;
        $qris_image = Setting::where('key', 'qris_image')->first()?->value;

        return view('admin.settings.index', compact('bank_name', 'bank_account', 'bank_holder', 'qris_image'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_holder' => 'required|string|max:255',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle QRIS image upload
        if ($request->hasFile('qris_image')) {
            $qrisPath = $request->file('qris_image')->store('settings/qris', 'public');
            Setting::updateOrCreate(['key' => 'qris_image'], ['value' => $qrisPath]);
        }

        // Save bank settings
        $bankData = collect($data)->only(['bank_name', 'bank_account', 'bank_holder']);
        foreach ($bankData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
