<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    public function view(User $user, Registration $registration): bool
    {
        return $user->id === $registration->user_id || $user->isAdmin() || $user->isOrganizer();
    }

    public function cancel(User $user, Registration $registration): bool
    {
        return $user->id === $registration->user_id && $registration->status === 'registered';
    }
}