<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    public function index() {
        $categories = Category::withCount('posts')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'slug'        => 'nullable|string|max:100|unique:categories,slug',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        Category::create([
            'name'        => strip_tags($data['name']),
            'slug'        => $slug,
            'description' => $data['description'] ? strip_tags($data['description']) : null,
            'color'       => $data['color'] ?? '#cba358',
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category) {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug'        => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'is_featured' => 'nullable|boolean',
        ]);

        $category->update([
            'name'        => strip_tags($data['name']),
            'slug'        => !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
            'description' => $data['description'] ? strip_tags($data['description']) : null,
            'color'       => $data['color'] ?? '#cba358',
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category) {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
