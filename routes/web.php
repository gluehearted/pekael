<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use Gemini\Laravel\Facades\Gemini;

Route::get('/gemini-test', function () {
    $result = Gemini::generativeModel('models/gemini-2.5-flash')
        ->generateContent('apakah kmu tau tentang api gemini itu hrs bayar brpa jika data privasinya aman?');

    return $result->text();
});
require __DIR__ . '/auth.php';
