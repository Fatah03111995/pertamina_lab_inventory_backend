<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Filament expects a route named `auth.logout` for the logout form.
// If not defined, `filament()->getLogoutUrl()` may return `/` which causes
// POSTs to root and a MethodNotAllowedHttpException. Provide a simple
// POST logout route that invalidates the session and redirects home.
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('auth.logout');
