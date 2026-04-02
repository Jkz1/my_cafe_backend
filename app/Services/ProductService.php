<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function create(array $data): Product
    {
        if (isset($data['image'])) {
            $data['image_path'] = $data['image']->store('products', 'public');
        } else {
            $data['image_path'] = 'defaults/no-image.png';
        }

        $data['slug'] = Str::slug($data['name']);

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['image'])) {
            if ($product->image_path !== 'defaults/no-image.png') {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $data['image']->store('products', 'public');
        }

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);
        return $product;
    }
}