<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->withCount(['registrations as registrations_count' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Location Filter
        if ($request->filled('location')) {
            $query->where('city', 'like', "%{$request->location}%");
        }

        // Date Filter
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'upcoming':
                    $query->where('start_date', '>=', now());
                    break;
                case 'this_week':
                    $query->whereBetween('start_date', [now(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        // Sort
        $sort = $request->get('sort', 'date_asc');
        switch ($sort) {
            case 'date_desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'popular':
                $query->orderBy('registrations_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
            default:
                $query->orderBy('start_date', 'asc');
                break;
        }

        $events = $query->paginate(12)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'max_attendees' => 'nullable|integer|min:1',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Festival created successfully.');
    }

    public function show(Event $event)
    {
        $event->loadCount(['registrations as registrations_count' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }, 'registrations as checked_in_count' => function ($q) {
            $q->where('status', 'checked_in');
        }]);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'max_attendees' => 'nullable|integer|min:1',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        if ($request->boolean('remove_image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = null;
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Festival updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Festival deleted successfully.');
    }
}