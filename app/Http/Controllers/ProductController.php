<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_available' => 'required|boolean',
            'image' => 'sometimes|image|max:2048'
        ]);
        $path = $request->file('image')?->store('products', 'public');
        $product = Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'is_available' => $validated['is_available'],
            'image_path' => $path ?? 'defaults/no-image.png',
            'slug' => str($validated['name'])->slug()
        ]);
        return response()->json($product, 201);
    }
    public function update(Request $r, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $r->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer',
            'is_available' => 'sometimes|boolean',
            'image' => 'sometimes|image|max:2048',
        ]);

        $product->update($validated);
        
        if ($r->hasFile('image')) {
            $product->image_path = $r->file('image')->store('products', 'public');
        }

        // Handle the slug only if the name was changed
        if ($r->has('name')) {
            $product->slug = str($r->name)->slug();
        }

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);

    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
