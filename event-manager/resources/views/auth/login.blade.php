@extends('layouts.app', ['hideSidebar' => true])

@section('title')
    <h1 class="page-title">Welcome Back to PestaPora</h1>
    <p class="page-subtitle">Sign in to discover amazing festivals</p>
@endsection

@section('header-actions')
    <a href="{{ route('register') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        Create Account
    </a>
@endsection

@section('content')
    <div class="max-w-md mx-auto animate-in">
        <div class="card">
            <div class="card-header text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-secondary-900">Welcome Back</h2>
                <p class="text-secondary-500 mt-1">Sign in to your PestaPora account</p>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="label">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="email"
                               class="input @error('email') input-error @enderror"
                               placeholder="you@example.com"
                               aria-describedby="@error('email') email-error @endif">
                        @error('email')
                            <p id="email-error" class="form-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="label">Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               class="input @error('password') input-error @enderror"
                               placeholder="••••••••"
                               aria-describedby="@error('password') password-error @endif">
                        @error('password')
                            <p id="password-error" class="form-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-secondary-700">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-700">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-secondary-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700 ml-1">
                            Create one
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Features Preview -->
        <div class="mt-8 grid grid-cols-3 gap-4 animate-in" style="animation-delay: 0.1s;">
            <div class="text-center p-4 rounded-xl bg-secondary-50 hover:bg-secondary-100 transition-colors">
                <svg class="w-8 h-8 mx-auto mb-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.042 13.408a14.85 14.85 0 007.433-1.13l1.042-1.042a14.934 14.934 0 012.141 7.433l-1.042 1.042a14.85 14.85 0 00-7.433 1.13l-1.042 1.042a14.934 14.934 0 01-2.141-7.433l1.042-1.042z" />
                </svg>
                <p class="text-sm font-medium text-secondary-900">Browse Festivals</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-secondary-50 hover:bg-secondary-100 transition-colors">
                <svg class="w-8 h-8 mx-auto mb-2 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 2v2m0 2v2m-9-8h10m-10 0l2 2m0 0l2-2m-2 2l-2-2m2 2l-2 2m-14 0v2m0 2v2m0 2v2" />
                </svg>
                <p class="text-sm font-medium text-secondary-900">Easy Registration</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-secondary-50 hover:bg-secondary-100 transition-colors">
                <svg class="w-8 h-8 mx-auto mb-2 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <p class="text-sm font-medium text-secondary-900">Quick Check-in</p>
            </div>
        </div>
    </div>
@endsection