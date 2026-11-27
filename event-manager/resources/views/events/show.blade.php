@extends('layouts.app', ['hideSidebar' => true])

@section('title')
    <h1 class="page-title">{{ $event->title }}</h1>
    <p class="page-subtitle">{{ $event->start_date->format('l, F d, Y') }} · {{ $event->location ?? 'Location TBA' }}</p>
@endsection

@section('header-actions')
    <a href="{{ route('events.index') }}" class="btn btn-ghost" data-history-back>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Back
    </a>

    @if(auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isOrganizer() && $event->organizer_id === auth()->id())))
        <a href="{{ auth()->user()->isOrganizer() ? route('organizer.events.edit', $event) : route('events.edit', $event) }}" class="btn btn-outline">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
    @endif
    
    <div class="hidden sm:flex items-center gap-2">
        <button onclick="shareEvent()" class="btn btn-ghost p-2" aria-label="Share event">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
        </button>
        <button onclick="saveEvent(event)" class="btn btn-ghost p-2" aria-label="Save event">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
        </button>
    </div>
@endsection

@section('content')
    @php
        $registrationsCount = $event->registrations_count ?? $event->registrations()->where('status', '!=', 'cancelled')->count();
        $checkedInCount = $event->checked_in_count ?? 0;
        $percentage = $event->max_attendees ? min(100, round(($registrationsCount / $event->max_attendees) * 100)) : 0;
        $isSoldOut = $event->max_attendees && $registrationsCount >= $event->max_attendees;
        $canRegister = $event->hasAvailableSlots() && $event->start_date->isFuture() && $event->status === 'published';
        $isRegistered = auth()->check() && $event->registrations()->where('user_id', auth()->id())->where('status', '!=', 'cancelled')->exists();
        $daysUntil = now()->diffInDays($event->start_date, false);
        $isUpcoming = $event->start_date->isFuture();
    @endphp

    <!-- Hero Section -->
    <section class="relative -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-8 animate-in">
        <div class="relative aspect-video sm:aspect-[16/9] lg:aspect-[21/9] overflow-hidden bg-secondary-100">
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}"
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-600 via-pink-600 to-orange-500">
                    <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0-1.034-.118-2.037-.337-2.997a14.976 14.976 0 00-6.41-6.41C14.037 4.118 13.034 4 12 4a14.976 14.976 0 00-7.663 1.583C4.118 7.963 4 8.966 4 10a14.976 14.976 0 001.583 7.663 14.976 14.976 0 006.41 6.41C7.963 21.882 8.966 22 10 22a14.976 14.976 0 007.663-1.583c.96-.337 1.963-.455 2.997-.337a14.5 14.5 0 00.017-.002l3.134 3.133a.75.75 0 001.061-1.06l-1.882-1.882a14.5 14.5 0 00-.002-.017z" />
                    </svg>
                </div>
            @endif

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-secondary-900/90 via-secondary-800/40 to-transparent"></div>

            <!-- Subtle pattern overlay for texture -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 36v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 6V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

            <!-- Badges & Info -->
            <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="badge bg-primary-600 text-white px-3 py-1.5 text-sm font-medium">{{ $event->category ?? 'Music Festival' }}</span>
                        <span class="badge px-3 py-1.5 text-sm font-medium
                            @if($event->status === 'published') badge-success
                            @elseif($event->status === 'cancelled') badge-danger
                            @else badge-warning @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                        @if($isSoldOut)
                            <span class="badge badge-danger px-3 py-1.5 text-sm font-medium">Sold Out</span>
                        @elseif($event->is_featured ?? false)
                            <span class="badge bg-warning-500 text-warning-900 px-3 py-1.5 text-sm font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Featured
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 leading-tight drop-shadow-lg">{{ $event->title }}</h1>

                    <div class="flex flex-wrap items-center gap-3 text-white/90 text-sm sm:text-base mt-2">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2">
                            <svg class="w-5 h-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $event->start_date->format('l, F d, Y \a\t g:i A') }} - {{ $event->end_date->format('g:i A') }}</span>
                        </div>
                        @if($event->location)
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2">
                                <svg class="w-5 h-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                                </svg>
                                <span>{{ $event->location }}{{ $event->city ? ', ' . $event->city : '' }}</span>
                            </div>
                        @endif
                        @if($event->city && !$event->location)
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2">
                                <svg class="w-5 h-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 9c0-2.5.5-5 1.5-7.5A8.99 8.99 0 0115 3c2.5 0 5 .5 7.5 1.5a8.99 8.99 0 011.5 7.5c0 2.5-.5 5-1.5 7.5a8.99 8.99 0 01-7.5 1.5 8.99 8.99 0 01-7.5 1.5 8.99 8.99 0 01-1.5-7.5c0-2.5.5-5 1.5-7.5A8.99 8.99 0 0115 3c2.5 0 5 .5 7.5 1.5a8.99 8.99 0 011.5 7.5c0 2.5-.5 5-1.5 7.5a8.99 8.99 0 01-7.5 1.5 8.99 8.99 0 01-1.5-7.5z" />
                                </svg>
                                <span>{{ $event->city }}</span>
                            </div>
                        @endif
                    </div>

                    @if($isUpcoming)
                        <div class="mt-6 pt-6 border-t border-white/20">
                            <div class="grid grid-cols-4 gap-4 sm:gap-6 text-center">
                                <div>
                                    <div id="countdown-days" class="text-2xl sm:text-3xl font-bold text-white">{{ $daysUntil }}</div>
                                    <div class="text-white/70 text-xs uppercase tracking-wider">Days</div>
                                </div>
                                <div>
                                    <div id="countdown-hours" class="text-2xl sm:text-3xl font-bold text-white">00</div>
                                    <div class="text-white/70 text-xs uppercase tracking-wider">Hours</div>
                                </div>
                                <div>
                                    <div id="countdown-minutes" class="text-2xl sm:text-3xl font-bold text-white">00</div>
                                    <div class="text-white/70 text-xs uppercase tracking-wider">Minutes</div>
                                </div>
                                <div>
                                    <div id="countdown-seconds" class="text-2xl sm:text-3xl font-bold text-white">00</div>
                                    <div class="text-white/70 text-xs uppercase tracking-wider">Seconds</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 min-w-0">
            <!-- Main Content -->
            <div class="w-full space-y-6 min-w-0">
                <!-- About Section -->
                <section class="card animate-in overflow-hidden">
                    <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-secondary-900">About This Festival</h2>
                        </div>
                        <span class="badge badge-outline text-sm">{{ $event->category ?? 'Music Festival' }}</span>
                    </div>
                    <div class="card-body">
                        @if($event->description)
                            <div class="prose prose-secondary max-w-none text-secondary-700 leading-relaxed">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-secondary-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                                </svg>
                                <p class="text-secondary-500">No description provided for this festival.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- What to Expect -->
                <section class="card animate-in" style="animation-delay: 0.1s;">
                    <div class="card-header">
                        <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-3">
                            <svg class="w-6 h-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            What to Expect
                        </h2>
                    </div>
                    <div class="card-body">
                        @php
                            $features = [
                                'World-class artists & performers',
                                'Multiple stages & experiences',
                                'Food & beverage vendors',
                                'Merchandise & souvenirs',
                            ];
                            if ($event->category === 'music') $features[] = 'Live band performances';
                            if ($event->category === 'electronic') $features[] = 'Top DJs & visual production';
                            if ($event->category === 'wellness') $features[] = 'Yoga & meditation sessions';
                            if ($event->category === 'food') $features[] = 'Cooking demos & tastings';
                            if ($event->category === 'cultural') $features[] = 'Traditional workshops';
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($features as $feature)
                                <div class="flex items-start gap-3 p-4 rounded-xl bg-secondary-50 hover:bg-secondary-100 transition-colors">
                                    <div class="w-10 h-10 rounded-xl bg-warning-100 text-warning-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </div>
                                    <span class="text-secondary-700">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <!-- Festival Details -->
                <section class="card animate-in" style="animation-delay: 0.15s;">
                    <div class="card-header">
                        <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-3">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Festival Details
                        </h2>
                    </div>
                    <div class="card-body">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2 min-w-0 rounded-xl bg-secondary-50 p-4">
                                <dt class="text-sm font-medium text-secondary-500 mb-2">Date & Time</dt>
                                <dd class="flex flex-wrap items-center gap-3 text-secondary-900">
                                    <div class="flex min-w-0 items-center gap-3 rounded-xl bg-white p-3">
                                        <svg class="w-5 h-5 flex-shrink-0 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="font-medium text-sm sm:text-base">
                                                <span class="hidden sm:inline">{{ $event->start_date->format('l, F d, Y') }}</span>
                                                <span class="sm:hidden">{{ $event->start_date->format('D, M d, Y') }}</span>
                                            </p>
                                            <p class="text-xs sm:text-sm text-secondary-500">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                    @if($daysUntil > 0 && $isUpcoming)
                                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">
                                            {{ $daysUntil }} days left
                                        </span>
                                    @elseif(!$isUpcoming)
                                        <span class="px-3 py-1 bg-secondary-100 text-secondary-700 rounded-full text-sm font-medium">
                                            Event ended
                                        </span>
                                    @endif
                            </dd>
                        </div>

                        <div class="min-w-0 rounded-xl bg-secondary-50 p-4">
                            <dt class="text-sm font-medium text-secondary-500 mb-2">Location</dt>
                            <dd class="flex min-w-0 items-start gap-3 text-secondary-900">
                                <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    @if($event->location)
                                        <p class="font-medium break-words">{{ $event->location }}</p>
                                    @else
                                        <p class="text-secondary-500">TBA</p>
                                    @endif
                                    @if($event->city)
                                        <p class="text-sm text-secondary-500">{{ $event->city }}</p>
                                    @endif
                                </div>
                            </dd>
                        </div>

                        <div class="min-w-0 rounded-xl bg-secondary-50 p-4">
                            <dt class="text-sm font-medium text-secondary-500 mb-2">Capacity</dt>
                            <dd class="flex items-center gap-2 text-secondary-900">
                                <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $registrationsCount }} / {{ $event->max_attendees ?: 'Unlimited' }}</p>
                                    @if($event->max_attendees)
                                        <p class="text-sm text-secondary-500">{{ $percentage }}% filled</p>
                                    @endif
                                </div>
                            </dd>
                        </div>

                        <div class="min-w-0 rounded-xl bg-secondary-50 p-4">
                            <dt class="text-sm font-medium text-secondary-500 mb-2">Status</dt>
                            <dd>
                                <span class="badge px-3 py-1.5 font-medium
                                    @if($event->status === 'published') badge-success
                                    @elseif($event->status === 'cancelled') badge-danger
                                    @else badge-warning @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </dd>
                        </div>

                        <div class="min-w-0 rounded-xl bg-secondary-50 p-4">
                            <dt class="text-sm font-medium text-secondary-500 mb-2">Organizer</dt>
                            <dd class="flex items-center gap-2 text-secondary-900">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center">
                                    <span class="font-medium text-sm">P</span>
                                </div>
                                <span>PestaPora</span>
                            </dd>
                        </div>

                        <div class="min-w-0 rounded-xl bg-secondary-50 p-4">
                            <dt class="text-sm font-medium text-secondary-500 mb-2">Created</dt>
                            <dd class="text-secondary-600">{{ $event->created_at->format('M d, Y \a\t H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

        </div>

    <!-- Registration and sharing -->
    <aside class="w-full min-w-0">
        <div class="space-y-6 animate-in" style="animation-delay: 0.1s;">
            <!-- Registration Card -->
            <div class="card overflow-hidden">
                @if($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}"
                         alt="{{ $event->title }}"
                         class="w-full h-40 object-cover">
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-primary-600 to-pink-600 flex items-center justify-center">
                        <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                        </svg>
                    </div>
                @endif
                <div class="p-6">
                    <h3 class="text-lg font-bold text-secondary-900 mb-2">{{ $event->title }}</h3>
                    <div class="flex items-center gap-2 text-sm text-secondary-500 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>{{ $event->start_date->format('M d, Y') }}</span>
                    </div>

                    @auth
                        @if($canRegister)
                            @unless($isRegistered)
                                <a href="{{ route('registrations.create', $event) }}"
                                   class="btn btn-success w-full justify-center py-4 text-lg font-semibold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                                    </svg>
                                    Register Now
                                </a>
                            @else
                                <a href="{{ route('registrations.index') }}"
                                   class="btn btn-outline w-full justify-center py-4 text-lg font-semibold border-success-300 text-success-700 hover:bg-success-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    Already Registered
                                </a>
                            @endunless
                        @elseif(!$event->start_date->isFuture())
                            <button class="btn btn-secondary w-full justify-center py-4 text-lg font-semibold" disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Event Ended
                            </button>
                        @elseif(!$event->hasAvailableSlots())
                            <button class="btn btn-secondary w-full justify-center py-4 text-lg font-semibold" disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Sold Out
                            </button>
                        @elseif($event->status !== 'published')
                            <button class="btn btn-secondary w-full justify-center py-4 text-lg font-semibold" disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Not Open for Registration
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('registrations.create', $event)) }}"
                           class="btn btn-success w-full justify-center py-4 text-lg font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login to Register
                        </a>
                    @endauth

                </div>
            </div>

            @if($event->max_attendees)
                        <!-- Registration Progress Card -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="text-xl font-bold text-secondary-900 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    Registration Progress
                                </h2>
                            </div>
                            <div class="card-body">
                                <div class="space-y-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-secondary-500">Spots filled</span>
                                        <span class="font-bold text-secondary-900">{{ $percentage }}%</span>
                                    </div>
                                    <div class="h-3 bg-secondary-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-700 ease-out
                                            @if($percentage >= 100) bg-danger-500
                                            @elseif($percentage >= 80) bg-warning-500
                                            @elseif($percentage >= 50) bg-primary-500
                                            @else bg-success-500 @endif"
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3 text-center text-sm">
                                        <div class="p-3 rounded-xl bg-secondary-50">
                                            <p class="text-xl font-bold text-secondary-900">{{ $registrationsCount }}</p>
                                            <p class="text-secondary-500 text-xs">Registered</p>
                                        </div>
                                        <div class="p-3 rounded-xl bg-secondary-50">
                                            <p class="text-xl font-bold text-secondary-900">{{ $checkedInCount }}</p>
                                            <p class="text-secondary-500 text-xs">Checked In</p>
                                        </div>
                                        <div class="p-3 rounded-xl bg-secondary-50">
                                            <p class="text-xl font-bold
                                                @if($event->hasAvailableSlots()) text-success-600
                                                @else text-danger-600 @endif">
                                                {{ $event->hasAvailableSlots() ? $event->max_attendees - $registrationsCount : 0 }}
                                            </p>
                                            <p class="text-secondary-500 text-xs">Available</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            @endif

            <!-- Share Festival Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="font-semibold text-secondary-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        Share Festival
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-3 gap-3">
                        <button onclick="shareToTwitter()" class="btn btn-outline p-3 hover:bg-sky-50 hover:border-sky-300 hover:text-sky-700 transition-colors" aria-label="Share on Twitter">
                            <svg class="w-6 h-6 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </button>
                        <button onclick="shareToFacebook()" class="btn btn-outline p-3 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors" aria-label="Share on Facebook">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.046V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </button>
                        <button onclick="copyLink()" class="btn btn-outline p-3 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-700 transition-colors" aria-label="Copy link">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l4-4a4 4 0 000-5.656z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 14a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l4-4a4 4 0 000-5.656z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>
