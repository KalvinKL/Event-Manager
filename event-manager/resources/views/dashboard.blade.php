@extends('layouts.app')

@section('title')
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ Auth::user()->name }}! Here's your event overview.</p>
@endsection

@section('header-actions')
    <a href="{{ route('events.index') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Browse Events
    </a>
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 animate-in">
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-icon-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">My Registrations</p>
                    <p class="stat-value">{{ $registrationsCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-icon-success">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Events Attended</p>
                    <p class="stat-value">{{ $checkedInCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-icon-info">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Upcoming Events</p>
                    <p class="stat-value">{{ $upcomingEventsCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Registrations -->
    <div class="card animate-in" style="animation-delay: 0.1s;">
        <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-secondary-900">Upcoming Events You're Registered For</h2>
                <p class="text-sm text-secondary-500">Showing your confirmed registrations</p>
            </div>
            <a href="{{ route('registrations.index') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                </svg>
                View All
            </a>
        </div>

        <div class="card-body p-0">
            @if($upcomingRegistrations->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="empty-state-title">No upcoming registrations</h3>
                    <p class="empty-state-description">Browse events and register for ones you'd like to attend.</p>
                    <div class="empty-state-action">
                        <a href="{{ route('events.index') }}" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Browse Events
                        </a>
                    </div>
                </div>
            @else
                <div class="table-container">
                    <table class="table" role="table">
                        <thead>
                            <tr>
                                <th scope="col">Event</th>
                                <th scope="col">Date & Time</th>
                                <th scope="col">Location</th>
                                <th scope="col">Status</th>
                                <th scope="col">Barcode</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingRegistrations as $registration)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if($registration->event->image)
                                                <img src="{{ asset('storage/' . $registration->event->image) }}"
                                                     alt=""
                                                     class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-secondary-900">{{ $registration->event->title }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary-700 whitespace-nowrap">
                                        {{ $registration->event->start_date->format('M d, Y \a\t g:i A') }}
                                    </td>
                                    <td class="text-secondary-600">
                                        {{ $registration->event->location ?? '<span class="text-secondary-400">TBA</span>' }}
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($registration->isCheckedIn()) badge-success
                                            @else badge-primary @endif">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="barcode-display text-sm font-mono bg-secondary-100 px-2 py-1 rounded">{{ $registration->barcode }}</code>
                                    </td>
                                    <td>
                                        <a href="{{ route('registrations.show', $registration) }}"
                                           class="btn btn-ghost btn-icon-sm"
                                           aria-label="View registration details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection