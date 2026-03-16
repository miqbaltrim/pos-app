<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with('product', 'user')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('stocks.index', compact('movements'));
    }

    public function adjustForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stocks.adjust', compact('products'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:in,out,adjustment',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'required|string|max:500',
        ]);

        // Pastikan user terautentikasi
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir, silakan login ulang.');
        }

        try {
            DB::transaction(function () use ($request, $userId) {

                // Lock row product untuk prevent race condition
                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $stockBefore = $product->stock;

                if ($request->type === 'out' && $product->stock < $request->quantity) {
                    throw new \Exception('Stok tidak cukup! Sisa stok: ' . $product->stock);
                }

                // Hitung stok baru
                $newStock = match ($request->type) {
                    'in'         => $stockBefore + $request->quantity,
                    'out'        => $stockBefore - $request->quantity,
                    'adjustment' => $request->quantity,
                };

                $product->update(['stock' => $newStock]);

                StockMovement::create([
                    'product_id'   => $product->id,
                    'type'         => $request->type,
                    'quantity'     => $request->type === 'adjustment'
                                        ? abs($newStock - $stockBefore)
                                        : $request->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after'  => $newStock,
                    'notes'        => $request->notes,
                    'user_id'      => (int) $userId,  // cast int untuk PostgreSQL
                ]);
            });

            return redirect()->route('stocks.index')
                ->with('success', 'Stok berhasil disesuaikan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}