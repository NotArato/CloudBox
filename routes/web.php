<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Files
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::put('/files/{file}/rename', [FileController::class, 'rename'])->name('files.rename');
    Route::put('/files/{file}/move', [FileController::class, 'move'])->name('files.move');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    // Folders
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::put('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

    // Premium Payment (ABA PayWay)
    Route::get('/upgrade', [PaymentController::class, 'index'])->name('upgrade');
    Route::get('/payment/check', [PaymentController::class, 'checkTransaction'])->name('payment.check');
    Route::post('/upgrade/confirm', [PaymentController::class, 'confirmPayment'])->name('upgrade.confirm');
    Route::post('/upgrade/revert', [PaymentController::class, 'revertFree'])->name('upgrade.revert');
});

// ABA PayWay Server Pushback Webhook (No Auth / Server-to-Server)
Route::post('/payment/pushback', [PaymentController::class, 'pushback'])->name('payment.pushback');

