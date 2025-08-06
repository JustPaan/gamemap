<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminGameController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminOrganizerController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerProfileController;
use App\Http\Controllers\OrganizerEventController;
use App\Http\Controllers\OrganizerAnalyticController;
use App\Http\Controllers\Organizer\OrganizerEventSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::redirect('/', '/login');

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// Public Event Routes
Route::controller(EventController::class)->name('events.')->group(function () {
    Route::get('/events', 'publicIndex')->name('public.index');
    Route::get('/events/{event}', 'publicShow')->name('public.show');
    Route::get('/eventregister', 'publicRegister')->name('public.register');
    Route::get('/events/map-data', 'getMapEvents')->name('map-data');
    // Registration
    Route::post('/events/{event}/register', 'register')->name('register');
    
    // Payment Routes
    Route::post('/events/{event}/checkout', 'checkout')
        ->name('checkout')
        ->middleware('auth');
        
    Route::get('/events/{event}/payment/success', 'paymentSuccess')
        ->name('payment.success')
        ->middleware('auth');
        
    Route::get('/events/{event}/payment/cancel', 'paymentCancel')
        ->name('payment.cancel')
        ->middleware('auth');
});


// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::get('/profile/info', 'info')->name('profile.info');
    });

    // Game routes
    Route::controller(GameController::class)->group(function () {
        Route::get('/games', 'publicIndex')->name('games.public.index');
        Route::get('/games/search', 'search')->name('games.search');
        Route::get('/games/{game}', 'show')->name('games.show');
    });

    // Event Management for Normal Users
    Route::controller(EventController::class)->name('events.')->group(function () {
        Route::get('/events', 'publicIndex')->name('public.index');
        Route::get('/events/{event}', 'publicShow')->name('public.show');
        Route::get('/events/{event}/edit', 'edit')->name('edit');
        Route::put('/events/{event}', 'update')->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (requires 'can:admin-access')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', fn () => redirect()->route('admin.dashboard'));

        // Admin organizer create route
        Route::get('/organizers/create', [AdminOrganizerController::class, 'create'])->name('organizers.create');

        // Organizer Approval
        Route::prefix('organizers')->name('organizers.')->group(function () {
            Route::get('/pending', [AdminController::class, 'pendingOrganizers'])->name('pending');
            Route::get('/approved', [AdminController::class, 'approvedOrganizers'])->name('approved');
            Route::get('/rejected', [AdminController::class, 'rejectedOrganizers'])->name('rejected');
            Route::post('/{user}/approve', [AdminController::class, 'approveOrganizer'])->name('approve');
            Route::post('/{user}/reject', [AdminController::class, 'rejectOrganizer'])->name('reject');
            Route::post('/{user}/revoke', [AdminController::class, 'revokeOrganizer'])->name('revoke');
            Route::post('/{user}/restore', [AdminController::class, 'restoreOrganizer'])->name('restore');
            Route::get('/{user}', [AdminController::class, 'showOrganizer'])->name('show');
        });

        // Organizer CRUD - Simplified
        Route::controller(AdminOrganizerController::class)->prefix('organizers')->name('organizers.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Game Management
        Route::controller(AdminGameController::class)->prefix('games')->name('games.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create'); 
            Route::post('/', 'store')->name('store');
            Route::get('/{game}/edit', 'edit')->name('edit');
            Route::put('/{game}', 'update')->name('update');
            Route::delete('/{game}', 'destroy')->name('destroy');
            Route::post('/{game}/toggle-delete-status', 'toggleDeleteStatus')->name('toggle-delete-status');
            Route::post('/{game}/restore', 'restore')->name('restore');
            Route::delete('/{game}/force-delete', 'forceDelete')->name('force-delete');
        });

        // User Management
        Route::controller(AdminUserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{user}/edit', 'edit')->name('edit');
            Route::put('/{user}', 'update')->name('update');
            Route::delete('/{user}', 'destroy')->name('destroy');
            Route::put('/update-all', 'updateAll')->name('updateAll');
            Route::post('/{user}/promote', 'promote')->name('promote');
        });

        // Event Management
        Route::get('/events', [AdminEventController::class, 'index'])->name('events');
        Route::post('/events', [AdminEventController::class, 'store'])->name('event.store');
        Route::get('/events/create', [AdminEventController::class, 'create'])->name('event.create');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('event.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('event.update');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('event.destroy');
        Route::get('/events/{event}/report', [AdminEventController::class, 'generateReport'])->name('event.report');

        // Preview Views
        Route::get('/organizer2', [AdminOrganizerController::class, 'index'])->name('organizer2');
        Route::get('/user2', [AdminUserController::class, 'user2'])->name('user2');
        Route::get('/game2', [AdminGameController::class, 'game2'])->name('game2');
    });

    /*
    |--------------------------------------------------------------------------
    | Organizer Routes (requires organizer role AND approval)
    |--------------------------------------------------------------------------
    */
    /* Temporarily disabled for deployment
    Route::middleware(['auth', 'can:organizer-access', 'organizer.approved'])->group(function () {
        Route::get('/organizer/dashboard', [OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

        // Organizer Profile
        Route::controller(OrganizerProfileController::class)->group(function () {
            Route::get('/organizer/profile', 'index')->name('organizer.profile');
            Route::get('/organizer/profile/edit', 'edit')->name('organizer.profile.edit');
            Route::post('/organizer/profile/update', 'update')->name('organizer.profile.update');
        });

        // Organizer Events
        Route::controller(OrganizerEventController::class)->group(function () {
            Route::get('/organizer/event', 'index')->name('organizer.event');
            Route::get('/organizer/event/form', 'create')->name('organizer.event.form');
            Route::post('/organizer/event', 'store')->name('organizer.event.store');
            Route::get('organizer/event/edit/{id}', [OrganizerEventController::class, 'edit'])->name('organizer.event.edit');
            Route::get('/organizer/eventdetail/{id}', 'viewDetails')->name('organizer.event.detail');
        });

        // Organizer Event Settings
        Route::controller(OrganizerEventSettingController::class)->group(function () {
            Route::get('/organizer/eventsetting', 'create')->name('organizer.eventsetting.create');
            Route::post('/organizer/eventsetting/store', 'store')->name('organizer.eventsetting.store');
            Route::get('/organizer/eventsetting/{event}/edit', 'edit')->name('organizer.eventsetting.edit');
            Route::put('/organizer/eventsetting/{event}', 'update')->name('organizer.eventsetting.update');
            Route::delete('/organizer/eventsetting/{event}', 'destroy')->name('organizer.eventsetting.destroy');
        });

        // Organizer Analytics
        Route::controller(OrganizerAnalyticController::class)->group(function () {
            Route::get('/organizer/analytics', [OrganizerAnalyticController::class, 'index'])->name('organizer.analytic');
        });
    });
    */

    /*
    |--------------------------------------------------------------------------
    | Gamer Routes (requires gamer role)
    |--------------------------------------------------------------------------
    */
    /* Temporarily disabled for deployment
    Route::middleware(['can:gamer-access'])->prefix('gamer')->name('gamer.')->controller(GameController::class)->group(function () {
        Route::get('/home', 'home')->name('home');
        Route::get('/events', 'events')->name('events');
        Route::get('/events/{event}', 'showEvent')->name('events.show');
        Route::post('/events/{event}/register', 'registerEvent')->name('events.register');
        Route::get('/games', 'showGames')->name('games');
    });
    */

    /*
    |--------------------------------------------------------------------------
    | Pending Organizer Notification
    |--------------------------------------------------------------------------
    */
    Route::get('/organizer/pending', function () {
        if (auth()->user()->role === 'organizer' && auth()->user()->is_approved === null) {
            return view('organizer.pending-approval');
        }
        return redirect()->route('home');
    })->middleware(['auth', 'can:organizer-access'])->name('organizer.pending');
});

