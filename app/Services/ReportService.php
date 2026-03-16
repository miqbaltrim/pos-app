<?php

namespace App\Services;

use App\Models\SaleHead;
use App\Models\SaleDetail;
use App\Models\PurchaseHead;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Ringkasan Dashboard
     */
    public function dashboardSummary(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today_sales' => SaleHead::completed()->today()->sum('grand_total'),
            'today_transactions' => SaleHead::completed()->today()->count(),
            'month_sales' => SaleHead::completed()->where('transaction_date', '>=', $thisMonth)->sum('grand_total'),
            'month_transactions' => SaleHead::completed()->where('transaction_date', '>=', $thisMonth)->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'min_stock')->count(),
            'top_products_today' => $this->topProducts($today, $today, 5),
        ];
    }

    /**
     * Laporan Penjualan per Periode
     */
    public function salesReport(string $from, string $to): array
    {
        $sales = SaleHead::completed()
            ->dateRange($from, $to)
            ->with('details', 'user', 'customer')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $dailySummary = SaleHead::completed()
            ->dateRange($from, $to)
            ->select(
                DB::raw("DATE(transaction_date) as date"),
                DB::raw("COUNT(*) as total_trx"),
                DB::raw("SUM(grand_total) as total_sales"),
                DB::raw("SUM(discount_amount) as total_discount"),
                DB::raw("SUM(tax_amount) as total_tax"),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'period' => ['from' => $from, 'to' => $to],
            'summary' => [
                'total_transactions' => $sales->count(),
                'total_sales' => $sales->sum('grand_total'),
                'total_discount' => $sales->sum('discount_amount'),
                'total_tax' => $sales->sum('tax_amount'),
                'average_per_transaction' => $sales->count() > 0
                    ? round($sales->sum('grand_total') / $sales->count(), 2) : 0,
            ],
            'daily_summary' => $dailySummary,
            'transactions' => $sales,
        ];
    }

    /**
     * Laporan Profit / Laba
     */
    public function profitReport(string $from, string $to): array
    {
        $details = SaleDetail::whereHas('saleHead', function ($q) use ($from, $to) {
            $q->completed()->dateRange($from, $to);
        })->get();

        $totalRevenue = $details->sum('subtotal');
        $totalCost = $details->sum(function ($d) {
            return $d->cost_price * $d->quantity;
        });
        $grossProfit = $totalRevenue - $totalCost;

        $profitByProduct = $details->groupBy('product_id')->map(function ($items) {
            $revenue = $items->sum('subtotal');
            $cost = $items->sum(fn($d) => $d->cost_price * $d->quantity);
            return [
                'product_name' => $items->first()->product_name,
                'qty_sold' => $items->sum('quantity'),
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $revenue - $cost,
                'margin' => $revenue > 0 ? round(($revenue - $cost) / $revenue * 100, 2) : 0,
            ];
        })->sortByDesc('profit')->values();

        return [
            'period' => ['from' => $from, 'to' => $to],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'margin_percent' => $totalRevenue > 0
                    ? round($grossProfit / $totalRevenue * 100, 2) : 0,
            ],
            'by_product' => $profitByProduct,
        ];
    }

    /**
     * Top Selling Products
     */
    public function topProducts(string $from, string $to, int $limit = 10): array
    {
        return SaleDetail::whereHas('saleHead', function ($q) use ($from, $to) {
            $q->completed()->dateRange($from, $to);
        })
        ->select(
            'product_id', 'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
        ->groupBy('product_id', 'product_name')
        ->orderByDesc('total_qty')
        ->limit($limit)
        ->get()
        ->toArray();
    }

    /**
     * Laporan Stok
     */
    public function stockReport(): array
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'category' => $p->category->name,
                    'stock' => $p->stock,
                    'min_stock' => $p->min_stock,
                    'unit' => $p->unit,
                    'cost_price' => $p->cost_price,
                    'stock_value' => $p->stock * $p->cost_price,
                    'is_low_stock' => $p->isLowStock(),
                ];
            });

        return [
            'total_items' => $products->count(),
            'total_stock_value' => $products->sum('stock_value'),
            'low_stock_count' => $products->where('is_low_stock', true)->count(),
            'products' => $products,
        ];
    }

    /**
     * Laporan Purchase
     */
    public function purchaseReport(string $from, string $to): array
    {
        $purchases = PurchaseHead::whereBetween('purchase_date', [$from, $to])
            ->with('details', 'supplier', 'user')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return [
            'period' => ['from' => $from, 'to' => $to],
            'summary' => [
                'total_purchases' => $purchases->count(),
                'total_amount' => $purchases->sum('grand_total'),
                'pending' => $purchases->where('status', 'pending')->count(),
                'received' => $purchases->where('status', 'received')->count(),
            ],
            'transactions' => $purchases,
        ];
    }
}