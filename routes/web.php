<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentComparisonController;

Route::get('/', function () {
    return view('halaman');
});

// Document Comparison Routes
Route::get('/comparison', [DocumentComparisonController::class, 'index'])->name('comparison.index');
Route::post('/comparison/upload', [DocumentComparisonController::class, 'upload'])->name('comparison.upload');
Route::get('/comparison/{id}', [DocumentComparisonController::class, 'show'])->name('comparison.show');

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

require __DIR__.'/auth.php';
