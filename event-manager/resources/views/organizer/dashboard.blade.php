@extends('layouts.app')

@section('title')
    <h1 class="page-title">Organizer Dashboard</h1>
    <p class="page-subtitle">Manage the events assigned to your organization.</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        <div class="card p-5">
            <p class="text-sm font-medium text-secondary-500">Total Registrations</p>
            <p class="mt-2 text-3xl font-semibold text-secondary-900 tabular-nums">{{ number_format($totalRegistered) }}</p>
            <p class="mt-1 text-sm text-secondary-500">{{ $selectedEventId ? 'Selected event, all time' : 'Across your events' }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-secondary-500">Checked In</p>
            <p class="mt-2 text-3xl font-semibold text-secondary-900 tabular-nums">{{ number_format($totalCheckedIn) }}</p>
            <p class="mt-1 text-sm text-secondary-500">Attendees who have arrived</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-secondary-500">Attendance Rate</p>
            <p class="mt-2 text-3xl font-semibold text-secondary-900 tabular-nums">{{ number_format($attendanceRate, 1) }}%</p>
            <p class="mt-1 text-sm text-secondary-500">Checked in out of registrations</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-secondary-500">Next Event</p>
            @if($upcomingEvent)
                <p class="mt-2 truncate text-lg font-semibold text-secondary-900" title="{{ $upcomingEvent->title }}">{{ $upcomingEvent->title }}</p>
                <p class="mt-1 text-sm text-secondary-500">{{ $upcomingEvent->start_date->format('M d, Y · g:i A') }}</p>
            @else
                <p class="mt-2 text-lg font-semibold text-secondary-900">No upcoming events</p>
                <p class="mt-1 text-sm text-secondary-500">Ask an admin to assign an event.</p>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('organizer.dashboard') }}" class="card mb-6 p-5">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-secondary-900">Analytics filters</h2>
            <p class="text-sm text-secondary-500">Choose an event and date range for the registration trend.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div>
                <label for="event_id" class="label">Event</label>
                <select id="event_id" name="event_id" class="input">
                    <option value="">All your events</option>
                    @foreach($allEvents as $eventOption)
                        <option value="{{ $eventOption->id }}" @selected((string) $selectedEventId === (string) $eventOption->id)>
                            {{ $eventOption->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="label">From</label>
                <input id="date_from" name="date_from" type="date" class="input" value="{{ $dateFrom->format('Y-m-d') }}" required>
            </div>
            <div>
                <label for="date_to" class="label">To</label>
                <input id="date_to" name="date_to" type="date" class="input" value="{{ $dateTo->format('Y-m-d') }}" required>
            </div>
            <div>
                <label for="granularity" class="label">Group by</label>
                <select id="granularity" name="granularity" class="input">
                    <option value="daily" @selected($granularity === 'daily')>Day</option>
                    <option value="weekly" @selected($granularity === 'weekly')>Week</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">Apply filters</button>
                <a href="{{ route('organizer.dashboard') }}" class="btn btn-outline">Reset</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6">
        <section class="card min-w-0 p-5" aria-labelledby="registration-trend-heading">
            <div class="mb-5">
                <h2 id="registration-trend-heading" class="text-lg font-semibold text-secondary-900">Registrations Over Time</h2>
                <p class="text-sm text-secondary-500">New non-cancelled registrations by {{ $granularity === 'daily' ? 'day' : 'week' }}.</p>
            </div>
            @if(count($registrationTrend) && array_sum(array_column($registrationTrend, 'registrations')) > 0)
                @php($maxTrendCount = max(1, max(array_column($registrationTrend, 'registrations'))))
                <div class="flex h-64 min-w-0 items-end gap-2 overflow-x-auto border-b border-l border-secondary-200 px-3 pt-4" role="img" aria-label="Bar chart showing registrations over time">
                    @foreach($registrationTrend as $point)
                        <div class="flex h-full w-12 flex-shrink-0 flex-col items-center justify-end gap-2" title="{{ $point['label'] }}: {{ $point['registrations'] }} registrations">
                            <span class="text-xs font-medium tabular-nums text-secondary-700">{{ $point['registrations'] }}</span>
                            <div class="flex h-40 w-full items-end justify-center">
                                <div class="w-6 rounded-t bg-primary-600" style="height: {{ $point['registrations'] > 0 ? max(3, ($point['registrations'] / $maxTrendCount) * 100) : 0 }}%"></div>
                            </div>
                            <span class="w-full truncate text-center text-xs text-secondary-500">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="rounded-lg bg-secondary-50 p-6 text-center text-sm text-secondary-500">No registrations in this date range.</p>
            @endif
        </section>

        <section class="card min-w-0 p-5" aria-labelledby="check-in-heading">
            <div class="mb-5">
                <h2 id="check-in-heading" class="text-lg font-semibold text-secondary-900">Registrations vs. Check-ins</h2>
                <p class="text-sm text-secondary-500">All-time attendance by event; cancelled registrations are excluded.</p>
            </div>
            @if($attendanceEvents->isNotEmpty())
                @php($maxAttendanceCount = max(1, (int) $attendanceEvents->max('registrations_count')))
                <div class="max-h-64 space-y-4 overflow-y-auto">
                    @foreach($attendanceEvents as $attendanceEvent)
                        <div>
                            <p class="mb-2 truncate text-sm font-medium text-secondary-800" title="{{ $attendanceEvent->title }}">{{ $attendanceEvent->title }}</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-20 flex-shrink-0 text-xs text-secondary-500">Registered</span>
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-secondary-100">
                                        <div class="h-full rounded-full bg-primary-600" style="width: {{ ($attendanceEvent->registrations_count / $maxAttendanceCount) * 100 }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-xs tabular-nums text-secondary-700">{{ $attendanceEvent->registrations_count }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-20 flex-shrink-0 text-xs text-secondary-500">Checked in</span>
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-secondary-100">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ ($attendanceEvent->checked_in_count / $maxAttendanceCount) * 100 }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-xs tabular-nums text-secondary-700">{{ $attendanceEvent->checked_in_count }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-5 border-t border-secondary-100 pt-3 text-xs text-secondary-500">
                    <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-primary-600"></span>Registered</span>
                    <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span>Checked in</span>
                </div>
            @else
                <p class="rounded-lg bg-secondary-50 p-6 text-center text-sm text-secondary-500">No events available for this filter.</p>
            @endif
        </section>
    </div>

    <div class="card animate-in">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-secondary-900">Your Events</h2>
        </div>
        <div class="card-body p-0">
            @if($events->isEmpty())
                <div class="empty-state p-8">
                    <h3 class="empty-state-title">No events assigned yet</h3>
                    <p class="empty-state-description">Ask an admin to assign an event to your organization.</p>
                </div>
            @else
                <p class="px-5 pt-4 text-sm text-secondary-500">
                    Open <strong>Scan tickets</strong> on an event to check in attendees. If it is missing, ask an admin to generate a scanner link.
                </p>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Event</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Registrations</th>
                                <th scope="col">Checked in</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('events.show', $event) }}" class="font-medium text-primary-700 hover:text-primary-800">
                                            {{ $event->title }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap">{{ $event->start_date->format('M d, Y') }}</td>
                                    <td><span class="badge badge-{{ $event->status === 'published' ? 'success' : ($event->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($event->status) }}</span></td>
                                    <td>{{ $event->registrations_count }}</td>
                                    <td>{{ $event->checked_in_count }}</td>
                                    <td class="whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-outline btn-sm">Manage</a>
                                            @if($event->scannerLinks->isNotEmpty())
                                                <a href="{{ route('scanner.scan', ['hash' => $event->scannerLinks->first()->hash]) }}" class="btn btn-primary btn-sm">Scan tickets</a>
                                            @else
                                                <span class="self-center text-xs text-secondary-500">Scanner link needed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
@endsection
