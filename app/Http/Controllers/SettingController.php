<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function getLokasi()
    {
        $setting = Setting::first();

        if (!$setting) {
            return response()->json([
                'message' => 'Data lokasi belum tersedia',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Data lokasi ditemukan',
            'data' => $setting
        ]);
    }

    public function updateLokasi(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1'
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $setting->latitude = $request->latitude;
        $setting->longitude = $request->longitude;
        $setting->radius = $request->radius;
        $setting->save();

        return response()->json([
            'message' => 'Lokasi kantor dan radius diperbarui!',
            'data' => $setting
        ]);
    }

    public function updateReminderEmail(Request $request)
    {
        $request->validate([
            'reminder_subject' => 'required|string',
            'reminder_body' => 'required|string',
        ]);

        $setting = Setting::first();
        $setting->update([
            'reminder_subject' => $request->reminder_subject,
            'reminder_body' => $request->reminder_body
        ]);

        return response()->json(['message' => 'Pengaturan email diperbarui']);
    }

}
