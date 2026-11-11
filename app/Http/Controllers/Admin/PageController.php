<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\HtmlSanitizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller {
    public function index() {
        $pages = Page::latest()->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create() {
        return view('admin.pages.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:pages,slug',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:300',
            'template' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $slug = $request->input('slug') ? Str::slug($request->input('slug')) : Str::slug($request->input('title'));

        Page::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'content' => HtmlSanitizerService::cleanPage($request->input('content')),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'meta_keywords' => $request->input('meta_keywords'),
            'template' => $request->input('template', 'default'),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page published successfully.');
    }

    public function edit(Page $page) {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page) {
        $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:300',
            'template' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('slug')),
            'content' => HtmlSanitizerService::cleanPage($request->input('content')),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'meta_keywords' => $request->input('meta_keywords'),
            'template' => $request->input('template', 'default'),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page) {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}
