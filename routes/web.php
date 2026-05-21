<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QcmController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GlossaryController;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('accueil');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::post('/profil/update-name', [ProfileController::class, 'updateName']);
    Route::post('/profil/update-password', [ProfileController::class, 'updatePassword']);
    Route::post('/profil/update-avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('/profil/update-bio', [ProfileController::class, 'updateBio']);

    Route::get('/parcours', [DashboardController::class, 'parcours'])->name('parcours');
    Route::get('/epreuves', [DashboardController::class, 'epreuves'])->name('epreuves');
    Route::get('/classement', [LeaderboardController::class, 'index'])->name('classement');
    Route::get('/certificat', [CertificateController::class, 'show'])->name('certificat');

    Route::get('/quiz/{slug}', [QcmController::class, 'show'])->name('qcm.show');
    Route::get('/fiche/{slug}', [QcmController::class, 'fiche'])->name('qcm.fiche');
    Route::get('/flashcards/{slug}', [QcmController::class, 'flashcards'])->name('qcm.flashcards');
    Route::get('/glossaire', [GlossaryController::class, 'show'])->name('glossaire');

    Route::post('/api/scores', [ScoreController::class, 'store']);
    Route::get('/api/classement/{id}', [LeaderboardController::class, 'userDetails']);
    Route::get('/api/progress/{qcmName}', [ProgressController::class, 'show']);
    Route::post('/api/progress', [ProgressController::class, 'store']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin');
        Route::post('/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::delete('/scores/{id}', [AdminController::class, 'deleteScore']);
    });
});
