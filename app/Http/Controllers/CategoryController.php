<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id) {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(StoreCategoryRequest $r)
    {
        $category = $this->categoryService->store($r->validated());
        return response()->json($category, 201);
    }

    public function update(UpdateCategoryRequest $r, $id)
    {
        $category = Category::findOrFail($id);
        $res = $this->categoryService->update($category, $r->validated());
        return response()->json($res, 200);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
