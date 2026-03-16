<?php

namespace App\Http\Controllers;

use App\Models\PurchaseHead;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseHead::with('supplier', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->orderByDesc('created_at')->paginate(20);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchases.form', compact('suppliers', 'products'));
    }

    public function store(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $purchaseService->createPurchase(
                $request->only(['supplier_id', 'purchase_date', 'discount_amount', 'tax_amount', 'notes']),
                $request->items,
                (int) $request->user()->id   // ← kirim userId
            );
            return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(PurchaseHead $purchase)
    {
        $purchase->load('details.product', 'supplier', 'user');
        return view('purchases.show', compact('purchase'));
    }

    public function receive(PurchaseHead $purchase, PurchaseService $purchaseService, Request $request)
    {
        try {
            $purchaseService->receivePurchase(
                $purchase,
                (int) $request->user()->id   // ← kirim userId
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}