<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleHead;
use App\Services\SaleService;
use App\Services\ThermalPrintService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('pos.index', compact('customers'));
    }

    public function store(Request $request, SaleService $saleService)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,debit,credit,ewallet,transfer',
        ]);

        try {
            $sale = $saleService->createSale(
                $request->only(['customer_id', 'payment_method', 'paid_amount', 'payment_reference', 'discount_percent', 'tax_percent', 'notes']),
                $request->items,
                (int) $request->user()->id 
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data' => $sale,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function printReceipt(SaleHead $sale, ThermalPrintService $printService)
    {
        try {
            $printService->printReceipt($sale);
            return response()->json(['success' => true, 'message' => 'Struk berhasil dicetak']);
        } catch (\Exception $e) {
            // Fallback: kirim text receipt
            $text = $printService->generateReceiptText($sale);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'receipt_text' => $text
            ]);
        }
    }

    public function receiptPreview(SaleHead $sale, ThermalPrintService $printService)
    {
        $text = $printService->generateReceiptText($sale);
        return response()->json(['receipt' => $text]);
    }
}