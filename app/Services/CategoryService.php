<?php
namespace App\Services;

use App\Models\Category;
use Str;

class CategoryService
{
    public function store($data) {
        $data["slug"] = str($data["name"])->slug();
        $cat = Category::create($data);
        return $cat;
    }
    public function update(Category $category, array $data): Category
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);
        
        return $category;
    }
}