<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\ServiceInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller {
    public function index(Request $request) {
        $query = Service::withCount('inquiries');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $services = $query->orderBy('sort_order', 'asc')->latest()->paginate(15)->withQueryString();

        $categories = Service::select('category')->distinct()->pluck('category')->filter()->values();

        $metrics = [
            'total'            => Service::count(),
            'active'           => Service::where('status', 'active')->count(),
            'featured'         => Service::where('is_featured', true)->count(),
            'total_inquiries'  => ServiceInquiry::count(),
            'pending_inquiries'=> ServiceInquiry::where('status', 'pending')->count(),
        ];

        return view('admin.services.index', compact('services', 'categories', 'metrics'));
    }

    public function create() {
        $categories = [
            'Editorial & Press',
            'Creative Direction',
            'Private Curation',
            'Digital Architecture',
            'Horology Advisory',
            'Brand Strategy',
        ];

        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title'           => 'required|string|max:190',
            'slug'            => 'nullable|string|max:190|unique:services,slug',
            'category'        => 'required|string|max:100',
            'tagline'         => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'icon'            => 'nullable|string|max:100',
            'badge'           => 'nullable|string|max:80',
            'cover_image'     => 'nullable|string|max:500',
            'pricing_type'    => 'required|in:fixed,starting_at,hourly,retainer,quote',
            'price'           => 'nullable|numeric|min:0',
            'price_unit'      => 'nullable|string|max:50',
            'delivery_time'   => 'nullable|string|max:100',
            'features'        => 'nullable',
            'target_audience' => 'nullable|string|max:255',
            'is_featured'     => 'nullable|boolean',
            'status'          => 'required|in:active,draft,archived',
            'sort_order'      => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Service::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['icon'] = !empty($validated['icon']) ? $validated['icon'] : 'bi-gem';

        // Parse features
        if (isset($validated['features']) && is_string($validated['features'])) {
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features'])));
            $validated['features'] = array_values($lines);
        }

        $service = Service::create($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Created atelier service offering: {$service->title}", 'service');
        }

        return redirect()->route('admin.services.index')->with('success', "Service '{$service->title}' created successfully.");
    }

    public function edit(Service $service) {
        $categories = [
            'Editorial & Press',
            'Creative Direction',
            'Private Curation',
            'Digital Architecture',
            'Horology Advisory',
            'Brand Strategy',
        ];

        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service) {
        $validated = $request->validate([
            'title'           => 'required|string|max:190',
            'slug'            => 'required|string|max:190|unique:services,slug,' . $service->id,
            'category'        => 'required|string|max:100',
            'tagline'         => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'icon'            => 'nullable|string|max:100',
            'badge'           => 'nullable|string|max:80',
            'cover_image'     => 'nullable|string|max:500',
            'pricing_type'    => 'required|in:fixed,starting_at,hourly,retainer,quote',
            'price'           => 'nullable|numeric|min:0',
            'price_unit'      => 'nullable|string|max:50',
            'delivery_time'   => 'nullable|string|max:100',
            'features'        => 'nullable',
            'target_audience' => 'nullable|string|max:255',
            'is_featured'     => 'nullable|boolean',
            'status'          => 'required|in:active,draft,archived',
            'sort_order'      => 'nullable|integer',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['icon'] = !empty($validated['icon']) ? $validated['icon'] : ($service->icon ?: 'bi-gem');

        // Parse features
        if (isset($validated['features']) && is_string($validated['features'])) {
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features'])));
            $validated['features'] = array_values($lines);
        }

        $service->update($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Updated atelier service: {$service->title}", 'service');
        }

        return redirect()->route('admin.services.index')->with('success', "Service '{$service->title}' updated successfully.");
    }

    public function destroy(Service $service) {
        $title = $service->title;
        $service->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Removed atelier service: {$title}", 'service');
        }

        return back()->with('success', "Service '{$title}' moved to trash.");
    }

    public function toggleStatus(Service $service) {
        $newStatus = $service->status === 'active' ? 'draft' : 'active';
        $service->update(['status' => $newStatus]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Toggled status for {$service->title} to {$newStatus}", 'service');
        }

        return back()->with('success', "Service '{$service->title}' is now {$newStatus}.");
    }

    public function toggleFeatured(Service $service) {
        $service->update(['is_featured' => !$service->is_featured]);

        $statusStr = $service->is_featured ? 'featured' : 'standard';
        return back()->with('success', "Service '{$service->title}' set to {$statusStr}.");
    }

    // Client Inquiries Desk
    public function inquiries(Request $request) {
        $query = ServiceInquiry::with('service');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id') && $request->service_id !== 'all') {
            $query->where('service_id', $request->service_id);
        }

        $inquiries = $query->latest()->paginate(20)->withQueryString();
        $services = Service::orderBy('title')->get();

        $metrics = [
            'total'     => ServiceInquiry::count(),
            'pending'   => ServiceInquiry::where('status', 'pending')->count(),
            'contacted' => ServiceInquiry::where('status', 'contacted')->count(),
            'converted' => ServiceInquiry::where('status', 'converted')->count(),
        ];

        return view('admin.services.inquiries', compact('inquiries', 'services', 'metrics'));
    }

    public function updateInquiry(Request $request, ServiceInquiry $inquiry) {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,contacted,converted,archived',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $inquiry->update($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Updated inquiry #{$inquiry->id} from {$inquiry->name} ({$inquiry->status})", 'service');
        }

        return back()->with('success', "Inquiry dossier for {$inquiry->name} updated.");
    }

    public function destroyInquiry(ServiceInquiry $inquiry) {
        $name = $inquiry->name;
        $inquiry->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log("Deleted inquiry from {$name}", 'service');
        }

        return back()->with('success', "Inquiry from {$name} removed.");
    }
}
