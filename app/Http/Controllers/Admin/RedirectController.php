<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectController extends Controller {
    public function index() {
        $redirects = Redirect::latest()->paginate(15);
        return view('admin.redirects.index', compact('redirects'));
    }

    public function store(Request $request) {
        $request->validate([
            'source_url' => 'required|string|max:255',
            'target_url' => 'required|string|max:255',
            'status_code' => 'required|in:301,302,307,308',
        ]);

        $source = '/' . ltrim($request->input('source_url'), '/');
        $target = $request->input('target_url');

        Redirect::create([
            'source_url' => $source,
            'target_url' => $target,
            'status_code' => (int) $request->input('status_code'),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Redirect rule established successfully.');
    }

    public function update(Request $request, Redirect $redirect) {
        $request->validate([
            'source_url' => 'required|string|max:255',
            'target_url' => 'required|string|max:255',
            'status_code' => 'required|in:301,302,307,308',
        ]);

        $source = '/' . ltrim($request->input('source_url'), '/');

        $redirect->update([
            'source_url' => $source,
            'target_url' => $request->input('target_url'),
            'status_code' => (int) $request->input('status_code'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Redirect updated.');
    }

    public function destroy(Redirect $redirect) {
        $redirect->delete();
        return redirect()->back()->with('success', 'Redirect removed.');
    }
}
