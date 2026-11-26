<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\ScannerLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    public function showScanPage($hash)
    {
        $scannerLink = ScannerLink::where('hash', $hash)->first();

        if (!$scannerLink || !$scannerLink->isValid()) {
            abort(404, 'Invalid or expired scanner link.');
        }

        $event = $scannerLink->event;

        return view('scanner.scan', compact('scannerLink', 'event'));
    }

    public function scan(Request $request, $hash)
    {
        $scannerLink = ScannerLink::where('hash', $hash)->first();

        if (!$scannerLink || !$scannerLink->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired scanner link.',
            ], 404);
        }

        $request->validate([
            'barcode' => 'required|string',
        ]);

        $registration = Registration::where('barcode', $request->barcode)
            ->where('event_id', $scannerLink->event_id)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found for this event.',
            ], 404);
        }

        if ($registration->isCheckedIn()) {
            return response()->json([
                'success' => false,
                'message' => 'This attendee has already checked in.',
                'registration' => $registration->load('user'),
            ], 409);
        }

        if ($registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This registration has been cancelled.',
            ], 409);
        }

        $registration->checkIn();

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'registration' => $registration->load('user'),
        ]);
    }

    public function recentScans($hash)
    {
        $scannerLink = ScannerLink::where('hash', $hash)->first();

        if (!$scannerLink || !$scannerLink->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired scanner link.',
            ], 404);
        }

        $scans = Registration::where('event_id', $scannerLink->event_id)
            ->where('status', 'checked_in')
            ->with('user')
            ->latest('checked_in_at')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'scans' => $scans,
        ]);
    }
}