<?php

namespace App\Http\Controllers;

use App\Models\SaleHead;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleHead::with('customer', 'user');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'ilike', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->dateRange($request->from, $request->to);
        }

        $sales = $query->orderByDesc('created_at')->paginate(20);
        return view('sales.index', compact('sales'));
    }

    public function show(SaleHead $sale)
    {
        $sale->load('details.product', 'customer', 'user');
        return view('sales.show', compact('sale'));
    }

    public function cancel(SaleHead $sale, SaleService $saleService, Request $request)
    {
        try {
            $saleService->cancelSale(
                $sale,
                (int) $request->user()->id   // ← kirim userId
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}