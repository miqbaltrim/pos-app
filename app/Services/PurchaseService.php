<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseHead;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * @param array $data
     * @param array $items
     * @param int   $userId  ← dari controller: $request->user()->id
     */
    public function createPurchase(array $data, array $items, int $userId): PurchaseHead
    {
        return DB::transaction(function () use ($data, $items, $userId) {

            $subtotal = 0;
            $detailRows = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $item['unit_cost'] * $item['quantity'];

                $detailRows[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_cost'    => $item['unit_cost'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $itemSubtotal,
                ];

                $subtotal += $itemSubtotal;
            }

            $purchase = PurchaseHead::create([
                'supplier_id'     => $data['supplier_id'],
                'user_id'         => $userId,
                'purchase_date'   => $data['purchase_date'] ?? now()->toDateString(),
                'subtotal'        => $subtotal,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount'      => $data['tax_amount'] ?? 0,
                'grand_total'     => $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0),
                'status'          => 'pending',
                'notes'           => $data['notes'] ?? null,
            ]);

            foreach ($detailRows as $row) {
                $purchase->details()->create($row);
            }

            return $purchase->load('details.product', 'supplier');
        });
    }

    /**
     * @param PurchaseHead $purchase
     * @param int          $userId  ← dari controller: $request->user()->id
     */
    public function receivePurchase(PurchaseHead $purchase, int $userId): PurchaseHead
    {
        return DB::transaction(function () use ($purchase, $userId) {

            if ($purchase->status !== 'pending') {
                throw new \Exception('Purchase sudah di-receive atau di-cancel');
            }

            foreach ($purchase->details as $detail) {
                $product = Product::find($detail->product_id);
                $stockBefore = $product->stock;
                $product->increment('stock', $detail->quantity);
                $product->update(['cost_price' => $detail->unit_cost]);

                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => 'in',
                    'quantity'       => $detail->quantity,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $product->fresh()->stock,
                    'reference_type' => PurchaseHead::class,
                    'reference_id'   => $purchase->id,
                    'notes'          => 'Purchase: ' . $purchase->purchase_number,
                    'user_id'        => $userId,
                ]);
            }

            $purchase->update(['status' => 'received']);

            return $purchase->fresh();
        });
    }
}