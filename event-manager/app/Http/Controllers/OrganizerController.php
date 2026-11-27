<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class OrganizerController extends Controller
{
    public function dashboard()
    {
        $events = Auth::user()->organizedEvents()
            ->withCount([
                'registrations as registrations_count' => fn ($query) => $query->where('status', '!=', 'cancelled'),
                'registrations as checked_in_count' => fn ($query) => $query->where('status', 'checked_in'),
            ])
            ->latest()
            ->paginate(12);

        return view('organizer.dashboard', compact('events'));
    }
}
