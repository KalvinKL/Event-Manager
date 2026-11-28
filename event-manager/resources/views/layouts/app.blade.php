<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PestaPora' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/pestapora-mark.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-secondary-50">
    @php
        $showSidebar = !($hideSidebar ?? false)
            && !(Auth::check() && Auth::user()->isAttendee());
    @endphp
    <div class="flex min-h-screen">
        @if($showSidebar)
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar" aria-label="Main navigation">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-secondary-800">
                    <img src="{{ asset('images/pestapora-mark.svg') }}" alt="" class="w-10 h-10 flex-shrink-0">
                    <span class="text-white font-semibold text-lg truncate">PestaPora</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto" aria-label="Navigation">
                    <!-- Public Links -->
                    <a href="{{ route('events.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('events.index') ? 'bg-primary-600/20 text-white' : '' }}"
                       aria-current="{{ request()->routeIs('events.index') ? 'page' : 'false' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>All Events</span>
                    </a>

                    @auth
                        @php
                            $dashboardRoute = Auth::user()->isAdmin()
                                ? route('admin.dashboard')
                                : (Auth::user()->isOrganizer() ? route('organizer.dashboard') : route('dashboard'));
                            $dashboardActive = request()->routeIs('dashboard', 'admin.*', 'organizer.*')
                                && !request()->routeIs('admin.users.*');
                        @endphp
                        @if(!Auth::user()->isAttendee())
                        <a href="{{ $dashboardRoute }}"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ $dashboardActive ? 'bg-primary-600/20 text-white' : '' }}"
                           aria-current="{{ $dashboardActive ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ ucfirst(Auth::user()->role) }} Dashboard</span>
                        </a>
                        @endif

                        @if(Auth::user()->isAttendee())
                        <a href="{{ route('registrations.index') }}"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('registrations.index') ? 'bg-primary-600/20 text-white' : '' }}"
                           aria-current="{{ request()->routeIs('registrations.index') ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                            </svg>
                            <span>My Registrations</span>
                        </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-600/20 text-white' : '' }}"
                               aria-current="{{ request()->routeIs('admin.users.*') ? 'page' : 'false' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>User Management</span>
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- User Menu / Auth Actions -->
                <div class="p-4 border-t border-secondary-800">
                    @auth
                        <div class="flex items-center gap-3 px-3 py-2">
                            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-medium">{{ Str::upper(Auth::user()->name[0]) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium truncate">{{ Auth::user()->name }}</p>
                                <span class="badge badge-xs
                                    @if(Auth::user()->isAdmin()) badge-danger
                                    @elseif(Auth::user()->isOrganizer()) badge-warning
                                    @else badge-primary @endif">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <div class="space-y-2">
                            <a href="{{ route('login') }}"
                               class="w-full btn btn-outline justify-center text-white border-white/20 hover:bg-white/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                               class="w-full btn btn-primary justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" aria-hidden="true"></div>
        @endif

        <!-- Main Content -->
        <main id="main-content" class="{{ $showSidebar ? 'main-content flex-1 flex flex-col min-w-0' : 'flex-1 flex flex-col min-w-0' }}">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="flex min-h-16 items-center justify-between py-2 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        @if($showSidebar)
                        <button id="sidebar-toggle"
                                class="lg:hidden p-2 rounded-lg text-secondary-500 hover:text-secondary-700 hover:bg-secondary-100 transition-colors"
                                aria-label="Toggle navigation"
                                aria-expanded="false"
                                aria-controls="sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @endif
                        @if(!$showSidebar && !request()->routeIs('home'))
                            <a href="{{ route('home') }}" aria-label="PestaPora home" class="flex-shrink-0">
                                <img src="{{ asset('images/pestapora-mark.svg') }}" alt="" class="w-8 h-8">
                            </a>
                        @endif
                        <div class="min-w-0">
                            @yield('title')
                        </div>
                    </div>

                    <div class="flex flex-shrink-0 items-center gap-3">
                        @if(Auth::check() && Auth::user()->isAttendee() && !request()->routeIs('home'))
                            <a href="{{ route('home') }}"
                               class="btn btn-ghost btn-icon-sm"
                               aria-label="Home"
                               title="Home">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                                </svg>
                            </a>
                            <a href="{{ route('registrations.index') }}" class="btn btn-primary px-3 py-2 text-sm sm:text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75V6a2.25 2.25 0 00-2.25-2.25h-4.5A2.25 2.25 0 007.5 6v.75m9 0H7.5m9 0h1.125c1.243 0 2.25 1.007 2.25 2.25v9.75A2.25 2.25 0 0117.625 21h-11.25a2.25 2.25 0 01-2.25-2.25V9a2.25 2.25 0 012.25-2.25H7.5m3 4.5h3m-3 3h3" />
                                </svg>
                                <span class="hidden sm:inline">My Registrations</span>
                                <span class="sm:hidden">Tickets</span>
                            </a>
                        @endif
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="page-header">
                    @if(isset($pageTitle))
                        <h1 class="page-title">{{ $pageTitle }}</h1>
                        @if(isset($pageSubtitle))
                            <p class="page-subtitle">{{ $pageSubtitle }}</p>
                        @endif
                    @endif
                </div>

                <div class="px-4 sm:px-6 lg:px-8 pb-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success mb-6 animate-in" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <div>
                                <p class="font-medium">{{ session('success') }}</p>
                            </div>
                            <button type="button"
                                    class="text-success-500 hover:text-success-700 p-1 ml-auto"
                                    onclick="this.parentElement.remove()"
                                    aria-label="Dismiss">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-error mb-6 animate-in" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <div>
                                <p class="font-medium">{{ session('error') }}</p>
                            </div>
                            <button type="button"
                                    class="text-danger-500 hover:text-danger-700 p-1 ml-auto"
                                    onclick="this.parentElement.remove()"
                                    aria-label="Dismiss">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sidebar Toggle - only run if sidebar exists
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        if (sidebar && sidebarOverlay && sidebarToggle) {
            function openSidebar() {
                sidebar.classList.add('sidebar-open');
                sidebarOverlay.classList.remove('hidden');
                sidebarOverlay.classList.add('block');
                sidebarToggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.add('hidden');
                sidebarOverlay.classList.remove('block');
                sidebarToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            sidebarToggle.addEventListener('click', () => {
                if (sidebar.classList.contains('sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            sidebarOverlay.addEventListener('click', closeSidebar);

            // Close sidebar on navigation link click (mobile)
            document.querySelectorAll('#sidebar .nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });

            // Handle resize
            window.addEventListener('resize', closeSidebar);

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('sidebar-open')) {
                    closeSidebar();
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>