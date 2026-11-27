<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScannerLink extends Model
{
    protected $fillable = [
        'event_id',
        'organizer_id',
        'hash',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        $event = $this->event;
        $organizer = $this->organizer;

        return $event
            && $organizer
            && $organizer->isOrganizer()
            && $event->organizer_id === $this->organizer_id;
    }

    public function getScannerUrl(): string
    {
        return route('scanner.scan', ['hash' => $this->hash]);
    }
}
