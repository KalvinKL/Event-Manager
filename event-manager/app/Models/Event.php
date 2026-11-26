<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'city',
        'category',
        'image',
        'status',
        'max_attendees',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_attendees' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scannerLinks(): HasMany
    {
        return $this->hasMany(ScannerLink::class);
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->where('status', '!=', 'cancelled')->count();
    }

    public function getCheckedInCountAttribute(): int
    {
        return $this->registrations()->where('status', 'checked_in')->count();
    }

    public function hasAvailableSlots(): bool
    {
        if (!$this->max_attendees) {
            return true;
        }

        return $this->registered_count < $this->max_attendees;
    }
}