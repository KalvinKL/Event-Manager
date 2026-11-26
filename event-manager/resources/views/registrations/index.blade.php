@extends('layouts.app')

@section('title')
    <h1 class="h2">My Registrations</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($registrations->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-ticket-perforated fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No registrations yet</h4>
                <p class="text-muted">Browse events and register for ones you'd like to attend.</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-lg me-1"></i> Browse Events
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Barcode</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            <tr>
                                <td>
                                    <strong>{{ $registration->event->title }}</strong>
                                    @if($registration->event->status !== 'published')
                                        <span class="badge bg-warning text-dark ms-2">{{ ucfirst($registration->event->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $registration->event->start_date->format('M d, Y g:i A') }}</td>
                                <td>{{ $registration->event->location ?? 'TBA' }}</td>
                                <td>
                                    <span class="badge bg-{{ $registration->isCheckedIn() ? 'success' : ($registration->status === 'cancelled' ? 'danger' : 'info') }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </td>
                                <td>
                                    <code class="barcode-display">{{ $registration->barcode }}</code>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('registrations.show', $registration) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($registration->status === 'registered' && $registration->event->start_date->isFuture())
                                            <form action="{{ route('registrations.cancel', $registration) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this registration?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection