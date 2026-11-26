@extends('layouts.app')

@section('title')
    <h1 class="h2">Registration Confirmation</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $registration->event->title }}</h5>
                <span class="badge bg-{{ $registration->isCheckedIn() ? 'success' : 'info' }} fs-6">
                    {{ ucfirst($registration->status) }}
                </span>
            </div>
            <div class="card-body">
                @if($registration->event->image)
                    <img src="{{ asset('storage/' . $registration->event->image) }}" class="img-fluid rounded mb-3" alt="{{ $registration->event->title }}">
                @endif

                <dl class="row">
                    <dt class="col-sm-3">Date & Time</dt>
                    <dd class="col-sm-9">{{ $registration->event->start_date->format('l, F d, Y \a\t g:i A') }}</dd>

                    @if($registration->event->location)
                        <dt class="col-sm-3">Location</dt>
                        <dd class="col-sm-9">{{ $registration->event->location }}</dd>
                    @endif

                    <dt class="col-sm-3">Registered On</dt>
                    <dd class="col-sm-9">{{ $registration->created_at->format('M d, Y H:i') }}</dd>

                    @if($registration->checked_in_at)
                        <dt class="col-sm-3">Checked In</dt>
                        <dd class="col-sm-9">{{ $registration->checked_in_at->format('M d, Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-upc-scan me-2"></i>Your Barcode</h5>
            </div>
            <div class="card-body text-center">
                <div class="alert alert-light border">
                    <div class="barcode-display mb-2">{{ $registration->barcode }}</div>
                    <div id="barcode-image"></div>
                </div>
                <p class="text-muted small mt-3">
                    Present this barcode at the event for check-in. You can also save this page or take a screenshot.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <a href="{{ route('registrations.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Registrations
                    </a>
                </div>
            </div>
        </div>

        @if(!$registration->isCheckedIn())
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Important</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Save this barcode - you'll need it on the event day</li>
                        <li>Arrive early to allow time for check-in</li>
                        <li>Bring a valid ID for verification</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/@barcode-js/barcode-js@2.0.0/dist/barcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const barcodeValue = '{{ $registration->barcode }}';
        try {
            const barcode = new Barcode('#barcode-image', barcodeValue, {
                format: 'CODE128',
                displayValue: true,
                fontSize: 16,
                height: 80,
                margin: 10
            });
        } catch (e) {
            console.log('Barcode generation failed:', e);
        }
    });
</script>
@endsection