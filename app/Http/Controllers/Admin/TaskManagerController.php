<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskManagerController extends Controller {
    public function index() {
        $sprintTasks = Task::where('status', 'sprint')->orderBy('order')->latest()->get();
        $queueTasks = Task::where('status', 'queue')->orderBy('order')->latest()->get();
        $archivedTasks = Task::where('status', 'archived')->orderBy('order')->latest()->get();

        return view('admin.apps.task-manager', compact('sprintTasks', 'queueTasks', 'archivedTasks'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'avatar_badge' => 'nullable|string|max:10',
            'badge_color'  => 'nullable|string|max:20',
            'status'       => 'nullable|string|in:sprint,queue,archived',
        ]);

        $task = Task::create([
            'title'        => $data['title'],
            'avatar_badge' => $data['avatar_badge'] ?? strtoupper(substr($data['title'], 0, 2)),
            'badge_color'  => $data['badge_color'] ?? 'primary',
            'status'       => $data['status'] ?? 'sprint',
            'order'        => Task::where('status', $data['status'] ?? 'sprint')->count(),
            'user_id'      => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return back()->with('success', 'Task created successfully.');
    }

    public function updateStatus(Request $request, Task $task) {
        $data = $request->validate([
            'status' => 'required|string|in:sprint,queue,archived',
            'order'  => 'nullable|integer',
        ]);

        $task->update([
            'status' => $data['status'],
            'order'  => $data['order'] ?? $task->order,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task) {
        $task->delete();
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Task deleted.');
    }
}
