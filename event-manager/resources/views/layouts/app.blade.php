<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PestaPora' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-secondary-50">
    <div class="flex min-h-screen">
        @unless($hideSidebar ?? false)
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar" aria-label="Main navigation">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-secondary-800">
                    <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
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
                        <a href="{{ route('dashboard') }}"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-600/20 text-white' : '' }}"
                           aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('registrations.index') }}"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('registrations.index') ? 'bg-primary-600/20 text-white' : '' }}"
                           aria-current="{{ request()->routeIs('registrations.index') ? 'page' : 'false' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                            </svg>
                            <span>My Registrations</span>
                        </a>

                        @can('isAdmin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-secondary-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600/20 text-white' : '' }}"
                               aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Admin Panel</span>
                            </a>
                        @endcan
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
        @endunless

        <!-- Main Content -->
        <main id="main-content" class="{{ ($hideSidebar ?? false) ? 'flex-1 flex flex-col min-w-0' : 'main-content flex-1 flex flex-col min-w-0' }}">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="flex min-h-16 items-center justify-between py-2 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        @unless($hideSidebar ?? false)
                        <button id="sidebar-toggle"
                                class="lg:hidden p-2 rounded-lg text-secondary-500 hover:text-secondary-700 hover:bg-secondary-100 transition-colors"
                                aria-label="Toggle navigation"
                                aria-expanded="false"
                                aria-controls="sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @endunless
                        <div class="min-w-0">
                            @yield('title')
                        </div>
                    </div>

                    <div class="flex flex-shrink-0 items-center gap-3">
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
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                sidebarOverlay.classList.add('block');
                sidebarToggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                sidebarOverlay.classList.remove('block');
                sidebarToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            sidebarToggle.addEventListener('click', () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
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
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>