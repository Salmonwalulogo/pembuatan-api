<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Product::latest()->paginate(10));
    }

    public function store(Request $request, ProductImageProcessor $images): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'product_code' => ['nullable', 'string', 'max:100', 'unique:products,product_code'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'image_rotation' => ['sometimes', 'integer', 'in:0,90,180,270'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $rotation = (int) ($validated['image_rotation'] ?? 0);
        unset($validated['image_rotation']);

        if ($request->hasFile('image')) {
            $validated['image'] = $images->store($request->file('image'), $rotation);
        }

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product, ProductImageProcessor $images): JsonResponse
    {
        $required = $request->isMethod('put') ? ['required'] : ['sometimes', 'required'];
        $validated = $request->validate([
            'name' => [...$required, 'string', 'max:255'],
            'product_code' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('products', 'product_code')->ignore($product)],
            'barcode' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($product)],
            'category' => [...$required, 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => [...$required, 'numeric', 'min:0'],
            'stock' => [...$required, 'integer', 'min:0'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'image_rotation' => ['sometimes', 'integer', 'in:0,90,180,270'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $rotation = (int) ($validated['image_rotation'] ?? 0);
        unset($validated['image_rotation']);

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

        return response()->json($product->fresh());
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
