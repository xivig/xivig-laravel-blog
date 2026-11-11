<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller {
    public function index() {
        $tags = Tag::withCount('posts')->orderBy('name')->get();
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'slug' => 'nullable|string|max:50|unique:tags,slug',
        ]);

        Tag::create([
            'name' => strip_tags($data['name']),
            'slug' => !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
        ]);

        return back()->with('success', 'Tag created.');
    }

    public function update(Request $request, Tag $tag) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'slug' => 'nullable|string|max:50|unique:tags,slug,' . $tag->id,
        ]);

        $tag->update([
            'name' => strip_tags($data['name']),
            'slug' => !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
        ]);

        return back()->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag) {
        $tag->delete();
        return back()->with('success', 'Tag deleted.');
    }
}
