@extends('layouts.app', ['hideSidebar' => true])

@section('title')
    <a href="{{ route('home') }}" aria-label="PestaPora">
        <img src="{{ asset('images/pestapora-logo.svg') }}" alt="PestaPora" class="h-12 w-auto">
    </a>
    <p class="page-subtitle">Discover Amazing Music Festivals & Entertainment Events</p>
@endsection

@section('header-actions')
    <!-- Navbar with auth buttons on far right -->
    <nav class="hidden md:flex items-center justify-between w-full" aria-label="Main navigation">
        <!-- Left: Nav links -->
        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors" aria-current="page">
                Home
            </a>
            <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                All Events
            </a>
            
            <!-- Categories Dropdown -->
            <div class="relative" id="categories-dropdown">
                <button type="button"
                        class="px-3 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors flex items-center gap-1.5"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="categories-menu"
                        id="categories-trigger">
                    Browse by Category
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="categories-menu"
                     class="absolute left-1/2 -translate-x-1/2 top-full mt-3 w-56 bg-white rounded-xl shadow-xl border border-secondary-200 opacity-0 invisible transform translate-y-2 transition-all duration-200 z-50"
                     role="menu"
                     aria-orientation="vertical">
                    <div class="p-2">
                        @php
                            $categories = [
                                ['key' => 'music', 'label' => 'Music Festivals', 'icon' => 'music', 'color' => 'primary'],
                                ['key' => 'electronic', 'label' => 'EDM & Electronic', 'icon' => 'electronic', 'color' => 'primary'],
                                ['key' => 'rock', 'label' => 'Rock & Indie', 'icon' => 'rock', 'color' => 'primary'],
                                ['key' => 'cultural', 'label' => 'Cultural & Arts', 'icon' => 'cultural', 'color' => 'primary'],
                                ['key' => 'food', 'label' => 'Food & Drink', 'icon' => 'food', 'color' => 'primary'],
                                ['key' => 'comedy', 'label' => 'Comedy & Shows', 'icon' => 'comedy', 'color' => 'primary'],
                                ['key' => 'wellness', 'label' => 'Wellness & Yoga', 'icon' => 'wellness', 'color' => 'primary'],
                                ['key' => 'family', 'label' => 'Family & Kids', 'icon' => 'family', 'color' => 'primary'],
                            ];
                        @endphp
                        @foreach($categories as $cat)
                            <a href="{{ route('events.index', ['category' => $cat['key']]) }}"
                               class="block px-4 py-2.5 rounded-lg text-secondary-700 hover:bg-{{ $cat['color'] }}-50 hover:text-{{ $cat['color'] }}-600 transition-colors flex items-center gap-3"
                               role="menuitem">
                                <div class="w-8 h-8 rounded-lg bg-{{ $cat['color'] }}-100 text-{{ $cat['color'] }}-600 flex items-center justify-center flex-shrink-0">
                                    @php
                                        $icons = [
                                            'music' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />',
                                            'electronic' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />',
                                            'rock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0-1.034-.118-2.037-.337-2.997a14.976 14.976 0 00-6.41-6.41C14.037 4.118 13.034 4 12 4a14.976 14.976 0 00-7.663 1.583C4.118 7.963 4 8.966 4 10a14.976 14.976 0 001.583 7.663 14.976 14.976 0 006.41 6.41C7.963 21.882 8.966 22 10 22a14.976 14.976 0 007.663-1.583c.96-.337 1.963-.455 2.997-.337a14.5 14.5 0 00.017-.002l3.134 3.133a.75.75 0 001.061-1.06l-1.882-1.882a14.5 14.5 0 00-.002-.017z" />',
                                            'cultural' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.21 15.21A10.036 10.036 0 019.21 13.21c.002 1.328-.082 2.589-.306 3.816.28.509.586.985.92 1.425.296.394.613.756.949 1.093.356.357.735.699 1.131 1.021.395.323.81.617 1.239.89.428.272.875.514 1.34.734.54.253 1.098.485 1.678.701.598.223 1.212.42 1.842.591.63.171 1.275.32 1.923.451.648.131 1.301.248 1.955.356.654.108 1.314.207 1.977.297.663.09 1.33.168 2 .238.67.07 1.342.13 2.014.18.672.05 1.343.086 2.015.11.67.024 1.34-.002 2.008-.03.667-.033 1.333-.079 2-.135.667-.056 1.333-.123 1.998-.204.665-.08 1.328-.171 1.99-.276.662-.105 1.322-.224 1.98-.35.657-.126 1.312-.256 1.97-.396.653-.14 1.304-.299 1.95-.46.65-.162 1.297-.347 1.94-.54.646-.192 1.29-.416 1.93-.63.642-.214 1.283-.5 1.92-.6.639-.1 1.276-.62 1.91-.75" />',
                                            'food' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292 1.591-1.425 3.695-2.292 6-2.292a8.987 8.987 0 016-2.25v-14.25A8.966 8.966 0 0012 6.042z" />',
                                            'comedy' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7V9.75a8.967 8.967 0 01-2.314 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a23.548 23.548 0 00-5.714 0" />',
                                            'wellness' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 10.5H18.75v.75c0 2.485-2.099 4.5-4.688 4.5-1.935 0-3.597-1.126-4.312-2.733A13.475 13.475 0 013.248 12c0-1.13.083-2.252.245-3.374.166-1.152.438-2.282.815-3.374h6.081A4.5 4.5 0 0118 6v.75m0 0H10.5a2.25 2.25 0 000 4.5h2.25m4.5 0H19.5a2.25 2.25 0 000-4.5h-2.25M3.75 21h16.5" />',
                                            'family' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />',
                                        ];
                                    @endphp
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        {!! $icons[$cat['icon']] !!}
                                    </svg>
                                </div>
                                <span>{{ $cat['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="#about" class="px-3 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                About
            </a>
        </div>

        <!-- Right: Auth buttons -->
        <div class="flex items-center gap-3">
            @auth
                @if(Auth::user()->isAttendee())
                    <a href="{{ route('registrations.index') }}" class="px-4 py-2 rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 font-semibold transition-colors">
                        My Registrations
                    </a>
                @else
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('organizer.dashboard') }}" class="px-4 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                        Dashboard
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Get Started
                </a>
            @endauth
        </div>
    </nav>

    <!-- Mobile menu button -->
    <div class="md:hidden ml-auto">
        <button id="mobile-menu-btn" class="p-2 rounded-lg text-secondary-500 hover:text-secondary-700 hover:bg-secondary-100 transition-colors" aria-label="Toggle menu" aria-expanded="false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</section>

<!-- Mobile Menu -->
<div id="mobile-menu" class="md:hidden hidden fixed inset-0 z-50 bg-white animate-in">
    <div class="flex flex-col items-center justify-center h-full space-y-6">
        <a href="{{ route('home') }}" class="text-xl font-medium text-secondary-700 hover:text-primary-600">Home</a>
        <a href="{{ route('events.index') }}" class="text-xl font-medium text-secondary-700 hover:text-primary-600">All Events</a>
        <a href="{{ route('categories.index') }}" class="text-xl font-medium text-secondary-700 hover:text-primary-600">Browse by Category</a>
        <a href="#about" class="text-xl font-medium text-secondary-700 hover:text-primary-600">About</a>
        @auth
            @if(Auth::user()->isAttendee())
                <a href="{{ route('registrations.index') }}" class="btn btn-primary text-xl px-8 py-3">My Registrations</a>
            @else
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('organizer.dashboard') }}" class="btn btn-primary text-xl px-8 py-3">Dashboard</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline text-xl px-8 py-3">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline text-xl px-8 py-3">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-primary text-xl px-8 py-3">Get Started</a>
        @endauth
    </div>
