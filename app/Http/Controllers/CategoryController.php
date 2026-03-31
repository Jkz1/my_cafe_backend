<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id) {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $r)
    {
        $validated = $r->validate([
            "name" => "required|string|max:255|unique:categories",
        ]);
        $validated["slug"] = str($validated["name"])->slug();
        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $r, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $r->validate([
            "name" => "sometimes|string|max:255|unique:categories,name," . $id
        ]);
        if (isset($validated['name'])) {
            $validated['slug'] = str($validated['name'])->slug();
        }
        $category->update($validated);
        return response()->json($category, 200);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
