<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller {
    public function index() {
        $widgets = Widget::orderBy('area')->orderBy('order')->get();
        return view('admin.widgets.index', compact('widgets'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'  => 'required|string|max:100',
            'type'  => 'required|string|max:50',
            'area'  => 'required|string|max:50',
            'title' => 'nullable|string|max:150',
        ]);

        $order = Widget::where('area', $request->input('area'))->max('order') + 1;

        Widget::create([
            'name'      => $request->input('name'),
            'type'      => $request->input('type'),
            'area'      => $request->input('area'),
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'order'     => $order,
            'is_active' => true,
        ]);

        return back()->with('success', 'Widget added successfully.');
    }

    public function toggle(Widget $widget) {
        $widget->update(['is_active' => !$widget->is_active]);
        return back()->with('success', 'Widget status toggled.');
    }

    public function destroy(Widget $widget) {
        $widget->delete();
        return back()->with('success', 'Widget removed.');
    }
}
