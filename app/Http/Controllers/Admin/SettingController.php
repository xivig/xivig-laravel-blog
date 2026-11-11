<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = [
            'site_title'             => Setting::get('site_title', 'XIVIG Maison Gazette'),
            'site_tagline'           => Setting::get('site_tagline', 'The Haute Couture & Horology Journal'),
            'site_description'       => Setting::get('site_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and haute couture.'),
            'contact_email'          => Setting::get('contact_email', 'gazette@xivig.com'),
            'posts_per_page'         => Setting::get('posts_per_page', 9),
            'comment_auto_approve'   => Setting::get('comment_auto_approve', '1'),
            'timezone'               => Setting::get('timezone', config('app.timezone', 'UTC')),
            'maintenance_mode'       => Setting::get('maintenance_mode', '0'),
            'google_analytics_id'    => Setting::get('google_analytics_id', 'G-XXXXXXXXXX'),
            'openai_api_key'         => Setting::get('openai_api_key', ''),
            'mailchimp_api_key'      => Setting::get('mailchimp_api_key', ''),
            'mailchimp_list_id'      => Setting::get('mailchimp_list_id', 'XXXXXXXXXX'),
        ];

        $timezones = TimezoneHelper::list();

        return view('admin.settings.index', compact('settings', 'timezones'));
    }

    public function update(Request $request) {
        $data = $request->validate([
            'site_title'           => 'required|string|max:100',
            'site_tagline'         => 'nullable|string|max:200',
            'site_description'     => 'nullable|string|max:500',
            'contact_email'        => 'required|email|max:150',
            'posts_per_page'       => 'required|integer|min:3|max:50',
            'comment_auto_approve' => 'required|in:0,1',
            'timezone'             => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $val) {
            if ($val !== null) {
                Setting::set($key, (string) $val);
            }
        }

        ActivityLog::record('updated', 'Updated atelier & system settings', null, ['keys' => array_keys($data)]);

        return back()->with('success', 'Maison Gazette settings saved.');
    }

    public function toggleMaintenance(Request $request) {
        $enabled = $request->boolean('maintenance_mode');
        Setting::set('maintenance_mode', $enabled ? '1' : '0');

        $statusStr = $enabled ? 'activated' : 'deactivated';
        ActivityLog::record('updated', "Site maintenance mode {$statusStr}", null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'maintenance_mode' => $enabled ? '1' : '0',
                'message'          => "Site maintenance mode {$statusStr}.",
            ]);
        }

        return back()->with('success', "Site maintenance mode {$statusStr}.");
    }

    public function syncIntegrations(Request $request) {
        $data = $request->validate([
            'google_analytics_id' => 'nullable|string|max:100',
            'openai_api_key'      => 'nullable|string|max:255',
            'mailchimp_api_key'   => 'nullable|string|max:255',
            'mailchimp_list_id'   => 'nullable|string|max:100',
        ]);

        foreach ($data as $k => $v) {
            Setting::set($k, $v ?: '');
        }

        ActivityLog::record('updated', 'Synchronized external services & API integrations', null, ['keys' => array_keys($data)]);

        return back()->with('success', 'External integrations synchronized successfully.');
    }
}
