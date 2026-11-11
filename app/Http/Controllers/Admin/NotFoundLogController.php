<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotFoundLog;
use Illuminate\Http\Request;

class NotFoundLogController extends Controller {
    public function index() {
        $logs = NotFoundLog::orderByDesc('hits')->paginate(20);
        return view('admin.not_found_logs.index', compact('logs'));
    }

    public function destroy(NotFoundLog $log) {
        $log->delete();
        return redirect()->back()->with('success', 'Log entry cleared.');
    }

    public function clear() {
        NotFoundLog::truncate();
        return redirect()->back()->with('success', 'All 404 monitoring records cleared.');
    }
}
