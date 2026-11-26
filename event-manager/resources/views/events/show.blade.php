@extends('layouts.app')

@section('title')
    <h1 class="page-title">{{ $event->title }}</h1>
    <p class="page-subtitle">{{ $event->start_date->format('l, F d, Y') }} · {{ $event->location ?? 'Location TBA' }}</p>
@endsection

@section('header-actions')
    <a href="{{ route('events.index') }}" class="btn btn-ghost">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Back
    </a>

    @can('isAdmin')
        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.event.scanners', $event) }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Scanners
        </a>
    @endcan
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Image -->
            @if($event->image)
                <div class="card overflow-hidden animate-in">
                    <img src="{{ asset('storage/' . $event->image) }}"
                         alt="{{ $event->title }}"
                         class="w-full h-64 sm:h-80 object-cover">
                    <div class="absolute top-4 left-4 right-4 flex justify-between">
                        <span class="badge
                            @if($event->status === 'published') badge-success
                            @elseif($event->status === 'cancelled') badge-danger
                            @else badge-warning @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                        @if($event->max_attendees && !$event->hasAvailableSlots())
                            <span class="badge badge-danger">Full</span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div class="card animate-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-secondary-900">About this Event</h2>
                </div>
                <div class="card-body">
                    @if($event->description)
                        <div class="prose prose-secondary max-w-none text-secondary-700">
                            {{ nl2br(e($event->description)) }}
                        </div>
                    @else
                        <p class="text-secondary-500">No description provided for this event.</p>
                    @endif
                </div>
            </div>

            <!-- Event Details -->
            <div class="card animate-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-secondary-900">Event Details</h2>
                </div>
                <div class="card-body">
                    <dl class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-secondary-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="badge
                                        @if($event->status === 'published') badge-success
                                        @elseif($event->status === 'cancelled') badge-danger
                                        @else badge-warning @endif">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-secondary-500">Start Date</dt>
                                <dd class="mt-1 text-secondary-900">{{ $event->start_date->format('l, F d, Y \a\t g:i A') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-secondary-500">End Date</dt>
                                <dd class="mt-1 text-secondary-900">{{ $event->end_date->format('l, F d, Y \a\t g:i A') }}</dd>
                            </div>

                            @if($event->location)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-secondary-500">Location</dt>
                                    <dd class="mt-1 text-secondary-900 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                                        </svg>
                                        {{ $event->location }}
                                    </dd>
                                </div>
                            @endif

                            @if($event->max_attendees)
                                <div>
                                    <dt class="text-sm font-medium text-secondary-500">Capacity</dt>
                                    <dd class="mt-1 text-secondary-900">
                                        {{ $event->registrations()->where('status', '!=', 'cancelled')->count() }} / {{ $event->max_attendees }}
                                        @if($event->max_attendees > 0)
                                            <span class="text-secondary-500 ml-2">({{ round(($event->registrations()->where('status', '!=', 'cancelled')->count() / $event->max_attendees) * 100) }}%)</span>
                                        @endif
                                    </dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-secondary-500">Created</dt>
                                <dd class="mt-1 text-secondary-600">{{ $event->created_at->format('M d, Y H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-secondary-500">Last Updated</dt>
                                <dd class="mt-1 text-secondary-600">{{ $event->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </div>

                        @if($event->max_attendees && $event->max_attendees > 0)
                            <div class="pt-4 border-t border-secondary-100">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-secondary-500">Registration Progress</span>
                                    <span class="font-medium text-secondary-900">{{ round(($event->registrations()->where('status', '!=', 'cancelled')->count() / $event->max_attendees) * 100) }}%</span>
                                </div>
                                <div class="h-3 bg-secondary-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        @if(($event->registrations()->where('status', '!=', 'cancelled')->count() / $event->max_attendees) >= 1) bg-danger-500
                                        @elseif(($event->registrations()->where('status', '!=', 'cancelled')->count() / $event->max_attendees) >= 0.8) bg-warning-500
                                        @else bg-primary-500 @endif"
                                         style="width: {{ min(100, round(($event->registrations()->where('status', '!=', 'cancelled')->count() / $event->max_attendees) * 100)) }}%"></div>
                                </div>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1">
            <div class="card sticky top-24 animate-in" style="animation-delay: 0.15s;">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-secondary-900">Actions</h2>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        @auth
                            @if($event->hasAvailableSlots() && $event->start_date->isFuture() && $event->status === 'published')
                                @unless($event->registrations()->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->exists())
                                    <a href="{{ route('registrations.create', $event) }}"
                                       class="btn btn-success w-full justify-center py-3 text-base">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                                        </svg>
                                        Register for Event
                                    </a>
                                @else
                                    <a href="{{ route('registrations.index') }}"
                                       class="btn btn-outline w-full justify-center py-3 text-base">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                        Already Registered
                                    </a>
                                @endunless
                            @elseif(!$event->start_date->isFuture())
                                <button class="btn btn-secondary w-full justify-center py-3 text-base" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Event Ended
                                </button>
                            @elseif(!$event->hasAvailableSlots())
                                <button class="btn btn-secondary w-full justify-center py-3 text-base" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Fully Booked
                                </button>
                            @elseif($event->status !== 'published')
                                <button class="btn btn-secondary w-full justify-center py-3 text-base" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Not Open for Registration
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(route('registrations.create', $event)) }}"
                               class="btn btn-success w-full justify-center py-3 text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Login to Register
                            </a>
                        @endauth

                        @can('isAdmin')
                            <hr class="border-secondary-200">

                            <a href="{{ route('events.edit', $event) }}"
                               class="btn btn-primary w-full justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit Event
                            </a>

                            <a href="{{ route('admin.event.scanners', $event) }}"
                               class="btn btn-outline w-full justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Manage Scanners
                            </a>

                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger w-full justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Delete Event
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Quick Info Card -->
            @if($event->max_attendees)
                <div class="card mt-6 animate-in" style="animation-delay: 0.2s;">
                    <div class="card-body">
                        <h3 class="font-semibold text-secondary-900 mb-4">Quick Info</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-secondary-500">Registered</dt>
                                <dd class="font-medium text-secondary-900">{{ $event->registrations()->where('status', '!=', 'cancelled')->count() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-secondary-500">Checked In</dt>
                                <dd class="font-medium text-secondary-900">{{ $event->checked_in_count }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-secondary-500">Available Slots</dt>
                                <dd class="font-medium
                                    @if($event->hasAvailableSlots()) text-success-600
                                    @else text-danger-600 @endif">
                                    {{ $event->hasAvailableSlots() ? $event->max_attendees - $event->registrations()->where('status', '!=', 'cancelled')->count() : 0 }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection