<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller {
    public function index() {
        $menus = Menu::with('items.children')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:100',
            'location' => 'required|string|max:50|unique:menus,location',
        ]);

        Menu::create($request->only('name', 'location'));
        return back()->with('success', 'Menu created successfully.');
    }

    public function addItem(Request $request, Menu $menu) {
        $request->validate([
            'title'     => 'required|string|max:120',
            'url'       => 'required|string|max:255',
            'target'    => 'nullable|string|in:_self,_blank',
            'parent_id' => 'nullable|exists:menu_items,id',
        ]);

        $order = $menu->items()->max('order') + 1;

        $menu->items()->create([
            'title'     => $request->input('title'),
            'url'       => $request->input('url'),
            'target'    => $request->input('target', '_self'),
            'parent_id' => $request->input('parent_id'),
            'order'     => $order,
        ]);

        return back()->with('success', 'Menu item added successfully.');
    }

    public function deleteItem(MenuItem $item) {
        $item->delete();
        return back()->with('success', 'Menu item removed.');
    }
}
