<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ReviewController;

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);

Route::middleware(['auth:api', 'role:BOOK_USER'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
});

Route::middleware(['auth:api', 'role:BOOK_ADMIN'])->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::patch('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});