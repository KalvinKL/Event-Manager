@extends('layouts.app')

@section('title')
    <h1 class="page-title">Organizer Dashboard</h1>
    <p class="page-subtitle">Manage the events assigned to your organization.</p>
@endsection

@section('header-actions')
    <a href="{{ route('events.index') }}" class="btn btn-outline">Browse public events</a>
@endsection

@section('content')
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
                                    <td>
                                        <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-outline btn-sm">Manage</a>
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
