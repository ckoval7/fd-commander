<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Authentication routes (placeholder - will be replaced by Breeze/Fortify later)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    Route::get('/profile/edit', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Contact Logging
Route::middleware(['auth', 'can:log-contacts'])->group(function () {
    Route::get('/contacts/create', function () {
        return view('contacts.create');
    })->name('contacts.create');
});

Route::get('/contacts', function () {
    return view('contacts.index');
})->name('contacts.index');

// Event Management
Route::middleware('auth')->group(function () {
    Route::get('/scoring', function () {
        return view('scoring.index');
    })->name('scoring.index');

    Route::get('/gallery', function () {
        return view('gallery.index');
    })->name('gallery.index');

    Route::get('/guestbook', function () {
        return view('guestbook.index');
    })->name('guestbook.index');
});

Route::middleware(['auth', 'can:manage-bonuses'])->group(function () {
    Route::get('/bonuses', function () {
        return view('bonuses.index');
    })->name('bonuses.index');
});

Route::middleware(['auth', 'can:manage-stations'])->group(function () {
    Route::get('/stations', function () {
        return view('stations.index');
    })->name('stations.index');
});

Route::middleware(['auth', 'can:manage-equipment'])->group(function () {
    Route::get('/equipment', function () {
        return view('equipment.index');
    })->name('equipment.index');
});

// Administration
Route::middleware(['auth', 'can:manage-events'])->group(function () {
    Route::get('/events', function () {
        return view('events.index');
    })->name('events.index');
});

Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');
});

Route::middleware(['auth', 'can:manage-settings'])->group(function () {
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
});

Route::middleware(['auth', 'can:view-reports'])->group(function () {
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});
