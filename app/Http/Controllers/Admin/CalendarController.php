<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller {
    public function index() {
        return view('admin.apps.calendar');
    }

    public function events() {
        $events = CalendarEvent::all()->map(function ($ev) {
            return [
                'id'        => $ev->id,
                'title'     => $ev->title,
                'start'     => $ev->start_time->toIso8601String(),
                'end'       => $ev->end_time ? $ev->end_time->toIso8601String() : null,
                'allDay'    => $ev->is_all_day,
                'className' => 'bg-' . $ev->color . ' border-0 text-white',
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request) {
        $request->merge([
            'start' => $request->input('start', $request->input('start_date')),
            'end'   => $request->input('end', $request->input('end_date')),
        ]);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'start'      => 'required|string',
            'end'        => 'nullable|string',
            'allDay'     => 'nullable|boolean',
            'color'      => 'nullable|string|in:primary,warning,success,danger,info,secondary',
        ]);

        $event = CalendarEvent::create([
            'title'      => $data['title'],
            'start_time' => date('Y-m-d H:i:s', strtotime($data['start'])),
            'end_time'   => !empty($data['end']) ? date('Y-m-d H:i:s', strtotime($data['end'])) : null,
            'is_all_day' => $request->boolean('allDay', false),
            'color'      => $data['color'] ?? 'primary',
            'user_id'    => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'event'   => [
                'id'        => $event->id,
                'title'     => $event->title,
                'start'     => $event->start_time->toIso8601String(),
                'end'       => $event->end_time ? $event->end_time->toIso8601String() : null,
                'className' => 'bg-' . $event->color . ' border-0 text-white',
            ],
        ], 201);
    }

    public function update(Request $request, CalendarEvent $event) {
        $request->merge([
            'start' => $request->input('start', $request->input('start_date', $event->start_time->toIso8601String())),
            'end'   => $request->input('end', $request->input('end_date')),
        ]);

        $data = $request->validate([
            'title'  => 'nullable|string|max:255',
            'start'  => 'required|string',
            'end'    => 'nullable|string',
            'allDay' => 'nullable|boolean',
            'color'  => 'nullable|string|in:primary,warning,success,danger,info,secondary',
        ]);

        $event->update([
            'title'      => $data['title'] ?? $event->title,
            'start_time' => date('Y-m-d H:i:s', strtotime($data['start'])),
            'end_time'   => !empty($data['end']) ? date('Y-m-d H:i:s', strtotime($data['end'])) : null,
            'is_all_day' => $request->boolean('allDay', $event->is_all_day),
            'color'      => $data['color'] ?? $event->color,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(CalendarEvent $event) {
        $event->delete();
        return response()->json(['success' => true]);
    }
}
