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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_available' => 'required|boolean',
            'image_path' => 'nullable|string|max:255'
        ]);
        $validated['slug'] = \Illuminate\Support\Str::slug($request->name);
        $product = Product::create($validated);
        return response()->json($product, 201);
    }
}
