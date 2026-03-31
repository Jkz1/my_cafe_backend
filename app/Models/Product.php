<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['name', 'slug','category_id', 'description', 'price', 'stock', 'is_available', 'image_path'])]
class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? asset('storage/' . $value) : null,
        );
    }
}
