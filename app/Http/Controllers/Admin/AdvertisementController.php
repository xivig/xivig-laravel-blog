<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdvertisementController extends Controller {
    public function index() {
        $ads = Advertisement::latest()->paginate(15);

        $totalCampaigns = Advertisement::count();
        $activeCampaigns = Advertisement::where('is_active', true)->count();
        $totalImpressions = (int) Advertisement::sum('impressions');
        $totalClicks = (int) Advertisement::sum('clicks');
        $overallCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.0;

        return view('admin.ads.index', compact(
            'ads',
            'totalCampaigns',
            'activeCampaigns',
            'totalImpressions',
            'totalClicks',
            'overallCtr'
        ));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title'       => 'required|string|max:180',
            'slot'        => 'required|string|in:header,sidebar,in_article,footer',
            'target_url'  => 'nullable|url|max:255',
            'custom_code' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'is_active'   => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'ad_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('ads', $filename, 'public');
            $imagePath = '/storage/' . $path;

            $publicDir = public_path('storage/ads');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);
            @copy($file->getRealPath(), $publicDir . '/' . $filename);
        }

        $ad = Advertisement::create([
            'title'       => $data['title'],
            'slot'        => $data['slot'],
            'target_url'  => $data['target_url'] ?? null,
            'custom_code' => $data['custom_code'] ?? null,
            'image_path'  => $imagePath,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('created', "Launched ad campaign '{$ad->title}' in slot {$ad->slot}", $ad);

        return back()->with('success', 'Advertising campaign launched successfully.');
    }

    public function update(Request $request, Advertisement $ad) {
        $data = $request->validate([
            'title'       => 'required|string|max:180',
            'slot'        => 'required|string|in:header,sidebar,in_article,footer',
            'target_url'  => 'nullable|url|max:255',
            'custom_code' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'ad_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('ads', $filename, 'public');
            $data['image_path'] = '/storage/' . $path;

            $publicDir = public_path('storage/ads');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);
            @copy($file->getRealPath(), $publicDir . '/' . $filename);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $ad->update($data);

        ActivityLog::record('updated', "Updated ad campaign '{$ad->title}'", $ad);

        return back()->with('success', 'Advertising campaign updated successfully.');
    }

    public function toggle(Advertisement $ad) {
        $ad->update(['is_active' => !$ad->is_active]);

        $statusStr = $ad->is_active ? 'activated' : 'paused';
        ActivityLog::record('updated', "Campaign '{$ad->title}' {$statusStr}", $ad);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success'   => true,
                'is_active' => $ad->is_active,
                'message'   => "Campaign status changed to {$statusStr}.",
            ]);
        }

        return back()->with('success', "Campaign '{$ad->title}' {$statusStr}.");
    }

    public function destroy(Advertisement $ad) {
        $title = $ad->title;
        $ad->delete();

        ActivityLog::record('deleted', "Deleted ad campaign '{$title}'", null);

        return back()->with('success', "Campaign '{$title}' removed.");
    }
}
