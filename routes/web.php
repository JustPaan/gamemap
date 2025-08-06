<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

// Image serving route for Digital Ocean compatibility
Route::get('/storage/game_images/{filename}', function ($filename) {
    $path = storage_path('app/public/game_images/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($path);
    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->name('game.image');

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

    Route::post('/stripe/webhook', [EventController::class, 'handleWebhook'])
         ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::get('/profile/info', 'info')->name('profile.info');

    // Add this with your other game routes
Route::controller(GameController::class)->group(function () {
    Route::get('/games', 'publicIndex')->name('games.public.index');
    Route::get('/games/search', 'search')->name('games.search'); // Add this line
    Route::get('/games/{game}', 'show')->name('games.show');
});

    // Event Management for Normal Users
    Route::controller(EventController::class)->name('events.')->group(function () {
        Route::get('/events', 'publicIndex')->name('public.index');
        Route::get('/events/{event}', 'publicShow')->name('public.show');
        Route::get('/events/{event}/edit', 'edit')->name('edit'); // Add this line
        Route::put('/events/{event}', 'update')->name('update');
    });    
    });
Route::get('/admin/organizers/create', [AdminOrganizerController::class, 'create'])->name('admin.organizers.create');
    /*
    |--------------------------------------------------------------------------
    | Admin Routes (requires 'can:admin-access')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', fn () => redirect()->route('admin.dashboard'));

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

        // Organizer CRUD
        Route::controller(AdminOrganizerController::class)->prefix('organizers')->name('organizers.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::resource('admin/organizers', AdminOrganizerController::class)->except(['create', 'edit']);
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::put('/bulk-update', 'bulkUpdate')->name('bulkUpdate');
            Route::get('/{id}/demote', 'demote')->name('demote');
            Route::get('/cancel', 'cancel')->name('cancel');
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

    /*
    |--------------------------------------------------------------------------
    | Gamer Routes (requires gamer role)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:gamer-access'])->prefix('gamer')->name('gamer.')->controller(GameController::class)->group(function () {
        Route::get('/home', 'home')->name('home');
        Route::get('/events', 'events')->name('events');
        Route::get('/events/{event}', 'showEvent')->name('events.show');
        Route::post('/events/{event}/register', 'registerEvent')->name('events.register');
        Route::get('/games', 'showGames')->name('games');
    });

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

// TEMPORARY DEBUG ROUTE - Check what's happening with avatar on refresh
Route::get('/debug-avatar', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    $output = '<div style="font-family: Arial; padding: 20px; background: #f5f5f5;">';
    $output .= '<h2>Avatar Debug Information (Refresh Issue)</h2>';
    $output .= '<p><strong>User ID:</strong> ' . $user->id . '</p>';
    $output .= '<p><strong>Name:</strong> ' . $user->name . '</p>';
    $output .= '<p><strong>Avatar (DB):</strong> ' . ($user->avatar ?? 'NULL') . '</p>';
    $output .= '<p><strong>Avatar URL:</strong> ' . $user->avatar_url . '</p>';
    $output .= '<p><strong>Current Time:</strong> ' . now() . '</p>';
    
    // Check if avatar file exists
    if ($user->avatar) {
        $filename = basename($user->avatar);
        $avatarPath = storage_path('app/public/avatars/' . $filename);
        $gameImagePath = storage_path('app/public/game_images/' . $filename);
        
        $output .= '<h3>File Status:</h3>';
        $output .= '<p><strong>Filename:</strong> ' . $filename . '</p>';
        $output .= '<p><strong>Avatar Path:</strong> ' . $avatarPath . '</p>';
        $output .= '<p><strong>Avatar Exists:</strong> ' . (file_exists($avatarPath) ? '✅ YES' : '❌ NO') . '</p>';
        $output .= '<p><strong>Game Image Path:</strong> ' . $gameImagePath . '</p>';
        $output .= '<p><strong>Game Image Exists:</strong> ' . (file_exists($gameImagePath) ? '⚠️ YES - PROBLEM!' : '✅ NO') . '</p>';
        
        // Check what serve_avatar.php would serve
        $serveUrl = url('/serve_avatar.php?f=' . $filename);
        $output .= '<p><strong>Serve Avatar URL:</strong> <a href="' . $serveUrl . '" target="_blank">' . $serveUrl . '</a></p>';
        
        // Check file sizes and modification times
        if (file_exists($avatarPath)) {
            $output .= '<p><strong>Avatar File Size:</strong> ' . filesize($avatarPath) . ' bytes</p>';
            $output .= '<p><strong>Avatar Modified:</strong> ' . date('Y-m-d H:i:s', filemtime($avatarPath)) . '</p>';
        }
        
        // Add fix button if avatar file is missing
        if (!file_exists($avatarPath)) {
            $output .= '<h3 style="color: red;">⚠️ AVATAR FILE MISSING!</h3>';
            $output .= '<p>Your database has an avatar filename but the file doesn\'t exist on the server.</p>';
            $output .= '<p><a href="/fix-missing-avatar" style="background: green; color: white; padding: 10px; text-decoration: none;">Reset Avatar to Allow New Upload</a></p>';
        }
    } else {
        $output .= '<h3>No Avatar Set (Database is NULL)</h3>';
        $output .= '<p>This means you can upload a new avatar and it should work.</p>';
    }
    
    $output .= '<h3>Directory Contents:</h3>';
    
    // List avatar directory contents
    $avatarDir = storage_path('app/public/avatars/');
    if (is_dir($avatarDir)) {
        $avatarFiles = scandir($avatarDir);
        $output .= '<h4>Avatars Directory:</h4>';
        foreach ($avatarFiles as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $avatarDir . $file;
                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                $fileTime = file_exists($filePath) ? date('Y-m-d H:i:s', filemtime($filePath)) : 'Unknown';
                $output .= '<p>• ' . $file . ' (' . $fileSize . ' bytes, modified: ' . $fileTime . ')</p>';
                
                // If this file exists but isn't in database, offer to fix it
                if ($file !== 'gitkeep' && !$user->avatar) {
                    $output .= '<p style="margin-left: 20px; color: orange;">⚠️ This file exists but isn\'t linked to your account!</p>';
                    $output .= '<p style="margin-left: 20px;"><a href="/link-avatar/' . urlencode($file) . '" style="background: blue; color: white; padding: 5px; text-decoration: none;">Link This File</a></p>';
                }
            }
        }
    }
    
    // List game_images directory contents (first 10 files)
    $gameDir = storage_path('app/public/game_images/');
    if (is_dir($gameDir)) {
        $gameFiles = array_slice(scandir($gameDir), 2, 10); // Skip . and .., get first 10
        $output .= '<h4>Game Images Directory (first 10):</h4>';
        foreach ($gameFiles as $file) {
            $output .= '<p>• ' . $file . '</p>';
        }
    }
    
    $output .= '<h3>All Users with Same Email:</h3>';
    $duplicates = \App\Models\User::where('email', $user->email)->get();
    foreach ($duplicates as $dup) {
        $output .= '<div style="border: 1px solid #ccc; padding: 10px; margin: 5px 0;">';
        $output .= '<p><strong>ID:</strong> ' . $dup->id . ' | <strong>Name:</strong> ' . $dup->name . ' | <strong>Avatar:</strong> ' . ($dup->avatar ?? 'NULL') . ' | <strong>Created:</strong> ' . $dup->created_at . '</p>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    return $output;
})->middleware('auth');

// TEMPORARY FIX ROUTE - Fix missing avatar file issue
Route::get('/fix-missing-avatar', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
    
    $user = auth()->user();
    
    // Reset avatar to null so user can upload a new one
    \App\Models\User::where('id', $user->id)->update(['avatar' => null]);
    
    return redirect('/debug-avatar')->with('message', 'Avatar reset! The database has been cleared. You can now upload a new profile picture and it will work properly.');
})->middleware('auth');

// TEMPORARY LINK ROUTE - Link existing avatar file to user account
Route::get('/link-avatar/{filename}', function ($filename) {
    if (!auth()->check()) {
        return redirect('/login');
    }
    
    $user = auth()->user();
    $filename = urldecode($filename);
    
    // Security check - only allow certain file types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return redirect('/debug-avatar')->with('message', 'Invalid file type.');
    }
    
    // Check if file exists
    $avatarPath = storage_path('app/public/avatars/' . $filename);
    if (!file_exists($avatarPath)) {
        return redirect('/debug-avatar')->with('message', 'File does not exist.');
    }
    
    // Update user's avatar field
    \App\Models\User::where('id', $user->id)->update(['avatar' => $filename]);
    
    return redirect('/debug-avatar')->with('message', 'Avatar linked successfully! Check your profile now.');
})->middleware('auth');

// DEVELOPMENT ONLY - Database Reset Route (remove in production)
Route::get('/dev/reset-database', function () {
    // Temporarily allow in all environments for reset
    return view('dev.reset-database');
});

Route::post('/dev/reset-database', function () {
    // Temporarily allow in all environments for reset
    try {
        \Illuminate\Support\Facades\Artisan::call('db:reset-data', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        return back()->with('success', 'Database reset completed successfully!')->with('output', $output);
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to reset database: ' . $e->getMessage());
    }
});

// EMERGENCY MANUAL RESET ROUTE - Use with caution
Route::get('/emergency/manual-reset/{confirm}', function ($confirm) {
    if ($confirm !== 'yes-delete-everything-now') {
        return 'Invalid confirmation token. Use: /emergency/manual-reset/yes-delete-everything-now';
    }
    
    $output = '<div style="font-family: Arial; padding: 20px;">';
    $output .= '<h2>🗑️ Manual Database Reset</h2>';
    
    try {
        // Clear tables manually
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        $tables = ['payments', 'participants', 'events', 'organizers', 'games', 'users'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $output .= "<p>✅ Cleared table: {$table}</p>";
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Clear uploaded files
        $avatarCount = 0;
        $gameCount = 0;
        $eventCount = 0;
        
        // Clear avatars
        $avatarFiles = Storage::disk('public')->files('avatars');
        foreach ($avatarFiles as $file) {
            if (basename($file) !== '.gitkeep') {
                Storage::disk('public')->delete($file);
                $avatarCount++;
            }
        }
        
        // Clear game images
        $gameFiles = Storage::disk('public')->files('game_images');
        foreach ($gameFiles as $file) {
            if (basename($file) !== '.gitkeep') {
                Storage::disk('public')->delete($file);
                $gameCount++;
            }
        }
        
        // Clear event images
        if (Storage::disk('public')->exists('event_images')) {
            $eventFiles = Storage::disk('public')->files('event_images');
            foreach ($eventFiles as $file) {
                if (basename($file) !== '.gitkeep') {
                    Storage::disk('public')->delete($file);
                    $eventCount++;
                }
            }
        }
        
        $output .= "<p>✅ Cleared {$avatarCount} avatar files</p>";
        $output .= "<p>✅ Cleared {$gameCount} game image files</p>";
        $output .= "<p>✅ Cleared {$eventCount} event image files</p>";
        $output .= '<p><strong>✅ Database reset completed successfully!</strong></p>';
        $output .= '<p><a href="/register">Register new admin account</a></p>';
        
    } catch (\Exception $e) {
        $output .= '<p style="color: red;">❌ Error: ' . $e->getMessage() . '</p>';
    }
    
    $output .= '</div>';
    return $output;
});