</div>
@endsection

@section('scripts')
    <script>
        // Countdown Timer
        function updateCountdown() {
            const eventDate = new Date('{{ $event->start_date->format('Y-m-d\TH:i:s') }}').getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance < 0) {
                document.querySelectorAll('[id^="countdown-"]').forEach(el => el.textContent = '00');
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const daysEl = document.getElementById('countdown-days');
            const hoursEl = document.getElementById('countdown-hours');
            const minutesEl = document.getElementById('countdown-minutes');
            const secondsEl = document.getElementById('countdown-seconds');

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Share Functions
        function shareEvent() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $event->title }}',
                    text: 'Check out this amazing festival!',
                    url: window.location.href
                }).catch(console.error);
            } else {
                copyLink();
            }
        }

        function shareToTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('Check out this amazing festival: {{ $event->title }}');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=550,height=420');
        }

        function shareToFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=550,height=420');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                // Show toast
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-6 right-6 bg-secondary-900 text-white px-6 py-3 rounded-xl shadow-xl z-50 animate-in';
                toast.innerHTML = '<flex class="items-center gap-2"><svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>Link copied!</flex>';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }).catch(console.error);
        }

        function saveEvent(event) {
            // Toggle save state
            const btn = event.currentTarget;
            const icon = btn.querySelector('svg');
            const isSaved = btn.classList.contains('text-primary-600');
            
            if (isSaved) {
                btn.classList.remove('text-primary-600');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />';
            } else {
                btn.classList.add('text-primary-600');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />';
            }
        }

        // Add save event listener
        document.addEventListener('DOMContentLoaded', () => {
            const backLink = document.querySelector('[data-history-back]');
            backLink?.addEventListener('click', (event) => {
                const hasSameOriginHistory = window.history.length > 1
                    && document.referrer
                    && new URL(document.referrer).origin === window.location.origin;

                if (
                    event.button !== 0
                    || event.metaKey
                    || event.ctrlKey
                    || event.shiftKey
                    || event.altKey
                    || !hasSameOriginHistory
                ) {
                    return;
                }

                event.preventDefault();
                window.history.back();
            });

        });
    </script>
@endsection