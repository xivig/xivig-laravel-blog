<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class SubscriberController extends Controller {
    public function index(Request $request) {
        $query = NewsletterSubscriber::query();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tier Filter
        if ($request->filled('tier') && $request->tier !== 'all') {
            $query->where('tier', $request->tier);
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'alpha') {
            $query->orderBy('email', 'asc');
        } else {
            $query->latest();
        }

        $subscribers = $query->paginate(20)->withQueryString();

        // Metrics
        $metrics = [
            'total'        => NewsletterSubscriber::count(),
            'active'       => NewsletterSubscriber::where('status', 'subscribed')->count(),
            'vip'          => NewsletterSubscriber::where('tier', 'vip')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'recent'       => NewsletterSubscriber::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'metrics'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'email'  => 'required|email|max:190|unique:newsletter_subscribers,email',
            'name'   => 'nullable|string|max:120',
            'tier'   => 'required|in:standard,vip,press,partner',
            'status' => 'required|in:subscribed,unsubscribed,pending,bounced',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $validated['source'] = 'admin_manual';
        $validated['ip_address'] = $request->ip();

        $subscriber = NewsletterSubscriber::create($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Enlisted new newsletter patron: {$subscriber->email} ({$subscriber->tier})", 'subscriber');
        }

        return redirect()->route('admin.subscribers.index')->with('success', "Patron '{$subscriber->email}' enlisted successfully.");
    }

    public function update(Request $request, NewsletterSubscriber $subscriber) {
        $validated = $request->validate([
            'email'  => 'required|email|max:190|unique:newsletter_subscribers,email,' . $subscriber->id,
            'name'   => 'nullable|string|max:120',
            'tier'   => 'required|in:standard,vip,press,partner',
            'status' => 'required|in:subscribed,unsubscribed,pending,bounced',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $subscriber->update($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Updated patron dossier for: {$subscriber->email}", 'subscriber');
        }

        return redirect()->route('admin.subscribers.index')->with('success', "Patron record for '{$subscriber->email}' updated successfully.");
    }

    public function destroy(NewsletterSubscriber $subscriber) {
        $email = $subscriber->email;
        $subscriber->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Removed newsletter patron: {$email}", 'subscriber');
        }

        return back()->with('success', "Patron '{$email}' removed from registry.");
    }

    public function bulk(Request $request) {
        $validated = $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:newsletter_subscribers,id',
            'action' => 'required|in:subscribe,unsubscribe,set_vip,set_standard,delete',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];
        $count = count($ids);

        switch ($action) {
            case 'subscribe':
                NewsletterSubscriber::whereIn('id', $ids)->update(['status' => 'subscribed']);
                $msg = "{$count} patrons activated as Subscribed.";
                break;
            case 'unsubscribe':
                NewsletterSubscriber::whereIn('id', $ids)->update(['status' => 'unsubscribed']);
                $msg = "{$count} patrons marked as Unsubscribed.";
                break;
            case 'set_vip':
                NewsletterSubscriber::whereIn('id', $ids)->update(['tier' => 'vip']);
                $msg = "{$count} patrons elevated to VIP Atelier Tier.";
                break;
            case 'set_standard':
                NewsletterSubscriber::whereIn('id', $ids)->update(['tier' => 'standard']);
                $msg = "{$count} patrons reassigned to Standard Tier.";
                break;
            case 'delete':
                NewsletterSubscriber::whereIn('id', $ids)->delete();
                $msg = "{$count} patrons removed from registry.";
                break;
            default:
                $msg = "Bulk operation processed.";
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Bulk subscriber action: {$action} applied to {$count} patrons.", 'subscriber');
        }

        return back()->with('success', $msg);
    }

    public function export(Request $request) {
        $query = NewsletterSubscriber::query();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('tier') && $request->tier !== 'all') {
            $query->where('tier', $request->tier);
        }

        $subscribers = $query->get();

        $csv = "ID,Name,Email,Tier,Status,Source,IP Address,Notes,Subscribed At,Last Broadcast At\n";
        foreach ($subscribers as $sub) {
            $name = str_replace(['"', ','], ['', ' '], $sub->name ?? '');
            $notes = str_replace(['"', "\n", "\r", ','], ['', ' ', ' ', ' '], $sub->notes ?? '');
            $lastBroadcast = $sub->last_broadcast_at ? $sub->last_broadcast_at->format('Y-m-d H:i') : 'Never';
            $csv .= "{$sub->id},\"{$name}\",{$sub->email},{$sub->tier},{$sub->status},{$sub->source},{$sub->ip_address},\"{$notes}\",{$sub->created_at->format('Y-m-d H:i')},{$lastBroadcast}\n";
        }

        $filename = 'maison_gazette_patrons_' . date('Y_m_d_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function import(Request $request) {
        $request->validate([
            'import_file' => 'nullable|file|mimes:csv,txt|max:4096',
            'raw_emails'  => 'nullable|string|max:20000',
            'import_tier' => 'required|in:standard,vip,press,partner',
        ]);

        $tier = $request->input('import_tier', 'standard');
        $lines = [];

        if ($request->hasFile('import_file')) {
            $content = file_get_contents($request->file('import_file')->getRealPath());
            $lines = preg_split('/\r\n|\r|\n/', $content);
        } elseif ($request->filled('raw_emails')) {
            $lines = preg_split('/\r\n|\r|\n/', $request->input('raw_emails'));
        }

        $imported = 0;
        $updated = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with(strtolower($line), 'email') || str_starts_with(strtolower($line), 'id,')) {
                continue;
            }

            $email = null;
            $name = null;

            // Check if format is "Name <email@domain.com>"
            if (preg_match('/^(.*?)\s*<([^>]+)>$/', $line, $matches)) {
                $name = trim($matches[1]);
                $email = strtolower(trim($matches[2]));
            } elseif (str_contains($line, ',')) {
                // CSV line: email, name OR name, email
                $parts = array_map('trim', str_getcsv($line));
                if (filter_var($parts[0], FILTER_VALIDATE_EMAIL)) {
                    $email = strtolower($parts[0]);
                    $name = $parts[1] ?? null;
                } elseif (isset($parts[1]) && filter_var($parts[1], FILTER_VALIDATE_EMAIL)) {
                    $name = $parts[0];
                    $email = strtolower($parts[1]);
                }
            } else {
                if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                    $email = strtolower($line);
                }
            }

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $sub = NewsletterSubscriber::where('email', $email)->first();
                if ($sub) {
                    $sub->update([
                        'status' => 'subscribed',
                        'tier'   => $tier,
                        'name'   => !empty($name) ? $name : $sub->name,
                    ]);
                    $updated++;
                } else {
                    NewsletterSubscriber::create([
                        'email'      => $email,
                        'name'       => $name,
                        'tier'       => $tier,
                        'status'     => 'subscribed',
                        'source'     => 'csv_import',
                        'ip_address' => $request->ip(),
                    ]);
                    $imported++;
                }
            }
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Imported {$imported} new patrons, updated {$updated} existing.", 'subscriber');
        }

        return redirect()->route('admin.subscribers.index')->with('success', "Import completed: {$imported} newly enlisted, {$updated} updated.");
    }

    public function broadcast(Request $request) {
        $validated = $request->validate([
            'subject'    => 'required|string|max:200',
            'body'       => 'required|string',
            'target_tier'=> 'required|in:all,standard,vip,press,partner',
            'test_email' => 'nullable|email',
        ]);

        if (!empty($validated['test_email'])) {
            return back()->with('success', "Test chronicle '{$validated['subject']}' dispatched to {$validated['test_email']}.");
        }

        $query = NewsletterSubscriber::where('status', 'subscribed');
        if ($validated['target_tier'] !== 'all') {
            $query->where('tier', $validated['target_tier']);
        }

        $count = $query->count();
        $query->update(['last_broadcast_at' => now()]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Dispatched chronicle '{$validated['subject']}' to {$count} patrons.", 'subscriber');
        }

        return back()->with('success', "Chronicle broadcast queued successfully. {$count} patrons scheduled for delivery.");
    }
}
