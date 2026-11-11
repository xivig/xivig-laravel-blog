<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Setting;
use App\Services\SeoService;
use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SEOController extends Controller {
    public function index() {
        $robotsContent = Setting::get('robots_txt_custom', "User-agent: *\nDisallow: /admin\nAllow: /\nSitemap: " . url('/sitemap.xml'));
        $siteTitle = Setting::get('site_title', 'XIVIG Maison Gazette');
        $siteDescription = Setting::get('site_description', 'Chronicles of Haute Couture, Horology & Fine Savoir-Faire.');
        $siteKeywords = Setting::get('site_keywords', 'luxury, haute couture, horology, craftsmanship, atelier, editorial');

        $audits = Post::published()->latest('published_at')->take(10)->get()->map(function ($post) {
            return [
                'post'  => $post,
                'audit' => SeoService::auditPost($post),
            ];
        });

        $avgScore = $audits->count() > 0 ? (int) round($audits->avg(fn($a) => $a['audit']['score'])) : 85;

        return view('admin.seo.index', compact('robotsContent', 'siteTitle', 'siteDescription', 'siteKeywords', 'audits', 'avgScore'));
    }

    public function updateRobots(Request $request) {
        $request->validate(['robots_txt' => 'required|string']);
        Setting::set('robots_txt_custom', $request->input('robots_txt'), 'textarea', 'seo');
        return back()->with('success', 'Robots.txt configuration updated successfully.');
    }

    public function regenerateSitemap() {
        $xml = SitemapService::generateXml();
        File::put(public_path('sitemap.xml'), $xml);
        return back()->with('success', 'XML Sitemap refreshed successfully at ' . url('/sitemap.xml'));
    }
}
