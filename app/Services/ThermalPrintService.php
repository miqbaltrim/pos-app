<?php

namespace App\Services;

use App\Models\SaleHead;
use App\Models\StoreSetting;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class ThermalPrintService
{
    protected ?Printer $printer = null;

    /**
     * Connect ke printer berdasarkan setting
     */
    protected function connect(): void
    {
        $printerPath = StoreSetting::getValue('receipt_printer', '/dev/usb/lp0');

        try {
            // Deteksi tipe koneksi
            if (str_starts_with($printerPath, '/dev/') || str_starts_with($printerPath, '/tmp/')) {
                // Linux USB / File
                $connector = new FilePrintConnector($printerPath);
            } elseif (str_contains($printerPath, ':')) {
                // Network printer (ip:port)
                [$ip, $port] = explode(':', $printerPath);
                $connector = new NetworkPrintConnector($ip, (int) $port);
            } else {
                // Windows shared printer
                $connector = new WindowsPrintConnector($printerPath);
            }

            $this->printer = new Printer($connector);
        } catch (\Exception $e) {
            throw new \Exception('Gagal konek ke printer: ' . $e->getMessage());
        }
    }

    /**
     * Cetak struk penjualan
     */
    public function printReceipt(SaleHead $sale): bool
    {
        $sale->load('details', 'customer', 'user');

        $this->connect();

        try {
            $storeName = StoreSetting::getValue('store_name', 'TOKO');
            $storeAddress = StoreSetting::getValue('store_address', '');
            $storePhone = StoreSetting::getValue('store_phone', '');
            $footer = StoreSetting::getValue('receipt_footer', 'Terima Kasih!');

            // ===== HEADER =====
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 2);
            $this->printer->text($storeName . "\n");
            $this->printer->setTextSize(1, 1);
            $this->printer->setEmphasis(false);
            $this->printer->text($storeAddress . "\n");
            $this->printer->text("Telp: " . $storePhone . "\n");
            $this->printer->text(str_repeat('=', 32) . "\n");

            // ===== INFO TRANSAKSI =====
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("No    : " . $sale->invoice_number . "\n");
            $this->printer->text("Tgl   : " . $sale->transaction_date->format('d/m/Y H:i') . "\n");
            $this->printer->text("Kasir : " . $sale->user->name . "\n");
            if ($sale->customer) {
                $this->printer->text("Cust  : " . $sale->customer->name . "\n");
            }
            $this->printer->text(str_repeat('-', 32) . "\n");

            // ===== DETAIL ITEM =====
            foreach ($sale->details as $detail) {
                $this->printer->text($detail->product_name . "\n");
                $right = $this->formatMoney($detail->subtotal);
                $left = " " . $detail->quantity . " x " . $this->formatMoney($detail->unit_price);
                if ($detail->discount_amount > 0) {
                    $left .= " (disc " . $detail->discount_percent . "%)";
                }
                $this->printer->text($this->columnify($left, $right, 32) . "\n");
            }

            $this->printer->text(str_repeat('-', 32) . "\n");

            // ===== TOTALS =====
            $this->printer->text($this->columnify("Subtotal", $this->formatMoney($sale->subtotal), 32) . "\n");

            if ($sale->discount_amount > 0) {
                $this->printer->text($this->columnify(
                    "Diskon ({$sale->discount_percent}%)",
                    "-" . $this->formatMoney($sale->discount_amount),
                    32
                ) . "\n");
            }

            if ($sale->tax_amount > 0) {
                $this->printer->text($this->columnify(
                    "Pajak ({$sale->tax_percent}%)",
                    $this->formatMoney($sale->tax_amount),
                    32
                ) . "\n");
            }

            $this->printer->text(str_repeat('=', 32) . "\n");
            $this->printer->setEmphasis(true);
            $this->printer->text($this->columnify("TOTAL", $this->formatMoney($sale->grand_total), 32) . "\n");
            $this->printer->setEmphasis(false);

            $this->printer->text($this->columnify("Bayar (" . $sale->payment_method . ")", $this->formatMoney($sale->paid_amount), 32) . "\n");
            $this->printer->text($this->columnify("Kembali", $this->formatMoney($sale->change_amount), 32) . "\n");

            // ===== FOOTER =====
            $this->printer->text("\n");
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text($footer . "\n");
            $this->printer->text($sale->created_at->format('d/m/Y H:i:s') . "\n");

            $this->printer->feed(3);
            $this->printer->cut();
            $this->printer->close();

            return true;

        } catch (\Exception $e) {
            if ($this->printer) {
                $this->printer->close();
            }
            throw new \Exception('Gagal cetak: ' . $e->getMessage());
        }
    }

    /**
     * Format uang
     */
    protected function formatMoney($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Buat 2 kolom (kiri-kanan) dalam lebar tertentu
     */
    protected function columnify(string $left, string $right, int $width): string
    {
        $space = $width - strlen($left) - strlen($right);
        if ($space < 1) $space = 1;
        return $left . str_repeat(' ', $space) . $right;
    }

    /**
     * Generate receipt sebagai plain text (untuk preview / non-thermal)
     */
    public function generateReceiptText(SaleHead $sale): string
    {
        $sale->load('details', 'customer', 'user');

        $storeName = StoreSetting::getValue('store_name', 'TOKO');
        $storeAddress = StoreSetting::getValue('store_address', '');
        $storePhone = StoreSetting::getValue('store_phone', '');
        $footer = StoreSetting::getValue('receipt_footer', 'Terima Kasih!');

        $w = 32;
        $lines = [];

        $lines[] = str_pad($storeName, $w, ' ', STR_PAD_BOTH);
        $lines[] = str_pad($storeAddress, $w, ' ', STR_PAD_BOTH);
        $lines[] = str_pad("Telp: " . $storePhone, $w, ' ', STR_PAD_BOTH);
        $lines[] = str_repeat('=', $w);
        $lines[] = "No    : " . $sale->invoice_number;
        $lines[] = "Tgl   : " . $sale->transaction_date->format('d/m/Y H:i');
        $lines[] = "Kasir : " . $sale->user->name;
        if ($sale->customer) {
            $lines[] = "Cust  : " . $sale->customer->name;
        }
        $lines[] = str_repeat('-', $w);

        foreach ($sale->details as $d) {
            $lines[] = $d->product_name;
            $left = " {$d->quantity} x " . $this->formatMoney($d->unit_price);
            $right = $this->formatMoney($d->subtotal);
            $lines[] = $this->columnify($left, $right, $w);
        }

        $lines[] = str_repeat('-', $w);
        $lines[] = $this->columnify("Subtotal", $this->formatMoney($sale->subtotal), $w);
        if ($sale->discount_amount > 0) {
            $lines[] = $this->columnify("Diskon ({$sale->discount_percent}%)", "-" . $this->formatMoney($sale->discount_amount), $w);
        }
        if ($sale->tax_amount > 0) {
            $lines[] = $this->columnify("Pajak ({$sale->tax_percent}%)", $this->formatMoney($sale->tax_amount), $w);
        }
        $lines[] = str_repeat('=', $w);
        $lines[] = $this->columnify("TOTAL", $this->formatMoney($sale->grand_total), $w);
        $lines[] = $this->columnify("Bayar", $this->formatMoney($sale->paid_amount), $w);
        $lines[] = $this->columnify("Kembali", $this->formatMoney($sale->change_amount), $w);
        $lines[] = "";
        $lines[] = str_pad($footer, $w, ' ', STR_PAD_BOTH);
        $lines[] = str_pad($sale->created_at->format('d/m/Y H:i:s'), $w, ' ', STR_PAD_BOTH);

        return implode("\n", $lines);
    }
}