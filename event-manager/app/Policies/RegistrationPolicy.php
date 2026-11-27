<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    public function view(User $user, Registration $registration): bool
    {
        return $user->getKey() === $registration->getAttribute('user_id')
            || $user->isAdmin()
            || $user->isOrganizer() && $registration->event->getAttribute('organizer_id') === $user->getKey();
    }

    public function cancel(User $user, Registration $registration): bool
    {
        return $user->getKey() === $registration->getAttribute('user_id')
            && $registration->getAttribute('status') === 'registered';
    }
}
