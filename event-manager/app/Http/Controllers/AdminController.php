<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScannerLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $events = Event::withCount([
            'registrations as registrations_count' => fn ($query) => $query->where('status', '!=', 'cancelled'),
            'scannerLinks',
        ])->latest()->get();
        $organizers = User::where('role', 'organizer')->get();
        $attendeeCount = User::where('role', 'attendee')
            ->whereNotNull('email_verified_at')
            ->count();

        return view('admin.dashboard', compact('events', 'organizers', 'attendeeCount'));
    }

    public function users(Request $request)
    {
        $filters = $request->validate([
            'user_search' => ['nullable', 'string', 'max:100'],
            'user_role' => ['nullable', 'in:all,attendee,organizer'],
        ]);
        $userSearch = trim($filters['user_search'] ?? '');
        $userRole = $filters['user_role'] ?? 'all';
        $usersQuery = User::query()
            ->whereIn('role', ['organizer', 'attendee'])
            ->whereNotNull('email_verified_at')
            ->when($userRole !== 'all', fn ($query) => $query->where('role', $userRole))
            ->when($userSearch !== '', function ($query) use ($userSearch): void {
                $query->where(function ($query) use ($userSearch): void {
                    $query->where('name', 'like', "%{$userSearch}%")
                        ->orWhere('email', 'like', "%{$userSearch}%");
                });
            });
        $users = $usersQuery
            ->withCount('organizedEvents')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        $attendeeCount = User::where('role', 'attendee')
            ->whereNotNull('email_verified_at')
            ->count();
        $organizerCount = User::where('role', 'organizer')
            ->whereNotNull('email_verified_at')
            ->count();

        return view('admin.users', compact('users', 'userSearch', 'userRole', 'attendeeCount', 'organizerCount'));
    }

    public function generateScannerLink(Request $request, Event $event)
    {
        $request->validate([
            'expires_at' => 'nullable|date|after:now',
        ]);

        $organizer = $event->organizer;
        if (! $organizer || ! $organizer->isOrganizer()) {
            return back()->with('error', 'Assign an organizer to this event before generating a scanner link.');
        }

        $hash = $this->generateUniqueHash();

        $scannerLink = ScannerLink::create([
            'event_id' => $event->getKey(),
            'organizer_id' => $organizer->id,
            'hash' => $hash,
            'expires_at' => $request->input('expires_at'),
            'is_active' => true,
        ]);

        return back()->with('success', 'Scanner link generated successfully!')
            ->with('scanner_link', [
                'url' => $scannerLink->getScannerUrl(),
                'expires_at' => $scannerLink->expires_at
                    ? $scannerLink->expires_at->toIso8601String()
                    : null,
            ]);
    }

    public function revokeScannerLink(ScannerLink $scannerLink)
    {
        $scannerLink->update(['is_active' => false]);

        return back()->with('success', 'Scanner link has been revoked.');
    }

    public function eventScanners(Event $event)
    {
        $event->load('organizer');
        $scannerLinks = $event->scannerLinks()->with(['organizer', 'event'])->latest()->paginate(15);

        return view('admin.event-scanners', compact('event', 'scannerLinks'));
    }

    public function makeOrganizer(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot change admin role.');
        }

        $user->update(['role' => 'organizer']);

        return back()->with('success', "{$user->name} is now an organizer.");
    }

    public function removeOrganizer(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot change admin role.');
        }

        if ($user->organizedEvents()->exists()) {
            return back()->with('error', 'Reassign this organizer\'s events before removing their role.');
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
