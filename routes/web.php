<?php

use Illuminate\Support\Facades\Route;

// SPA entrypoint
Route::get('/{section}/{version}/{any?}', function () {
    return view('app');
})->where('any', '.*')->name('docs');

// Fallback for home or redirect
Route::get('/', function () {
    return redirect('/core/13.7');
});
