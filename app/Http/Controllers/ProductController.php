<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Produk terbaru akan muncul di halaman 1 (depan)
     */
    public function index()
    {
        // latest() = urutkan berdasarkan created_at DESC (terbaru di atas)
        // paginate(10) = 10 produk per halaman
        $products = Product::latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ProductImageProcessor $images)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => ['nullable', 'string', 'max:100', 'unique:products,product_code'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_rotation' => 'nullable|integer|in:0,90,180,270',
            'is_active' => 'nullable|boolean',
        ]);

        $rotation = (int) ($validated['image_rotation'] ?? 0);
        unset($validated['image_rotation']);

        // Pastikan description tidak null
        $validated['description'] = $validated['description'] ?? '';

        // Handle checkbox is_active
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $images->store($request->file('image'), $rotation);
        } else {
            $validated['image'] = null;
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductImageProcessor $images)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => ['nullable', 'string', 'max:100', Rule::unique('products', 'product_code')->ignore($product)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($product)],
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_rotation' => 'nullable|integer|in:0,90,180,270',
            'is_active' => 'nullable|boolean',
        ]);

        $rotation = (int) ($validated['image_rotation'] ?? 0);
        unset($validated['image_rotation']);
        $validated['description'] = $validated['description'] ?? '';
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            $previousImage = $product->image;
            $validated['image'] = $images->store($request->file('image'), $rotation);
        } else {
            $previousImage = null;
        }

        $product->update($validated);

        if ($previousImage && $previousImage !== $product->image) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
