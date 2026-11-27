@extends('layouts.app')

@section('title')
    <h1 class="page-title">User Management</h1>
    <p class="page-subtitle">Manage attendee and organizer roles</p>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- User Management -->
        <div class="animate-in">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-secondary-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Accounts
                    </h2>
                </div>
                <div class="card-body">
                    <p class="text-sm text-secondary-500 mb-4">
                        Manage attendee and organizer roles. Admin accounts are protected.
                        <span class="ml-2">{{ $attendeeCount }} {{ Str::plural('attendee', $attendeeCount) }}, {{ $organizerCount }} {{ Str::plural('organizer', $organizerCount) }}</span>
                    </p>

                    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_12rem_auto] gap-3 items-end">
                        <div>
                            <label for="user_search" class="label">Search users</label>
                            <input id="user_search"
                                   type="search"
                                   name="user_search"
                                   value="{{ $userSearch }}"
                                   maxlength="100"
                                   class="input"
                                   placeholder="Name or email"
                                   autocomplete="off">
                        </div>
                        <div>
                            <label for="user_role" class="label">Role</label>
                            <select id="user_role" name="user_role" class="input">
                                <option value="all" @selected($userRole === 'all')>All users</option>
                                <option value="attendee" @selected($userRole === 'attendee')>Attendees</option>
                                <option value="organizer" @selected($userRole === 'organizer')>Organizers</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            @if($userSearch !== '' || $userRole !== 'all')
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Clear</a>
                            @endif
                        </div>
                    </form>

                    @if($users->isEmpty())
                        <div class="empty-state py-6">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <p class="text-sm text-secondary-500 text-center">
                                {{ $userSearch !== '' || $userRole !== 'all' ? 'No users match these filters.' : 'No attendee or organizer accounts yet.' }}
                            </p>
                            @if($userSearch !== '' || $userRole !== 'all')
                                <a href="{{ route('admin.users.index') }}" class="mt-3 inline-flex text-sm font-medium">Clear filters</a>
                            @endif
                        </div>
                    @else
                        <p class="mt-5 text-sm text-secondary-500" aria-live="polite">
                            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
                        </p>
                        <div class="mt-2 divide-y divide-secondary-100">
                            @foreach($users as $organizer)
                                <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="avatar avatar-md">
                                            {{ Str::upper($organizer->name[0]) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-secondary-900">{{ $organizer->name }}</p>
                                            <p class="break-all text-sm text-secondary-500">{{ $organizer->email }}</p>
                                        </div>
                                        <span class="badge {{ $organizer->isOrganizer() ? 'badge-warning' : 'badge-primary' }} shrink-0">
                                            {{ ucfirst($organizer->role) }}
                                        </span>
                                    </div>
                                    @if($organizer->isOrganizer())
                                        @if($organizer->organized_events_count > 0)
                                            <div class="sm:text-right">
                                                <button type="button"
                                                        class="btn btn-outline btn-sm w-full sm:w-auto"
                                                        aria-describedby="organizer-role-{{ $organizer->id }}-note"
                                                        disabled>
                                                    Reassign events first
                                                </button>
                                                <p id="organizer-role-{{ $organizer->id }}-note" class="mt-1 text-xs text-secondary-500">
                                                    {{ $organizer->organized_events_count }} {{ Str::plural('event', $organizer->organized_events_count) }} assigned. Reassign them before changing this role.
                                                </p>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.users.remove-organizer', $organizer) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-outline btn-sm w-full text-warning-700 border-warning-300 hover:bg-warning-50 sm:w-auto"
                                                        aria-label="Change {{ $organizer->name }} to attendee"
                                                        onclick="return confirm('Move {{ $organizer->name }} back to attendee?')">
                                                    Make attendee
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('admin.users.make-organizer', $organizer) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-success btn-sm w-full sm:w-auto"
                                                    aria-label="Make {{ $organizer->name }} an organizer"
                                                    onclick="return confirm('Make {{ $organizer->name }} an organizer?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                                Make Organizer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
