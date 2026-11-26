@extends('layouts.app')

@section('title')
    <h1 class="page-title">All Festivals</h1>
    <p class="page-subtitle">Discover and filter amazing festivals</p>
@endsection

@section('header-actions')
    @can('isAdmin')
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Festival
        </a>
    @endcan
@endsection

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-6 animate-in">
        <div class="card-body">
            <form method="GET" action="{{ route('events.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-4">
                <!-- Search -->
                <div class="md:flex-1">
                    <label for="search" class="sr-only">Search festivals</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search festivals, artists, locations..."
                               class="input pl-10 pr-4"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="md:w-48">
                    <label for="category" class="sr-only">Category</label>
                    <select id="category" name="category" class="input appearance-none bg-white">
                        <option value="">All Categories</option>
                        @foreach([
                            'music' => 'Music Festivals',
                            'electronic' => 'EDM & Electronic',
                            'rock' => 'Rock & Indie',
                            'cultural' => 'Cultural & Arts',
                            'food' => 'Food & Drink',
                            'comedy' => 'Comedy & Shows',
                            'wellness' => 'Wellness & Yoga',
                            'family' => 'Family & Kids',
                        ] as $key => $label)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="md:w-48">
                    <label for="location" class="sr-only">Location</label>
                    <select id="location" name="location" class="input appearance-none bg-white">
                        <option value="">All Locations</option>
                        <option value="jakarta" {{ request('location') === 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="bali" {{ request('location') === 'bali' ? 'selected' : '' }}>Bali</option>
                        <option value="bandung" {{ request('location') === 'bandung' ? 'selected' : '' }}>Bandung</option>
                        <option value="surabaya" {{ request('location') === 'surabaya' ? 'selected' : '' }}>Surabaya</option>
                        <option value="yogyakarta" {{ request('location') === 'yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="md:w-40">
                    <label for="date_filter" class="sr-only">Date</label>
                    <select id="date_filter" name="date_filter" class="input appearance-none bg-white">
                        <option value="">All Dates</option>
                        <option value="upcoming" {{ request('date_filter') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="md:w-40">
                    <label for="sort" class="sr-only">Sort</label>
                    <select id="sort" name="sort" class="input appearance-none bg-white">
                        <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Date: Soonest</option>
                        <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Date: Latest</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Added</option>
                    </select>
                </div>

                <!-- Submit/Clear -->
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'location', 'date_filter', 'sort']))
                        <a href="{{ route('events.index') }}" class="btn btn-outline whitespace-nowrap">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 animate-in" style="animation-delay: 0.1s;">
        <div class="flex items-center gap-3 text-sm text-secondary-600">
            <span>{{ $events->total() }} festivals found</span>
            @if(request('category'))
                <span class="badge badge-primary">{{ ucfirst(request('category')) }}</span>
            @endif
            @if(request('location'))
                <span class="badge badge-outline">{{ ucfirst(request('location')) }}</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <label for="view_mode" class="sr-only">View mode</label>
            <select id="view_mode" class="input appearance-none bg-white w-auto" onchange="changeView(this.value)">
                <option value="grid" {{ !request('view') || request('view') === 'grid' ? 'selected' : '' }}>Grid</option>
                <option value="list" {{ request('view') === 'list' ? 'selected' : '' }}>List</option>
            </select>
        </div>
    </div>

    @if($events->isEmpty())
        <div class="card animate-in">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="empty-state-title">No festivals found</h2>
                <p class="empty-state-description">Try adjusting your filters or search terms.</p>
                <div class="empty-state-action">
                    <a href="{{ route('events.index') }}" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </div>
        </div>
    @else
        <div id="events-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-in" style="animation-delay: 0.2s;">
            @foreach($events as $event)
                <article class="card group overflow-hidden h-full flex flex-col">
                    <!-- Event Image -->
                    <div class="relative aspect-video overflow-hidden bg-secondary-100">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-500 to-pink-500">
                                <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                                </svg>
                            </div>
                        @endif

                        <div class="absolute top-3 left-3 right-3 flex flex-wrap gap-2">
                            <span class="badge bg-primary-600 text-white">{{ $event->category ?? 'Music Festival' }}</span>
                            <span class="badge
                                @if($event->status === 'published') badge-success
                                @elseif($event->status === 'cancelled') badge-danger
                                @else badge-warning @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                            @php
                                $isSoldOut = $event->max_attendees && ($event->registrations_count ?? $event->registrations()->where('status', '!=', 'cancelled')->count()) >= $event->max_attendees;
                            @endphp
                            @if($isSoldOut)
                                <span class="badge badge-danger">Sold Out</span>
                            @endif
                        </div>

                        @if($event->is_featured ?? false)
                            <div class="absolute top-3 right-3">
                                <span class="badge bg-warning-500 text-warning-900 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Featured
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Event Content -->
                    <div class="card-body flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="badge badge-outline text-xs">{{ $event->city ?? 'Jakarta' }}</span>
                            <span class="badge badge-outline text-xs">{{ $event->start_date->format('M Y') }}</span>
                        </div>

                        <h3 class="text-lg font-semibold text-secondary-900 line-clamp-2 group-hover:text-primary-600 transition-colors">
                            {{ $event->title }}
                        </h3>

                        @if($event->description)
                            <p class="mt-2 text-sm text-secondary-500 line-clamp-3 flex-1">
                                {{ Str::limit($event->description, 120) }}
                            </p>
                        @endif

                        <dl class="mt-4 space-y-2.5 text-sm">
                            <div class="flex items-center gap-2 text-secondary-600">
                                <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span>{{ $event->start_date->format('D, M d, Y \a\t g:i A') }}</span>
                            </div>

                            @if($event->location)
                                <div class="flex items-center gap-2 text-secondary-600">
                                    <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                                    </svg>
                                    <span class="truncate">{{ $event->location }}</span>
                                </div>
                            @endif

                            @if($event->max_attendees)
                                <div class="flex items-center gap-2 text-secondary-600">
                                    <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                    <span>
                                        {{ $event->registrations_count ?? $event->registrations()->where('status', '!=', 'cancelled')->count() }} / {{ $event->max_attendees }}
                                    </span>
                                </div>
                            @endif
                        </dl>

                        <!-- Actions -->
                        <div class="mt-4 pt-4 border-t border-secondary-100 flex gap-2">
                            <a href="{{ route('events.show', $event->id) }}"
                               class="btn btn-primary flex-1 justify-center">
                                View Details
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>

                            @auth
                                @php
                                    $canRegister = $event->hasAvailableSlots() && $event->start_date->isFuture() && $event->status === 'published';
                                    $isRegistered = $event->registrations()->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->exists();
                                @endphp
                                @if($canRegister)
                                    @unless($isRegistered)
                                        <a href="{{ route('registrations.create', $event) }}"
                                           class="btn btn-success flex-1 justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                                            </svg>
                                            Register
                                        </a>
                                    @else
                                        <a href="{{ route('registrations.index') }}"
                                           class="btn btn-outline flex-1 justify-center border-success-300 text-success-700 hover:bg-success-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                            </svg>
                                            Registered
                                        </a>
                                    @endunless
                                @elseif(!$event->start_date->isFuture())
                                    <button class="btn btn-secondary flex-1 justify-center" disabled>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Ended
                                    </button>
                                @elseif(!$event->hasAvailableSlots())
                                    <button class="btn btn-secondary flex-1 justify-center" disabled>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Sold Out
                                    </button>
                                @elseif($event->status !== 'published')
                                    <button class="btn btn-secondary flex-1 justify-center" disabled>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Not Open
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ urlencode(route('registrations.create', $event)) }}"
                                   class="btn btn-success flex-1 justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Login to Register
                                </a>
                            @endauth

                            @can('isAdmin')
                                <a href="{{ route('events.edit', $event) }}"
                                   class="btn btn-ghost p-2"
                                   aria-label="Edit festival">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this festival? This action cannot be undone.')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-ghost p-2 text-danger-600 hover:bg-danger-50"
                                            aria-label="Delete festival">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- List View (Hidden by default) -->
        <div id="events-list" class="hidden space-y-4 animate-in" style="animation-delay: 0.2s;">
            @foreach($events as $event)
                <article class="card group flex">
                    <div class="relative w-full sm:w-64 lg:w-72 flex-shrink-0 aspect-video overflow-hidden bg-secondary-100">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-500 to-pink-500">
                                <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 right-3 flex flex-wrap gap-2">
                            <span class="badge bg-primary-600 text-white">{{ $event->category ?? 'Music Festival' }}</span>
                            <span class="badge
                                @if($event->status === 'published') badge-success
                                @elseif($event->status === 'cancelled') badge-danger
                                @else badge-warning @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body flex flex-col flex-1 justify-between p-6">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="badge badge-outline text-xs">{{ $event->city ?? 'Jakarta' }}</span>
                                <span class="badge badge-outline text-xs">{{ $event->start_date->format('M Y') }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-secondary-900 group-hover:text-primary-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            @if($event->description)
                                <p class="mt-2 text-secondary-500 line-clamp-2">{{ Str::limit($event->description, 150) }}</p>
                            @endif
                            <dl class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center gap-2 text-secondary-600">
                                    <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <span>{{ $event->start_date->format('D, M d, Y \a\t g:i A') }}</span>
                                </div>
                                @if($event->location)
                                    <div class="flex items-center gap-2 text-secondary-600">
                                        <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                                        </svg>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                @endif
                            </dl>
                        </div>
                        <div class="mt-4 pt-4 border-t border-secondary-100 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('events.show', $event->id) }}"
                               class="btn btn-primary flex-1 justify-center">
                                View Details
                            </a>
                            @auth
                                @php
                                    $canRegister = $event->hasAvailableSlots() && $event->start_date->isFuture() && $event->status === 'published';
                                    $isRegistered = $event->registrations()->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->exists();
                                @endphp
                                @if($canRegister)
                                    @unless($isRegistered)
                                        <a href="{{ route('registrations.create', $event) }}"
                                           class="btn btn-success flex-1 justify-center">
                                            Register
                                        </a>
                                    @else
                                        <a href="{{ route('registrations.index') }}"
                                           class="btn btn-outline flex-1 justify-center border-success-300 text-success-700 hover:bg-success-50">
                                            Registered
                                        </a>
                                    @endunless
                                @else
                                    <button class="btn btn-secondary flex-1 justify-center" disabled>
                                        @if(!$event->start_date->isFuture()) Ended
                                        @elseif(!$event->hasAvailableSlots()) Sold Out
                                        @else Not Open @endif
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ urlencode(route('registrations.create', $event)) }}"
                                   class="btn btn-success flex-1 justify-center">
                                    Login to Register
                                </a>
                            @endauth
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    @if($events->hasPages())
        <nav class="mt-8 flex justify-center" aria-label="Pagination">
            <div class="pagination">
                {{-- Previous --}}
                @if($events->onFirstPage())
                    <span class="pagination-link opacity-50 cursor-not-allowed" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $events->previousPageUrl() }}" class="pagination-link" rel="prev" aria-label="Previous page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($elements = $events->getUrlRange(1, $events->lastPage()) as $page => $url)
                    @if($page === '...')
                        <span class="pagination-ellipsis" aria-hidden="true">...</span>
                    @elseif($page === $events->currentPage())
                        <span class="pagination-link active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-link" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($events->onLastPage())
                    <span class="pagination-link opacity-50 cursor-not-allowed" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $events->nextPageUrl() }}" class="pagination-link" rel="next" aria-label="Next page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                @endif
            </div>
        </nav>
    @endif
@endsection

@section('scripts')
    <script>
        function changeView(mode) {
            const grid = document.getElementById('events-grid');
            const list = document.getElementById('events-list');
            const url = new URL(window.location.href);
            url.searchParams.set('view', mode);
            window.history.replaceState({}, '', url);

            if (mode === 'list') {
                grid.classList.add('hidden');
                list.classList.remove('hidden');
            } else {
                grid.classList.remove('hidden');
                list.classList.add('hidden');
            }
        }

        // Initialize view on load
        document.addEventListener('DOMContentLoaded', () => {
            const viewMode = '{{ request('view', 'grid') }}';
            if (viewMode === 'list') {
                changeView('list');
            }
        });
    </script>
@endsection