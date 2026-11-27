<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Notifications\RegistrationConfirmation;
use App\Services\RegistrationQrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function create(Event $event)
    {
        if (! $event->hasAvailableSlots()) {
            return back()->with('error', 'This event has reached maximum capacity.');
        }

        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'You are already registered for this event.');
        }

        return view('registrations.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        if (! $event->hasAvailableSlots()) {
            return back()->with('error', 'This event has reached maximum capacity.');
        }

        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'You are already registered for this event.');
        }

        $barcode = $this->generateUniqueBarcode();

        $registration = Registration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'barcode' => $barcode,
            'status' => 'registered',
        ]);

        $request->user()->notify(new RegistrationConfirmation($registration));

        return redirect()->route('registrations.show', $registration)
            ->with('success', 'Registration successful! Your barcode has been sent to your email.');
    }

    public function show(Registration $registration, RegistrationQrCodeGenerator $qrCodeGenerator)
    {
        Gate::authorize('view', $registration);
        $registration->load('event');
        $qrCodePng = $qrCodeGenerator->png($registration->barcode);

        return view('registrations.show', compact('registration', 'qrCodePng'));
    }

    public function myRegistrations()
    {
        $registrations = Auth::user()->registrations()
            ->with('event')
            ->latest()
            ->paginate(10);

        return view('registrations.index', compact('registrations'));
    }

    public function cancel(Registration $registration)
    {
        Gate::authorize('cancel', $registration);

        if (! $registration->event->start_date->isFuture()) {
            return back()->with('error', 'Cannot cancel registration for past events.');
        }

        $registration->update(['status' => 'cancelled']);

        return redirect()->route('registrations.index')
            ->with('success', 'Registration cancelled successfully.');
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = 'EVT-'.strtoupper(Str::random(12));
        } while (Registration::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
