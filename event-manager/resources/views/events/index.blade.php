@extends('layouts.app', ['hideSidebar' => true])

@section('title')
    <h1 class="page-title">All Festivals</h1>
    <p class="page-subtitle">Discover amazing festivals happening near you</p>
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
    <!-- Hero / Search Section -->
    <section class="relative mb-10 animate-in bg-gradient-to-br from-primary-50 via-white to-pink-50 rounded-2xl p-6 sm:p-10 lg:p-14">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%233b82f6" fill-opacity="0.03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 36v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 6V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>

        <div class="relative max-w-3xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-secondary-900 mb-3">Find Your Next Festival</h2>
            <p class="text-secondary-600 mb-6">Search by name, artist, or location</p>

            <form method="GET" action="{{ route('events.index') }}" class="relative max-w-xl mx-auto">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search festivals, artists, locations..."
                           class="input pl-12 pr-4 py-3 text-lg w-full"
                           autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('events.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600" aria-label="Clear search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- Category Pills -->
    <section class="mb-8 animate-in" style="animation-delay: 0.1s;">
        <div class="flex flex-wrap gap-3" role="group" aria-label="Festival categories">
            <a href="{{ route('events.index') }}"
               class="category-pill px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ !request('category') ? 'bg-primary-600 text-white shadow-lg' : 'bg-white text-secondary-600 border border-secondary-200 hover:border-primary-300 hover:text-primary-600' }}">
                All Festivals
            </a>

            @php
                $categories = [
                    ['key' => 'music', 'label' => 'Music Festivals', 'color' => 'primary'],
                    ['key' => 'electronic', 'label' => 'EDM & Electronic', 'color' => 'pink'],
                    ['key' => 'rock', 'label' => 'Rock & Indie', 'color' => 'orange'],
                    ['key' => 'cultural', 'label' => 'Cultural & Arts', 'color' => 'purple'],
                    ['key' => 'food', 'label' => 'Food & Drink', 'color' => 'amber'],
                    ['key' => 'comedy', 'label' => 'Comedy & Shows', 'color' => 'rose'],
                    ['key' => 'wellness', 'label' => 'Wellness & Yoga', 'color' => 'emerald'],
                    ['key' => 'family', 'label' => 'Family & Kids', 'color' => 'blue'],
                ];
            @endphp

            @foreach($categories as $cat)
                <a href="{{ route('events.index', ['category' => $cat['key']]) }}"
                   class="category-pill px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('category') === $cat['key'] ? 'bg-'.$cat['color'].'-600 text-white shadow-lg' : 'bg-white text-secondary-600 border border-secondary-200 hover:border-'.$cat['color'].'-300 hover:text-'.$cat['color'].'-600' }}">
                    {{ $cat['label'] }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Quick Filters Row -->
    @if(request()->hasAny(['location', 'date_filter', 'sort']))
        <div class="flex flex-wrap items-center gap-2 mb-6 animate-in" style="animation-delay: 0.15s;" id="active-filters">
            <span class="text-sm text-secondary-500">Active filters:</span>
            
            @if(request('location'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-secondary-100 text-secondary-700 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                    </svg>
                    {{ ucfirst(request('location')) }}
                    <a href="{{ route('events.index', array_merge(request()->except('location'), ['location' => ''])) }}" class="text-secondary-500 hover:text-secondary-700" aria-label="Remove location filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </span>
            @endif

            @if(request('date_filter'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-secondary-100 text-secondary-700 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }}
                    <a href="{{ route('events.index', array_merge(request()->except('date_filter'), ['date_filter' => ''])) }}" class="text-secondary-500 hover:text-secondary-700" aria-label="Remove date filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </span>
            @endif

            @if(request('sort') && request('sort') !== 'date_asc')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-secondary-100 text-secondary-700 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                    <a href="{{ route('events.index', array_merge(request()->except('sort'), ['sort' => ''])) }}" class="text-secondary-500 hover:text-secondary-700" aria-label="Remove sort filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </span>
            @endif

            <a href="{{ route('events.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                Clear all
            </a>
        </div>
    @endif

    <!-- Results Info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 animate-in" style="animation-delay: 0.2s;">
        <div class="flex items-center gap-3 text-sm text-secondary-600">
            <span>{{ $events->total() }} {{ $events->total() === 1 ? 'festival' : 'festivals' }} found</span>
            @if(request('category'))
                <span class="px-2.5 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-medium">{{ ucfirst(request('category')) }}</span>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <!-- View Toggle -->
            <div class="flex bg-secondary-100 rounded-lg p-1" role="group" aria-label="View mode">
                <a href="{{ route('events.index', array_merge(request()->except('view'), ['view' => 'grid'])) }}"
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ (!request('view') || request('view') === 'grid') ? 'bg-white text-secondary-900 shadow' : 'text-secondary-500 hover:text-secondary-700' }}"
                   aria-label="Grid view"
                   aria-pressed="{{ (!request('view') || request('view') === 'grid') ? 'true' : 'false' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </a>
                <a href="{{ route('events.index', array_merge(request()->except('view'), ['view' => 'list'])) }}"
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('view') === 'list' ? 'bg-white text-secondary-900 shadow' : 'text-secondary-500 hover:text-secondary-700' }}"
                   aria-label="List view"
                   aria-pressed="{{ request('view') === 'list' ? 'true' : 'false' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </a>
            </div>

            <!-- Sort Dropdown -->
            <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-2">
                @foreach(request()->except(['sort', 'view']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="sort" onchange="this.form.submit()" class="input appearance-none bg-white w-auto pr-10" aria-label="Sort festivals">
                    <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Date: Soonest</option>
                    <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Date: Latest</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Added</option>
                </select>
            </form>
        </div>
    </div>

    @if($events->isEmpty())
        <div class="card animate-in py-16 sm:py-24">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="empty-state-title">No festivals found</h2>
                <p class="empty-state-description">Try adjusting your search or filters.</p>
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
        <!-- Grid View -->
        <div id="events-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-in {{ request('view') === 'list' ? 'hidden' : '' }}" style="animation-delay: 0.3s;">
            @foreach($events as $event)
                <article class="card festival-card group overflow-hidden h-full flex flex-col animate-in"
                         style="animation-delay: {{ $loop->index * 30 + 100 }}ms;">
                    <!-- Festival Image -->
                    <div class="relative aspect-video overflow-hidden bg-secondary-100">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-500 to-pink-500">
                                <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                                </svg>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-3 left-3 right-3 flex flex-wrap gap-2">
                            <span class="badge bg-primary-600 text-white">{{ $event->category ?? 'Music Festival' }}</span>
                            <span class="badge
                                @if($event->status === 'published') badge-success
                                @elseif($event->status === 'cancelled') badge-danger
                                @else badge-warning @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                            @php
                                $registrationsCount = $event->registrations_count ?? $event->registrations()->where('status', '!=', 'cancelled')->count();
                                $isSoldOut = $event->max_attendees && $registrationsCount >= $event->max_attendees;
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

                    <!-- Festival Content -->
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
                                        {{ $registrationsCount }} / {{ $event->max_attendees }}
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

        <!-- List View -->
        <div id="events-list" class="hidden space-y-4 animate-in {{ request('view') === 'list' ? '' : 'hidden' }}" style="animation-delay: 0.3s;">
            @foreach($events as $event)
                <article class="card festival-card group flex">
                    <div class="relative w-full sm:w-64 lg:w-72 flex-shrink-0 aspect-video overflow-hidden bg-secondary-100">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
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
        <nav class="mt-10 flex justify-center" aria-label="Pagination">
            <div class="pagination">
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

                @foreach($elements = $events->getUrlRange(1, $events->lastPage()) as $page => $url)
                    @if($page === '...')
                        <span class="pagination-ellipsis" aria-hidden="true">...</span>
                    @elseif($page === $events->currentPage())
                        <span class="pagination-link active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-link" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach

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

        document.addEventListener('DOMContentLoaded', () => {
            const viewMode = '{{ request('view', 'grid') }}';
            if (viewMode === 'list') {
                changeView('list');
            }
        });
    </script>
@endsection