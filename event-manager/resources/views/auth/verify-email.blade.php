@extends('layouts.app', ['hideSidebar' => true])

@section('title')
    <h1 class="page-title">Verify Your Email</h1>
    <p class="page-subtitle">Confirm your email address to access your event tickets</p>
@endsection

@section('content')
    @php($verificationResendAvailableAt = session('verification_resend_available_at'))

    <div class="max-w-md mx-auto animate-in">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="text-2xl font-bold text-secondary-900">Check your inbox</h2>
                <p class="text-secondary-500 mt-1">
                    We sent a verification link to <strong>{{ Auth::user()->email }}</strong>.
                </p>
            </div>

            <div class="card-body">
                @if (config('mail.default') === 'log')
                    <p class="alert alert-warning mb-6" role="status">
                        Email is currently written to the application log instead of being delivered to your inbox. Configure SMTP to receive this link by email.
                    </p>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <p class="alert alert-success mb-6" role="status">
                        A new verification link has been sent to your email address.
                    </p>
                @elseif (session('status') === 'verification-link-cooldown')
                    <p class="alert alert-warning mb-6" role="status">
                        Please wait for the countdown before requesting another verification email.
                    </p>
                @endif

                <p class="text-secondary-600 mb-6">
                    Open the link in that email to verify your account before registering for events.
                </p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button id="resend-verification"
                            type="submit"
                            class="btn btn-primary w-full py-3"
                            @disabled($verificationResendAvailableAt > now()->timestamp)>
                        Resend verification email
                    </button>
                    <p id="verification-resend-countdown" class="form-hint mt-3 text-center" aria-live="polite"></p>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
                    @csrf
                    <button type="submit" class="font-medium text-primary-600 hover:text-primary-700">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const resendButton = document.getElementById('resend-verification');
        const resendCountdown = document.getElementById('verification-resend-countdown');
        const resendAvailableAt = @js($verificationResendAvailableAt);

        if (resendButton && resendCountdown && resendAvailableAt) {
            const updateCountdown = () => {
                const secondsRemaining = Math.max(0, resendAvailableAt - Math.floor(Date.now() / 1000));
                resendButton.disabled = secondsRemaining > 0;
                resendButton.textContent = secondsRemaining > 0
                    ? `Resend verification email (${secondsRemaining}s)`
                    : 'Resend verification email';
                resendCountdown.textContent = secondsRemaining > 0
                    ? 'Please wait before requesting another email.'
                    : 'You can request another verification email now.';

                if (secondsRemaining === 0) {
                    window.clearInterval(timerId);
                }
            };
            const timerId = window.setInterval(updateCountdown, 1000);

            updateCountdown();
        }
    </script>
@endsection
