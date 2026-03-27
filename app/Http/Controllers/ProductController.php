<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%")
                  ->orWhere('barcode', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode'       => 'nullable|string|unique:products,barcode',
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'min_stock'     => 'required|integer|min:0',
            'unit'          => 'required|string|max:20',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'barcode'       => 'nullable|string|unique:products,barcode,' . $product->id,
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock'     => 'required|integer|min:0',
            'unit'          => 'required|string|max:20',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle remove image
        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar kalau ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    // API: Search product (untuk POS & Purchase AJAX)
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'cost_price', 'selling_price', 'stock', 'unit', 'image')
            ->orderBy('name');

        if (strlen($q) >= 2) {
            // ilike = case-insensitive LIKE untuk PostgreSQL
            $query->where(function ($sub) use ($q) {
                $sub->where('name',    'ilike', "%{$q}%")
                    ->orWhere('sku',     'ilike', "%{$q}%")
                    ->orWhere('barcode', 'ilike', "%{$q}%");
            })->limit(15);
        } else {
            // Jika kosong: tampilkan 30 produk terbaru (untuk load awal POS)
            $query->limit(30);
        }

        $products = $query->get()->map(fn ($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'sku'           => $p->sku,
            'barcode'       => $p->barcode,
            'cost_price'    => $p->cost_price,
            'selling_price' => $p->selling_price,
            'stock'         => $p->stock,
            'unit'          => $p->unit,
            // URL lengkap agar langsung bisa dipakai di <img src>
            'image_url'     => $p->image ? asset('storage/' . $p->image) : null,
        ]);

        return response()->json($products);
    }
}