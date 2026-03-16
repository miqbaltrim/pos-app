<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SaleHead;
use App\Models\SaleDetail;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /**
     * @param array $data
     * @param array $items
     * @param int   $userId  ← dari controller: $request->user()->id
     */
    public function createSale(array $data, array $items, int $userId): SaleHead
    {
        return DB::transaction(function () use ($data, $items, $userId) {

            $subtotal = 0;
            $detailRows = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Sisa: {$product->stock}");
                }

                $itemDiscount = ($item['discount_percent'] ?? 0) / 100 * $product->selling_price * $item['quantity'];
                $itemSubtotal = ($product->selling_price * $item['quantity']) - $itemDiscount;

                $detailRows[] = [
                    'product_id'       => $product->id,
                    'product_name'     => $product->name,
                    'product_sku'      => $product->sku,
                    'unit_price'       => $product->selling_price,
                    'cost_price'       => $product->cost_price,
                    'quantity'         => $item['quantity'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount'  => $itemDiscount,
                    'subtotal'         => $itemSubtotal,
                ];

                $subtotal += $itemSubtotal;
            }

            $discountPercent = $data['discount_percent'] ?? 0;
            $discountAmount = $discountPercent / 100 * $subtotal;
            $afterDiscount = $subtotal - $discountAmount;

            $taxPercent = $data['tax_percent'] ?? 0;
            $taxAmount = $taxPercent / 100 * $afterDiscount;
            $grandTotal = $afterDiscount + $taxAmount;

            $paidAmount = $data['paid_amount'] ?? $grandTotal;
            $changeAmount = $paidAmount - $grandTotal;

            $saleHead = SaleHead::create([
                'customer_id'       => $data['customer_id'] ?? null,
                'user_id'           => $userId,
                'transaction_date'  => now()->toDateString(),
                'subtotal'          => $subtotal,
                'discount_percent'  => $discountPercent,
                'discount_amount'   => $discountAmount,
                'tax_percent'       => $taxPercent,
                'tax_amount'        => $taxAmount,
                'grand_total'       => $grandTotal,
                'paid_amount'       => $paidAmount,
                'change_amount'     => max(0, $changeAmount),
                'payment_method'    => $data['payment_method'] ?? 'cash',
                'payment_reference' => $data['payment_reference'] ?? null,
                'status'            => 'completed',
                'notes'             => $data['notes'] ?? null,
            ]);

            foreach ($detailRows as $row) {
                $saleHead->details()->create($row);

                $product = Product::find($row['product_id']);
                $stockBefore = $product->stock;
                $product->decrement('stock', $row['quantity']);

                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => 'out',
                    'quantity'       => $row['quantity'],
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $product->fresh()->stock,
                    'reference_type' => SaleHead::class,
                    'reference_id'   => $saleHead->id,
                    'notes'          => 'Penjualan: ' . $saleHead->invoice_number,
                    'user_id'        => $userId,
                ]);
            }

            if ($saleHead->customer_id) {
                $saleHead->customer->increment('total_purchases', $grandTotal);
                $saleHead->customer->increment('loyalty_points', floor($grandTotal / 10000));
            }

            return $saleHead->load('details.product', 'customer', 'user');
        });
    }

    /**
     * @param SaleHead $sale
     * @param int      $userId  ← dari controller: $request->user()->id
     */
    public function cancelSale(SaleHead $sale, int $userId): SaleHead
    {
        return DB::transaction(function () use ($sale, $userId) {

            if ($sale->status !== 'completed') {
                throw new \Exception('Hanya transaksi completed yang bisa di-cancel');
            }

            foreach ($sale->details as $detail) {
                $product = Product::find($detail->product_id);
                $stockBefore = $product->stock;
                $product->increment('stock', $detail->quantity);

                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => 'return',
                    'quantity'       => $detail->quantity,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $product->fresh()->stock,
                    'reference_type' => SaleHead::class,
                    'reference_id'   => $sale->id,
                    'notes'          => 'Cancel: ' . $sale->invoice_number,
                    'user_id'        => $userId,
                ]);
            }

            if ($sale->customer_id) {
                $sale->customer->decrement('total_purchases', $sale->grand_total);
            }

            $sale->update(['status' => 'cancelled']);

            return $sale->fresh();
        });
    }
}