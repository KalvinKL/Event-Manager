<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ScannerController;
use App\Models\Event;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredEvents = Event::query()
        ->where('status', 'published')
        ->where('start_date', '>=', now())
        ->where('is_featured', true)
        ->withCount(['registrations as registrations_count' => fn ($query) => $query->where('status', '!=', 'cancelled')])
        ->orderBy('start_date')
        ->take(3)
        ->get();

    return view('welcome', compact('featuredEvents'));
})->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/categories', function () {
    return view('categories.index');
})->name('categories.index');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->intended(route('home'))
        ->with('success', 'Your email address has been verified.');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isOrganizer()) {
            return redirect()->route('organizer.dashboard');
        }

        return redirect()->route('home');
    })->name('dashboard');

    Route::middleware('verified')->group(function () {
        Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])->name('registrations.index');
        Route::get('/events/{event}/register', [RegistrationController::class, 'create'])->name('registrations.create');
        Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
        Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
        Route::delete('/registrations/{registration}', [RegistrationController::class, 'cancel'])->name('registrations.cancel');
    });
});

Route::middleware(['auth', 'organizer'])->prefix('scanner')->name('scanner.')->group(function () {
    Route::get('/{hash}', [ScannerController::class, 'showScanPage'])->name('scan');
    Route::post('/{hash}/scan', [ScannerController::class, 'scan'])->name('scan.post');
    Route::get('/{hash}/recent', [ScannerController::class, 'recentScans'])->name('recent');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/events/{event}/scanners', [AdminController::class, 'eventScanners'])->name('event.scanners');
    Route::post('/events/{event}/scanners', [AdminController::class, 'generateScannerLink'])->name('scanners.generate');
    Route::delete('/scanners/{scannerLink}', [AdminController::class, 'revokeScannerLink'])->name('scanners.revoke');
    Route::post('/users/{user}/make-organizer', [AdminController::class, 'makeOrganizer'])->name('users.make-organizer');
    Route::post('/users/{user}/remove-organizer', [AdminController::class, 'removeOrganizer'])->name('users.remove-organizer');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});

Route::middleware(['auth', 'organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', [OrganizerController::class, 'dashboard'])->name('dashboard');
    Route::get('/events/{event}/edit', [EventController::class, 'editForOrganizer'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'updateForOrganizer'])->name('events.update');
});
