<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller {
    public function index(Request $request) {
        $filters = $request->only(['action', 'user_id', 'search', 'date_from', 'date_to']);

        $logs = ActivityLog::with('user')
            ->filter($filters)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $metrics = [
            'total'     => ActivityLog::count(),
            'today'     => ActivityLog::whereDate('created_at', today())->count(),
            'users'     => ActivityLog::distinct('user_id')->count('user_id'),
            'logins'    => ActivityLog::whereIn('action', ['login', 'security'])->count(),
        ];

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        $actions = [
            'login'    => 'User Login',
            'logout'   => 'User Logout',
            'created'  => 'Created Record',
            'updated'  => 'Updated Record',
            'deleted'  => 'Deleted Record',
            'security' => 'Security / Passphrase',
            'sent'     => 'Sent Dispatch / Email',
        ];

        return view('admin.activity_logs.index', compact('logs', 'metrics', 'users', 'actions', 'filters'));
    }

    public function destroy(ActivityLog $activity_log) {
        $activity_log->delete();
        return back()->with('success', 'Audit log entry removed.');
    }

    public function clear(Request $request) {
        $olderThan = $request->input('days', 30);

        if ($olderThan === 'all') {
            ActivityLog::truncate();
            $message = 'All audit logs have been flushed.';
        } else {
            $days = max(1, (int) $olderThan);
            $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
            $message = "Audit logs older than {$days} days successfully purged ({$deleted} entries).";
        }

        ActivityLog::record('security', $message);

        return back()->with('success', $message);
    }

    public function export(Request $request): StreamedResponse {
        $filters = $request->only(['action', 'user_id', 'search', 'date_from', 'date_to']);
        $logs = ActivityLog::with('user')->filter($filters)->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="maison_activity_logs_' . date('Y_m_d_His') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['ID', 'Date & Time', 'User Name', 'User Email', 'Action', 'Description', 'IP Address', 'User Agent']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    TimezoneHelper::format($log->created_at, 'Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'System / Guest',
                    $log->user ? $log->user->email : 'N/A',
                    strtoupper($log->action),
                    $log->description,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
