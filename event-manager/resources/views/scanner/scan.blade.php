@extends('layouts.app')

@section('title')
    <h1 class="h2">Event Scanner - {{ $event->title }}</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-camera-video me-2"></i>Barcode Scanner</h5>
                <span class="badge bg-success" id="scanner-status">Ready</span>
            </div>
            <div class="card-body">
                <div id="scanner-container" class="text-center">
                    <video id="scanner-video" class="scanner-video" playsinline></video>
                    <canvas id="scanner-canvas" style="display: none;"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <button id="start-scanner" class="btn btn-primary" onclick="startScanner()">
                        <i class="bi bi-camera me-1"></i> Start Scanner
                    </button>
                    <button id="stop-scanner" class="btn btn-secondary ms-2" onclick="stopScanner()" hidden>
                        <i class="bi bi-camera-video-off me-1"></i> Stop Scanner
                    </button>
                </div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" id="manual-barcode" class="form-control" placeholder="Or enter barcode manually...">
                        <button class="btn btn-outline-primary" type="button" onclick="manualScan()">
                            <i class="bi bi-search"></i> Scan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Scans</h5>
            </div>
            <div class="card-body" id="recent-scans">
                <div class="text-center text-muted py-4" id="no-scans">
                    <i class="bi bi-clock-history fs-1"></i>
                    <p class="mt-2">No scans yet</p>
                </div>
            </div>
        </div>
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
                    <strong id="total-registered">{{ $event->registered_count }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span>Checked In</span>
                    <strong id="total-checked-in" class="text-success">{{ $event->checked_in_count }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span>Remaining</span>
                    <strong id="total-remaining" class="text-warning">{{ $event->registered_count - $event->checked_in_count }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Organizer</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-1">Logged in as: <strong>{{ auth()->user()->name }}</strong></p>
                <p class="text-muted small mb-0">Role: <span class="badge bg-warning text-dark">{{ ucfirst($scannerLink->organizer->role) }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@zxing/library@0.20.0/umd/index.min.js"></script>
<script>
    let codeReader = null;
    let videoElement = document.getElementById('scanner-video');
    let isScanning = false;
    let scanInProgress = false;
    let lastDetectedBarcode = null;

    async function startScanner() {
        if (isScanning) {
            return;
        }

        try {
            const reader = new ZXing.BrowserMultiFormatReader();
            const videoInputDevices = await reader.listVideoInputDevices();
            if (videoInputDevices.length === 0) {
                alert('No camera found. Please use manual entry.');
                return;
            }

            const orderedDevices = [...videoInputDevices].sort((left, right) => {
                const priority = device => {
                    const label = device.label.toLowerCase();
                    if (label.includes('back') || label.includes('rear')) return 0;
                    return label.includes('virtual') ? 2 : 1;
                };

                return priority(left) - priority(right);
            });
            let lastCameraError = null;
            isScanning = true;

            for (const device of orderedDevices) {
                codeReader = new ZXing.BrowserMultiFormatReader();

                try {
                    await codeReader.decodeFromVideoDevice(device.deviceId, 'scanner-video', (result, err) => {
                        if (result) {
                            handleScanResult(result.getText(), true);
                        } else if (err instanceof ZXing.NotFoundException) {
                            lastDetectedBarcode = null;
                        } else if (err) {
                            console.error('Scanner error:', err);
                        }
                    });

                    document.getElementById('scanner-status').textContent = 'Scanning...';
                    document.getElementById('scanner-status').className = 'badge bg-warning text-dark';
                    document.getElementById('start-scanner').hidden = true;
                    document.getElementById('stop-scanner').hidden = false;
                    return;
                } catch (error) {
                    codeReader.reset();
                    codeReader = null;
                    lastCameraError = error;

                    if (error.name !== 'NotReadableError' && error.name !== 'NotFoundError') {
                        throw error;
                    }
                }
            }

            throw lastCameraError || new Error('No available camera could be started.');

        } catch (error) {
            console.error('Failed to start scanner:', error);
            isScanning = false;
            if (codeReader) {
                codeReader.reset();
                codeReader = null;
            }
            document.getElementById('scanner-status').textContent = 'Camera unavailable';
            document.getElementById('scanner-status').className = 'badge bg-danger';
            document.getElementById('start-scanner').hidden = false;
            document.getElementById('stop-scanner').hidden = true;

            const message = error.name === 'NotAllowedError'
                ? 'Camera access is blocked. Allow camera access in your browser settings, or enter the barcode manually.'
                : error.name === 'NotReadableError'
                    ? 'All available cameras are busy. Close other apps using the camera, or enter the barcode manually.'
                    : 'Could not start the camera. Please check camera access or enter the barcode manually.';
            alert(message);
        }
    }

    function stopScanner() {
        if (codeReader) {
            codeReader.reset();
            codeReader = null;
        }
        isScanning = false;
        lastDetectedBarcode = null;
        document.getElementById('scanner-status').textContent = 'Stopped';
        document.getElementById('scanner-status').className = 'badge bg-secondary';
        document.getElementById('start-scanner').hidden = false;
        document.getElementById('stop-scanner').hidden = true;
    }

    function manualScan() {
        const barcode = document.getElementById('manual-barcode').value.trim();
        if (barcode) {
            handleScanResult(barcode);
            document.getElementById('manual-barcode').value = '';
        }
    }

    async function handleScanResult(barcode, fromCamera = false) {
        if (scanInProgress) {
            return;
        }

        if (fromCamera) {
            if (lastDetectedBarcode === barcode) {
                return;
            }
            lastDetectedBarcode = barcode;
        } else if (!isScanning && !confirm('Process this barcode?')) {
            return;
        }

        scanInProgress = true;
        try {
            const response = await fetch('{{ route("scanner.scan.post", ["hash" => $scannerLink->hash]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ barcode: barcode })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', data.message);
                addRecentScan(data.registration);
                updateStats();
                playSuccessSound();
            } else {
                showToast('danger', data.message);
                playErrorSound();
            }
        } catch (error) {
            console.error('Scan error:', error);
            showToast('danger', 'An error occurred. Please try again.');
            playErrorSound();
        } finally {
            scanInProgress = false;
        }
    }

    function addRecentScan(registration) {
        const container = document.getElementById('recent-scans');
        const noScans = document.getElementById('no-scans');
        if (noScans) noScans.remove();

        container.insertBefore(createRecentScan(registration, 'Just now'), container.firstChild);

        // Keep only last 10 scans
        const scans = container.querySelectorAll('.border-bottom');
        if (scans.length > 10) {
            scans[scans.length - 1].remove();
        }
    }

    function createRecentScan(registration, timestamp) {
        const scanElement = document.createElement('div');
        scanElement.className = 'border-bottom pb-2 mb-2';

        const details = document.createElement('div');
        details.className = 'd-flex justify-content-between align-items-start';
        const attendee = document.createElement('div');
        const name = document.createElement('strong');
        name.textContent = registration.user.name;
        const barcode = document.createElement('small');
        barcode.className = 'text-muted';
        barcode.textContent = registration.barcode;
        const status = document.createElement('span');
        status.className = 'badge bg-success';
        status.textContent = 'Checked In';
        attendee.append(name, document.createElement('br'), barcode);
        details.append(attendee, status);

        const time = document.createElement('small');
        time.className = 'text-muted';
        time.textContent = timestamp;
        scanElement.append(details, time);

        return scanElement;
    }

    async function updateStats() {
        try {
            const response = await fetch('{{ route("scanner.recent", ["hash" => $scannerLink->hash]) }}');
            const data = await response.json();
            if (data.success) {
                document.getElementById('total-checked-in').textContent = data.scans.length;
                document.getElementById('total-remaining').textContent = 
                    parseInt(document.getElementById('total-registered').textContent) - data.scans.length;
            }
        } catch (error) {
            console.error('Failed to update stats:', error);
        }
    }

    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        const isSuccess = type === 'success';
        toast.className = `rounded-xl border px-5 py-3 shadow-lg ${isSuccess
            ? 'border-success-200 bg-success-50 text-success-800'
            : 'border-danger-200 bg-danger-50 text-danger-800'}`;
        toast.setAttribute('role', isSuccess ? 'status' : 'alert');
        toast.textContent = message;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-2';
        document.body.appendChild(container);
        return container;
    }

    function playSuccessSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
        audio.play().catch(() => {});
    }

    function playErrorSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
        audio.play().catch(() => {});
    }

    // Load recent scans on page load
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch('{{ route("scanner.recent", ["hash" => $scannerLink->hash]) }}');
            const data = await response.json();
            if (data.success && data.scans.length > 0) {
                const container = document.getElementById('recent-scans');
                const noScans = document.getElementById('no-scans');
                if (noScans) noScans.remove();
                
                data.scans.forEach(scan => {
                    container.appendChild(createRecentScan(scan, new Date(scan.checked_in_at).toLocaleString()));
                });
            }
        } catch (error) {
            console.error('Failed to load recent scans:', error);
        }
    });

    // Allow Enter key in manual input
    document.getElementById('manual-barcode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            manualScan();
        }
    });
</script>
@endsection