</div>

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden animate-in">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary-600/10 via-transparent to-primary-600/10"></div>
        <div class="pointer-events-none absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%233b82f6" fill-opacity="0.03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 36v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 6V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-100 text-primary-700 border border-primary-200 text-sm font-medium mb-6 animate-in" style="animation-delay: 0.1s;">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                </span>
                <span>New festivals added weekly</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-secondary-900 tracking-tight mb-6 animate-in" style="animation-delay: 0.2s;">
                Discover <span class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">Music Festivals</span> & <span class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">Entertainment</span>
            </h1>

            <p class="text-lg sm:text-xl text-secondary-600 max-w-2xl mx-auto mb-10 animate-in" style="animation-delay: 0.3s;">
                From electrifying music festivals to unforgettable entertainment experiences. Join thousands of festival-goers discovering their next adventure on PestaPora.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-in" style="animation-delay: 0.4s;">
                <a href="{{ route('events.index') }}" class="btn btn-primary btn-lg w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Explore Festivals
                </a>
                @guest
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg w-full sm:w-auto border-primary-300 text-primary-700 hover:bg-primary-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Join Free
                </a>
                @else
                    @if(Auth::user()->isAttendee())
                    <a href="{{ route('registrations.index') }}" class="btn btn-outline btn-lg w-full sm:w-auto border-primary-300 text-primary-700 hover:bg-primary-50">
                        My Registrations
                    </a>
                    @endif
                @endguest
            </div>

            <!-- Trust Indicators -->
            <div class="mt-16 flex flex-wrap items-center justify-center gap-8 text-secondary-500 animate-in" style="animation-delay: 0.5s;">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium text-secondary-700">200+</span>
                    <span class="text-sm">Festivals</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <span class="text-sm font-medium text-secondary-700">150K+</span>
                    <span class="text-sm">Attendees</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-secondary-700">50+</span>
                    <span class="text-sm">Cities</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Festivals -->
    <section class="py-16 sm:py-20 lg:py-24 animate-in" style="animation-delay: 0.6s;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-secondary-900">Featured Festivals</h2>
                    <p class="text-secondary-500 mt-1">Handpicked festivals you won't want to miss</p>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-outline">
                    View All Festivals
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredEvents->take(3) as $event)
                    <article class="card group overflow-hidden h-full flex flex-col animate-in" style="animation-delay: {{ $loop->index * 100 + 100 }}ms;">
                        <div class="relative aspect-video overflow-hidden bg-secondary-100">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}"
                                     alt="{{ $event->title }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-600 to-primary-700">
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
                                    $registrationsCount = $event->registrations_count ?? 0;
                                    $isSoldOut = $event->max_attendees && $registrationsCount >= $event->max_attendees;
                                @endphp
                                @if($isSoldOut)
                                    <span class="badge badge-danger">Sold Out</span>
                                @endif
                            </div>
                        </div>

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
                            </dl>

                            <div class="mt-4 pt-4 border-t border-secondary-100">
                                <a href="{{ route('events.show', $event->id) }}"
                                   class="btn btn-primary w-full justify-center">
                                    View Details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-16 sm:py-20 lg:py-24 bg-secondary-100 animate-in" style="animation-delay: 0.7s;" id="categories">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl font-bold text-secondary-900">Explore by Category</h2>
                <p class="text-secondary-500 mt-3 max-w-2xl mx-auto">Find your perfect festival experience</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['icon' => 'music', 'label' => 'Music Festivals', 'color' => 'primary', 'count' => '85'],
                    ['icon' => 'electronic', 'label' => 'EDM & Electronic', 'color' => 'primary', 'count' => '42'],
                    ['icon' => 'rock', 'label' => 'Rock & Indie', 'color' => 'primary', 'count' => '38'],
                    ['icon' => 'cultural', 'label' => 'Cultural & Arts', 'color' => 'primary', 'count' => '25'],
                    ['icon' => 'food', 'label' => 'Food & Drink', 'color' => 'primary', 'count' => '18'],
                    ['icon' => 'comedy', 'label' => 'Comedy & Shows', 'color' => 'primary', 'count' => '12'],
                    ['icon' => 'wellness', 'label' => 'Wellness & Yoga', 'color' => 'primary', 'count' => '15'],
                    ['icon' => 'family', 'label' => 'Family & Kids', 'color' => 'primary', 'count' => '22'],
                ] as $cat)
                    <a href="{{ route('events.index') }}?category={{ Str::slug($cat['label']) }}"
                       class="card p-6 text-center hover:shadow-lg hover:border-{{ $cat['color'] }}-300 transition-all group">
                        <div class="w-14 h-14 rounded-2xl bg-{{ $cat['color'] }}-100 text-{{ $cat['color'] }}-600 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            @php
                                $icons = [
                                    'music' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />',
                                    'electronic' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />',
                                    'rock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0-1.034-.118-2.037-.337-2.997a14.976 14.976 0 00-6.41-6.41C14.037 4.118 13.034 4 12 4a14.976 14.976 0 00-7.663 1.583C4.118 7.963 4 8.966 4 10a14.976 14.976 0 001.583 7.663 14.976 14.976 0 006.41 6.41C7.963 21.882 8.966 22 10 22a14.976 14.976 0 007.663-1.583c.96-.337 1.963-.455 2.997-.337a14.5 14.5 0 00.017-.002l3.134 3.133a.75.75 0 001.061-1.06l-1.882-1.882a14.5 14.5 0 00-.002-.017z" />',
                                    'cultural' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.21 15.21A10.036 10.036 0 019.21 13.21c.002 1.328-.082 2.589-.306 3.816.28.509.586.985.92 1.425.296.394.613.756.949 1.093.356.357.735.699 1.131 1.021.395.323.81.617 1.239.89.428.272.875.514 1.34.734.54.253 1.098.485 1.678.701.598.223 1.212.42 1.842.591.63.171 1.275.32 1.923.451.648.131 1.301.248 1.955.356.654.108 1.314.207 1.977.297.663.09 1.33.168 2 .238.67.07 1.342.13 2.014.18.672.05 1.343.086 2.015.11.67.024 1.34-.002 2.008-.03.667-.033 1.333-.079 2-.135.667-.056 1.333-.123 1.998-.204.665-.08 1.328-.171 1.99-.276.662-.105 1.322-.224 1.98-.35.657-.126 1.312-.256 1.97-.396.653-.14 1.304-.299 1.95-.46.65-.162 1.297-.347 1.94-.54.646-.192 1.29-.416 1.93-.63.642-.214 1.283-.5 1.92-.6.639-.1 1.276-.62 1.91-.75" />',
                                    'food' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292 1.591-1.425 3.695-2.292 6-2.292a8.987 8.987 0 016-2.25v-14.25A8.966 8.966 0 0012 6.042z" />',
                                    'comedy' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7V9.75a8.967 8.967 0 01-2.314 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a23.548 23.548 0 00-5.714 0" />',
                                    'wellness' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 10.5H18.75v.75c0 2.485-2.099 4.5-4.688 4.5-1.935 0-3.597-1.126-4.312-2.733A13.475 13.475 0 013.248 12c0-1.13.083-2.252.245-3.374.166-1.152.438-2.282.815-3.374h6.081A4.5 4.5 0 0118 6v.75m0 0H10.5a2.25 2.25 0 000 4.5h2.25m4.5 0H19.5a2.25 2.25 0 000-4.5h-2.25M3.75 21h16.5" />',
                                    'family' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />',
                                ];
                            @endphp
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                {!! $icons[$cat['icon']] !!}
                            </svg>
                        </div>
                        <h3 class="font-semibold text-secondary-900 mb-1">{{ $cat['label'] }}</h3>
                        <p class="text-sm text-{{ $cat['color'] }}-600 font-medium">{{ $cat['count'] }} festivals</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 sm:py-20 lg:py-24 animate-in" style="animation-delay: 0.8s;" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 mb-6">Your Gateway to Unforgettable Experiences</h2>
                    <p class="text-lg text-secondary-600 mb-6">
                        PestaPora is Indonesia's premier platform for discovering and joining the best music festivals, entertainment events, and cultural celebrations. We believe life is meant to be celebrated.
                    </p>
                    <p class="text-secondary-600 mb-8">
                        Whether you're into electronic dance music, rock festivals, cultural arts, or family-friendly events, we curate the best experiences across the archipelago. From Jakarta's biggest stadiums to Bali's beachside venues, your next adventure starts here.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('events.index') }}" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Browse Festivals
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline border-primary-300 text-primary-700 hover:bg-primary-50">
                                Create Free Account
                            </a>
                        @endguest
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="card p-6 text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">200+</div>
                        <p class="text-secondary-500 mt-1">Festivals Listed</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">150K+</div>
                        <p class="text-secondary-500 mt-1">Happy Attendees</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">50+</div>
                        <p class="text-secondary-500 mt-1">Cities Covered</p>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">99%</div>
                        <p class="text-secondary-500 mt-1">Satisfaction Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 sm:py-20 lg:py-24 animate-in" style="animation-delay: 0.9s;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden bg-gradient-to-r from-primary-700 via-primary-600 to-primary-700">
                <div class="p-8 sm:p-12 lg:p-16 text-center">
                    <div class="pointer-events-none absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 36v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 6V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>

                    <div class="relative">
                        <svg class="w-20 h-20 mx-auto mb-6 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0-1.034-.118-2.037-.337-2.997a14.976 14.976 0 00-6.41-6.41C14.037 4.118 13.034 4 12 4a14.976 14.976 0 00-7.663 1.583C4.118 7.963 4 8.966 4 10a14.976 14.976 0 001.583 7.663 14.976 14.976 0 006.41 6.41C7.963 21.882 8.966 22 10 22a14.976 14.976 0 007.663-1.583c.96-.337 1.963-.455 2.997-.337a14.5 14.5 0 00.017-.002l3.134 3.133a.75.75 0 001.061-1.06l-1.882-1.882a14.5 14.5 0 00-.002-.017z" />
                        </svg>

                        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                            Ready for Your Next Festival Adventure?
                        </h2>

                        <p class="text-white/90 text-lg max-w-2xl mx-auto mb-8">
                            Join thousands of festival-goers on PestaPora. Discover, register, and experience the best music festivals and entertainment events across Indonesia.
                        </p>

                        <div class="flex justify-center">
                            @guest
                                <a href="{{ route('register') }}" class="btn btn-lg bg-white text-primary-600 hover:bg-secondary-50 w-full sm:w-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Get Started Free
                                </a>
                            @else
                                @if(Auth::user()->isAttendee())
                                    <a href="{{ route('registrations.index') }}" class="btn btn-lg bg-white text-primary-600 hover:bg-secondary-50 w-full sm:w-auto">
                                        My Registrations
                                    </a>
                                @else
                                    <a href="{{ route('events.index') }}" class="btn btn-lg bg-white text-primary-600 hover:bg-secondary-50 w-full sm:w-auto">
                                        Browse Festivals
                                    </a>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            function openMobileMenu() {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('flex');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            mobileMenuBtn?.addEventListener('click', () => {
                if (mobileMenu.classList.contains('hidden')) {
                    openMobileMenu();
                } else {
                    closeMobileMenu();
                }
            });

            // Close on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                    closeMobileMenu();
                }
            });

            // Close on outside click
            mobileMenu?.addEventListener('click', (e) => {
                if (e.target === mobileMenu || e.target instanceof Element && e.target.closest('a')) {
                    closeMobileMenu();
                }
            });

            // Categories Dropdown (Desktop)
            const categoriesDropdown = document.getElementById('categories-dropdown');
            const categoriesTrigger = document.getElementById('categories-trigger');
            const categoriesMenu = document.getElementById('categories-menu');
            let categoriesTimeout;

            function openCategoriesDropdown() {
                clearTimeout(categoriesTimeout);
                categoriesMenu.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                categoriesMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
                categoriesTrigger.setAttribute('aria-expanded', 'true');
                categoriesTrigger.querySelector('svg').style.transform = 'rotate(180deg)';
            }

            function closeCategoriesDropdown() {
                categoriesTimeout = setTimeout(() => {
                    categoriesMenu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    categoriesMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                    categoriesTrigger.setAttribute('aria-expanded', 'false');
                    categoriesTrigger.querySelector('svg').style.transform = 'rotate(0deg)';
                }, 150);
            }

            categoriesDropdown?.addEventListener('mouseenter', openCategoriesDropdown);
            categoriesDropdown?.addEventListener('mouseleave', closeCategoriesDropdown);
            categoriesMenu?.addEventListener('mouseenter', openCategoriesDropdown);
            categoriesMenu?.addEventListener('mouseleave', closeCategoriesDropdown);

            // Keyboard support
            categoriesTrigger?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (categoriesMenu.classList.contains('opacity-0')) {
                        openCategoriesDropdown();
                    } else {
                        closeCategoriesDropdown();
                    }
                } else if (e.key === 'Escape') {
                    closeCategoriesDropdown();
                    categoriesTrigger.focus();
                }
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!categoriesDropdown?.contains(e.target)) {
                    closeCategoriesDropdown();
                }
            });
        });
    </script>
@endsection