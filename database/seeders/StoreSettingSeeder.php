<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreSetting;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'store_name' => 'Toko Saya',
            'store_address' => 'Jl. Contoh No. 123, Kota',
            'store_phone' => '08123456789',
            'store_email' => 'toko@email.com',
            'tax_percent' => '11',
            'receipt_footer' => 'Terima kasih atas kunjungan Anda!',
            'receipt_printer' => '/dev/usb/lp0',
            'currency_symbol' => 'Rp',
            'low_stock_threshold' => '10',
        ];

        foreach ($settings as $key => $value) {
            StoreSetting::setValue($key, $value);
        }
    }
}