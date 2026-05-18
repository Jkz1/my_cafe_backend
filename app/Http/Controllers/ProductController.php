<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {
        $products = Product::paginate(100);
        return ProductResource::collection($products);
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        return response()->json([
            'message' => 'Product created!',
            'data' => new ProductResource($product)
        ], 201);
    }
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $updatedProduct = $this->productService->update($product, $request->validated());

        return response()->json([
            'message' => 'Product updated!',
            'data' => new ProductResource($updatedProduct)
        ], 200);
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
