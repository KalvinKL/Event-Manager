@extends('layouts.app')

@section('title')
    <h1 class="h2">Register for {{ $event->title }}</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Confirm Registration</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" class="img-fluid rounded" alt="{{ $event->title }}">
                        @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-calendar-event fs-1 text-white-50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h4>{{ $event->title }}</h4>
                        <p class="text-muted">
                            <i class="bi bi-calendar-range me-1"></i>
                            {{ $event->start_date->format('l, F d, Y') }} at {{ $event->start_date->format('g:i A') }}
                        </p>
                        @if($event->location)
                            <p class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i> {{ $event->location }}
                            </p>
                        @endif
                        @if($event->max_attendees)
                            <p class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                {{ $event->registrations()->where('status', '!=', 'cancelled')->count() }} / {{ $event->max_attendees }} registered
                            </p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-2"></i>Registration Details</h6>
                    <ul class="mb-0">
                        <li>You will receive a confirmation email with your unique barcode</li>
                        <li>Present the barcode at the event for check-in</li>
                        <li>Each registration is personal and non-transferable</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('registrations.store', $event) }}">
                    @csrf
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Confirm Registration
                        </button>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection