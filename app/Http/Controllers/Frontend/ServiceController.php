<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\ServiceInquiry;
use Illuminate\Http\Request;

class ServiceController extends Controller {
    public function index(Request $request) {
        $query = Service::active();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $services = $query->orderBy('sort_order', 'asc')->latest()->get();
        $featuredServices = Service::active()->featured()->take(3)->get();
        $categories = Service::active()->select('category')->distinct()->pluck('category')->filter()->values();

        return view('frontend.services.index', compact('services', 'featuredServices', 'categories'));
    }

    public function show(string $slug) {
        $service = Service::active()->where('slug', $slug)->firstOrFail();
        $relatedServices = Service::active()->where('id', '!=', $service->id)->where('category', $service->category)->take(3)->get();
        if ($relatedServices->isEmpty()) {
            $relatedServices = Service::active()->where('id', '!=', $service->id)->take(3)->get();
        }

        return view('frontend.services.show', compact('service', 'relatedServices'));
    }

    public function inquire(Request $request) {
        // Honeypot check
        if (!empty($request->input('commission_verify_trap'))) {
            return back()->with('success', 'Your commission request has been received.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|max:190',
            'company'     => 'nullable|string|max:150',
            'phone'       => 'nullable|string|max:50',
            'service_id'  => 'nullable|exists:services,id',
            'budget'      => 'nullable|string|max:100',
            'timeline'    => 'nullable|string|max:100',
            'message'     => 'required|string|min:15|max:3000',
        ]);

        $inquiry = ServiceInquiry::create($validated);

        if (class_exists(ActivityLog::class)) {
            $serviceName = $inquiry->service ? $inquiry->service->title : 'General Commission';
            ActivityLog::log("New client commission inquiry from {$inquiry->name} ({$inquiry->email}) for '{$serviceName}'", 'service');
        }

        return back()->with('commission_success', 'Your commission brief has been received by the Atelier. Our curators will review your specifications and contact you within 24 business hours.');
    }
}
