<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PublicCutiController;
use Illuminate\Support\Facades\Route;

// Public Access Routes (No Login Required)
Route::get('/', [PublicCutiController::class, 'index'])->name('public.pengajuan');
Route::post('/pengajuan-cuti', [PublicCutiController::class, 'store'])->name('public.pengajuan.store');
Route::get('/tracking', [PublicCutiController::class, 'tracking'])->name('public.tracking');
Route::get('/surat-cuti/{kode_tracking}', [PublicCutiController::class, 'suratCuti'])->name('public.surat');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes (Requires Login: Kadiv, HRD, Ketua STIKes)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Multi-Level Approvals
    Route::post('/cuti/{cuti}/approve-kadiv', [ApprovalController::class, 'approveKadiv'])->name('approval.kadiv');
    Route::post('/cuti/{cuti}/approve-hrd', [ApprovalController::class, 'approveHrd'])->name('approval.hrd');
    Route::post('/cuti/{cuti}/approve-ketua', [ApprovalController::class, 'approveKetua'])->name('approval.ketua');
    Route::post('/cuti/{cuti}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');

    // CRUD Divisi/Prodi (Accessible by HRD/Admin)
    Route::resource('divisi', DivisiController::class);

    // CRUD Pegawai (Accessible by HRD/Admin)
    Route::resource('pegawai', PegawaiController::class);
});
