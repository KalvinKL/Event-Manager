<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'event_id' => ['nullable', 'integer', Rule::exists('events', 'id')->where('organizer_id', $user->getKey())],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
            'granularity' => ['nullable', Rule::in(['daily', 'weekly'])],
        ]);

        $dateFrom = Carbon::parse($validated['date_from'] ?? now()->subDays(13)->toDateString())->startOfDay();
        $dateTo = Carbon::parse($validated['date_to'] ?? now()->toDateString())->endOfDay();

        if ($dateFrom->gt($dateTo)) {
            return back()
                ->withErrors(['date_to' => 'The end date must be on or after the start date.'])
                ->withInput();
        }

        $granularity = $validated['granularity'] ?? 'daily';
        $selectedEventId = $validated['event_id'] ?? null;
        $allEvents = $user->organizedEvents()->orderBy('start_date')->get(['id', 'title']);
        $eventIds = $allEvents->pluck('id');
        $analyticsEventIds = $selectedEventId ? collect([(int) $selectedEventId]) : $eventIds;

        $totalRegistered = Registration::whereIn('event_id', $analyticsEventIds)
            ->where('status', '!=', 'cancelled')
            ->count();
        $totalCheckedIn = Registration::whereIn('event_id', $analyticsEventIds)
            ->where('status', 'checked_in')
            ->count();
        $attendanceRate = $totalRegistered > 0
            ? round(($totalCheckedIn / $totalRegistered) * 100, 1)
            : 0;

        $registrationTrend = $this->buildRegistrationTrend(
            $analyticsEventIds,
            $dateFrom,
            $dateTo,
            $granularity
        );

        $attendanceEvents = $user->organizedEvents()
            ->when($selectedEventId, fn ($query) => $query->whereKey($selectedEventId))
            ->withCount([
                'registrations as registrations_count' => fn ($query) => $query->where('status', '!=', 'cancelled'),
                'registrations as checked_in_count' => fn ($query) => $query->where('status', 'checked_in'),
            ])
            ->orderBy('start_date')
            ->get();

        $upcomingEvent = $user->organizedEvents()
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        $events = $user->organizedEvents()
            ->with(['scannerLinks' => function ($query) use ($user) {
                $query->where('organizer_id', $user->getKey())
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->latest();
            }])
            ->withCount([
                'registrations as registrations_count' => fn ($query) => $query->where('status', '!=', 'cancelled'),
                'registrations as checked_in_count' => fn ($query) => $query->where('status', 'checked_in'),
            ])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('organizer.dashboard', compact(
            'allEvents',
            'attendanceEvents',
            'attendanceRate',
            'dateFrom',
            'dateTo',
            'events',
            'granularity',
            'registrationTrend',
            'selectedEventId',
            'totalCheckedIn',
            'totalRegistered',
            'upcomingEvent'
        ));
    }

    private function buildRegistrationTrend(Collection $eventIds, Carbon $dateFrom, Carbon $dateTo, string $granularity): array
    {
        $registrationsByDate = Registration::query()
            ->whereIn('event_id', $eventIds)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
            ->groupBy('activity_date')
            ->orderBy('activity_date')
            ->pluck('total', 'activity_date');

        $trend = [];
        $cursor = $granularity === 'daily' ? $dateFrom->copy() : $dateFrom->copy()->startOfWeek();

        while ($cursor->lte($dateTo)) {
            $bucketStart = $cursor->lt($dateFrom) ? $dateFrom->copy() : $cursor->copy();
            $bucketEnd = $granularity === 'daily' ? $cursor->copy() : $cursor->copy()->endOfWeek();
            if ($bucketEnd->gt($dateTo)) {
                $bucketEnd = $dateTo->copy();
            }

            $registrations = 0;
            $day = $bucketStart->copy();
            while ($day->lte($bucketEnd)) {
                $registrations += (int) $registrationsByDate->get($day->toDateString(), 0);
                $day->addDay();
            }

            $trend[] = [
                'label' => $granularity === 'daily'
                    ? $cursor->format('M j')
                    : $bucketStart->format('M j').'-'.$bucketEnd->format('M j'),
                'registrations' => $registrations,
            ];
            if ($granularity === 'daily') {
                $cursor->addDay();
            } else {
                $cursor->addWeek();
            }
        }

        return $trend;
    }
}
