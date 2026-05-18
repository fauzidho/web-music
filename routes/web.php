<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\AdminController;
use Inertia\Inertia;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Secure main routes
Route::middleware('auth.firebase')->group(function () {
    Route::get('/', function (App\Services\FirebaseService $firebase) {
        try {
            $songs = $firebase->listDocuments('songs');
        } catch (\Exception $e) {
            $songs = [];
        }
        return Inertia::render('Home', [
            'songs' => $songs
        ]);
    })->name('home');

    // Background Interaction Routes
    Route::post('/track/{songId}/play', function ($songId, App\Services\FirebaseService $firebase) {
        try {
            $song = $firebase->getDocument('songs', $songId);
            if ($song) {
                $song['plays_count'] = ($song['plays_count'] ?? 0) + 1;
                $firebase->setDocument('songs', $songId, $song);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });

    Route::post('/track/{songId}/like', function ($songId, App\Services\FirebaseService $firebase) {
        try {
            $song = $firebase->getDocument('songs', $songId);
            if ($song) {
                $song['likes_count'] = ($song['likes_count'] ?? 0) + 1;
                $firebase->setDocument('songs', $songId, $song);
            }
            return response()->json(['likes_count' => $song['likes_count']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });

    // Producer / Creator Routes
    Route::middleware('role.producer')->group(function () {
        Route::get('/producer/dashboard', [ProducerController::class, 'dashboard'])->name('producer.dashboard');
        Route::get('/producer/upload', [ProducerController::class, 'showUpload'])->name('producer.upload');
        Route::post('/producer/upload', [ProducerController::class, 'upload']);
        Route::delete('/producer/track/{songId}', [ProducerController::class, 'delete']);
    });

    // Admin / Moderator Routes
    Route::middleware('role.admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/user/{uid}/role', [AdminController::class, 'toggleRole']);
        Route::post('/admin/track/{songId}/moderation', [AdminController::class, 'toggleModeration']);
        Route::delete('/admin/track/{songId}', [AdminController::class, 'deleteSong']);
    });
});
