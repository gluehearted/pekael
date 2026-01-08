<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentComparisonController;

Route::get('/', function () {
    return view('halaman');
});

// Document Comparison Routes
Route::get('/comparison', [DocumentComparisonController::class, 'index'])->name('comparison.index');
Route::post('/comparison/upload', [DocumentComparisonController::class, 'upload'])->name('comparison.upload');
Route::get('/comparison/{id}', [DocumentComparisonController::class, 'show'])->name('comparison.show');
