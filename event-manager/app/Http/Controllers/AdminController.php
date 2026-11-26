<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScannerLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $events = Event::withCount(['registrations', 'scannerLinks'])->latest()->get();
        $organizers = User::where('role', 'organizer')->get();

        return view('admin.dashboard', compact('events', 'organizers'));
    }

    public function generateScannerLink(Request $request, Event $event)
    {
        $request->validate([
            'organizer_id' => 'required|exists:users,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $organizer = User::findOrFail($request->organizer_id);

        if (!$organizer->isOrganizer()) {
            return back()->with('error', 'Selected user is not an organizer.');
        }

        $hash = $this->generateUniqueHash();

        $scannerLink = ScannerLink::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'hash' => $hash,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return back()->with('success', 'Scanner link generated successfully!')
            ->with('scanner_link', $scannerLink);
    }

    public function revokeScannerLink(ScannerLink $scannerLink)
    {
        $scannerLink->update(['is_active' => false]);

        return back()->with('success', 'Scanner link has been revoked.');
    }

    public function eventScanners(Event $event)
    {
        $scannerLinks = $event->scannerLinks()->with('organizer')->latest()->paginate(15);

        return view('admin.event-scanners', compact('event', 'scannerLinks'));
    }

    public function makeOrganizer(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot change admin role.');
        }

        $user->update(['role' => 'organizer']);

        return back()->with('success', "{$user->name} is now an organizer.");
    }

    public function removeOrganizer(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot change admin role.');
        }

        $user->update(['role' => 'attendee']);

        return back()->with('success', "{$user->name} is no longer an organizer.");
    }

    private function generateUniqueHash(): string
    {
        do {
            $hash = Str::random(32);
        } while (ScannerLink::where('hash', $hash)->exists());

        return $hash;
    }
}