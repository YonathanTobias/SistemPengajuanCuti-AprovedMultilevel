<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PublicCutiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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
    // Dashboard (Current Year Only)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dedicated Annual Archive Module (Per Tahun)
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');

    // Multi-Level Approvals
    Route::post('/cuti/{cuti}/approve-kadiv', [ApprovalController::class, 'approveKadiv'])->name('approval.kadiv');
    Route::post('/cuti/{cuti}/approve-hrd', [ApprovalController::class, 'approveHrd'])->name('approval.hrd');
    Route::post('/cuti/{cuti}/approve-ketua', [ApprovalController::class, 'approveKetua'])->name('approval.ketua');
    Route::post('/cuti/{cuti}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');

    // Export & Reports (CSV, XLSX, PDF)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export-xlsx', [ReportController::class, 'exportXlsx'])->name('reports.export.xlsx');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/pegawai/{pegawai}/export-csv', [ReportController::class, 'exportPegawaiCsv'])->name('reports.export.pegawai.csv');
    Route::get('/pegawai/{pegawai}/export-xlsx', [ReportController::class, 'exportPegawaiXlsx'])->name('reports.export.pegawai.xlsx');

    // Tambah Cuti Khusus & Reset Kuota
    Route::post('/pegawai/{pegawai}/tambah-cuti-khusus', [PegawaiController::class, 'tambahCutiKhusus'])->name('pegawai.tambah-cuti-khusus');
    Route::post('/pegawai/reset-kuota-manual', [PegawaiController::class, 'resetKuotaManual'])->name('pegawai.reset-kuota-manual');

    // CRUD Divisi/Prodi (Accessible by HRD/Admin)
    Route::resource('divisi', DivisiController::class);

    // CRUD Pegawai (Accessible by HRD/Admin)
    Route::resource('pegawai', PegawaiController::class);

    // CRUD User Management (Accessible by HRD/Admin)
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class);
});
