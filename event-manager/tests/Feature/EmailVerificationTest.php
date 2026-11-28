<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_attendees_must_verify_their_email_before_registering_for_events(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'New Attendee',
            'email' => 'attendee@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'attendee@example.test')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user): bool {
            $mail = $notification->toMail($user);
            $renderedMail = (string) $mail->render();

            return $mail->subject === 'Verifikasi email Anda | '.config('app.name')
                && $mail->markdown === 'emails.verify-email'
                && str_contains($renderedMail, 'Verifikasi alamat email Anda')
                && str_contains($renderedMail, $user->name)
                && str_contains($renderedMail, 'Verifikasi Email')
                && str_contains($renderedMail, '60 menit');
        });

        config(['mail.default' => 'log']);

        $this->get(route('verification.notice'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Email is currently written to the application log instead of being delivered to your inbox.');

        $this->get(route('registrations.index'))
            ->assertRedirect(route('verification.notice'));

        $this->from(route('verification.notice'))
            ->post(route('verification.send'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-cooldown');

        $this->travel(61)->seconds();

        $this->from(route('verification.notice'))
            ->post(route('verification.send'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-sent');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $this->get($verificationUrl)->assertRedirect();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get(route('registrations.index'))->assertOk();
    }
}
