<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Registration $registration
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->registration->event;
        $registrationUrl = route('registrations.show', $this->registration);

        return (new MailMessage)
            ->subject("Registration Confirmed: {$event->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have successfully registered for **{$event->title}**.")
            ->line("**Event Details:**")
            ->line("Date: {$event->start_date->format('l, F d, Y')} at {$event->start_date->format('g:i A')}")
            ->line("Location: " . ($event->location ?? 'TBA'))
            ->line("**Your Barcode:** `{$this->registration->barcode}`")
            ->line("Please save this barcode. You will need to present it at the event for check-in.")
            ->action('View Registration', $registrationUrl)
            ->line('We look forward to seeing you at the event!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'registration_id' => $this->registration->id,
            'event_id' => $this->registration->event_id,
            'barcode' => $this->registration->barcode,
        ];
    }
}