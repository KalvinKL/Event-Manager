<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ScannerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Create dummy festival events for landing page
    $featuredEvents = collect([
        (object) [
            'id' => 1,
            'title' => 'Jakarta International Java Jazz Festival 2025',
            'description' => 'Indonesia\'s biggest jazz festival returns with world-class artists across multiple stages. Experience 3 days of incredible jazz, fusion, and contemporary music.',
            'start_date' => \Carbon\Carbon::parse('2025-03-07 18:00'),
            'end_date' => \Carbon\Carbon::parse('2025-03-09 23:59'),
            'location' => 'JIExpo Kemayoran, Jakarta',
            'city' => 'Jakarta',
            'category' => 'Jazz Festival',
            'image' => null,
            'status' => 'published',
            'max_attendees' => 50000,
            'registrations' => (object) ['count' => 32450],
        ],
        (object) [
            'id' => 2,
            'title' => 'Djakarta Warehouse Project 2025',
            'description' => 'Asia\'s largest electronic dance music festival. Featuring the world\'s top DJs, mind-blowing production, and unforgettable moments across multiple stages.',
            'start_date' => \Carbon\Carbon::parse('2025-12-12 16:00'),
            'end_date' => \Carbon\Carbon::parse('2025-12-13 06:00'),
            'location' => 'JIExpo Kemayoran, Jakarta',
            'city' => 'Jakarta',
            'category' => 'EDM Festival',
            'image' => null,
            'status' => 'published',
            'max_attendees' => 80000,
            'registrations' => (object) ['count' => 67200],
        ],
        (object) [
            'id' => 3,
            'title' => 'Bali Beach Festival 2025',
            'description' => 'Sun, sand, and beats at Bali\'s premier beach festival. International and local artists, sunset parties, and the ultimate tropical festival experience.',
            'start_date' => \Carbon\Carbon::parse('2025-07-15 14:00'),
            'end_date' => \Carbon\Carbon::parse('2025-07-17 23:59'),
            'location' => 'Kuta Beach, Bali',
            'city' => 'Bali',
            'category' => 'Beach Festival',
            'image' => null,
            'status' => 'published',
            'max_attendees' => 25000,
            'registrations' => (object) ['count' => 18900],
        ],
    ]);

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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $registrationsCount = $user->registrations()->count();
        $checkedInCount = $user->registrations()->where('status', 'checked_in')->count();
        $upcomingEventsCount = $user->registrations()
            ->whereHas('event', fn($q) => $q->where('start_date', '>=', now()))
            ->where('status', '!=', 'cancelled')
            ->count();
        $upcomingRegistrations = $user->registrations()
            ->with('event')
            ->whereHas('event', fn($q) => $q->where('start_date', '>=', now()))
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'registrationsCount',
            'checkedInCount',
            'upcomingEventsCount',
            'upcomingRegistrations'
        ));
    })->name('dashboard');

    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])->name('registrations.index');
    Route::get('/events/{event}/register', [RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'cancel'])->name('registrations.cancel');
});

Route::prefix('scanner')->name('scanner.')->group(function () {
    Route::get('/{hash}', [ScannerController::class, 'showScanPage'])->name('scan');
    Route::post('/{hash}/scan', [ScannerController::class, 'scan'])->name('scan.post');
    Route::get('/{hash}/recent', [ScannerController::class, 'recentScans'])->name('recent');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
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