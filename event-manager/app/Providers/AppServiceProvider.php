<?php

namespace App\Providers;

use App\Models\Registration;
use App\Policies\RegistrationPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Registration::class, RegistrationPolicy::class);

        VerifyEmail::toMailUsing(static function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Verifikasi email Anda | '.config('app.name'))
                ->markdown('emails.verify-email', [
                    'userName' => $notifiable->name,
                    'appName' => config('app.name'),
                    'verificationUrl' => $url,
                    'expiresInMinutes' => config('auth.verification.expire', 60),
                ]);
        });
    }
}
