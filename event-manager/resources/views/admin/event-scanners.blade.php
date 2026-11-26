@extends('layouts.app')

@section('title')
    <h1 class="h2">Scanner Links for {{ $event->title }}</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Generate Scanner Link</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.scanners.generate', $event) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="organizer_id" class="form-label">Organizer</label>
                        <select class="form-select" id="organizer_id" name="organizer_id" required>
                            <option value="">Select an organizer</option>
                            @foreach($organizers as $organizer)
                                <option value="{{ $organizer->id }}">{{ $organizer->name }} ({{ $organizer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                        <div class="form-text">Leave empty for no expiration</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Generate Scanner Link
                    </button>
                </form>
            </div>
        </div>

        @if(session('scanner_link'))
            <div class="card border-success mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Scanner Link Generated!</h5>
                </div>
                <div class="card-body">
                    <p><strong>Scanner URL:</strong></p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control font-monospace" value="{{ session('scanner_link')->getScannerUrl() }}" readonly id="scanner-url">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('scanner-url')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <p class="text-muted small mb-0">
                        Share this link with the organizer. They can use it to scan attendee barcodes at the event.
                        @if(session('scanner_link')->expires_at)
                            <br>This link expires on {{ session('scanner_link')->expires_at->format('M d, Y g:i A') }}.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4 sticky-top" style="top: 2rem;">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Event Info</h5>
            </div>
            <div class="card-body">
                <h6>{{ $event->title }}</h6>
                <p class="text-muted small mb-2">
                    <i class="bi bi-calendar-range me-1"></i>
                    {{ $event->start_date->format('M d, Y g:i A') }}
                </p>
                @if($event->location)
                    <p class="text-muted small mb-2">
                        <i class="bi bi-geo-alt me-1"></i> {{ $event->location }}
                    </p>
                @endif
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Total Registered</span>
                    <strong>{{ $event->registered_count }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span>Checked In</span>
                    <strong class="text-success">{{ $event->checked_in_count }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Existing Scanner Links</h5>
    </div>
    <div class="card-body">
        @if($scannerLinks->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-upc-scan fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No scanner links yet</h5>
                <p class="text-muted">Generate a scanner link to allow organizers to check in attendees.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Organizer</th>
                            <th>Hash</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scannerLinks as $link)
                            <tr class="{{ !$link->isValid() ? 'table-secondary' : '' }}">
                                <td>
                                    <strong>{{ $link->organizer->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $link->organizer->email }}</small>
                                </td>
                                <td>
                                    <code class="text-truncate d-inline-block" style="max-width: 150px;">{{ $link->hash }}</code>
                                </td>
                                <td>
                                    @if($link->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($link->expires_at)
                                        {{ $link->expires_at->format('M d, Y g:i A') }}
                                        @if($link->expires_at->isPast())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>{{ $link->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ $link->getScannerUrl() }}" target="_blank" class="btn btn-outline-info" title="Test Link">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        @if($link->is_active)
                                            <form action="{{ route('admin.scanners.revoke', $link) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Revoke this scanner link?')">
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
                {{ $scannerLinks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');
        
        const btn = element.nextElementSibling;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }
</script>
@endsection