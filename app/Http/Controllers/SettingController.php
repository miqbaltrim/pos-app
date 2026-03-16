<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'store_name', 'store_address', 'store_phone', 'store_email',
            'tax_percent', 'receipt_footer', 'receipt_printer',
            'currency_symbol', 'low_stock_threshold'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                StoreSetting::setValue($field, $request->input($field));
            }
        }

        return redirect()->route('settings.index')->with('success', 'Settings berhasil disimpan');
    }
}