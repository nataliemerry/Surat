<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('users', [UsersController::class, 'index'])
    ->name('users')
    ->middleware('auth');

Route::get('users/create', [UsersController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');

Route::post('users', [UsersController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

Route::get('/surat', [DashboardController::class, 'index'])
    ->name('dashboard');

// ─── Surat Tugas ──────────────────────────────────────────────────────────────
Route::prefix('surat/tugas')->group(function () {
    Route::get('/', [SuratController::class, 'optionSuratTugas'])->name('surat.tugas.index');
    Route::get('/form', [SuratController::class, 'formSuratTugas'])->name('surat.tugas.form');
    Route::post('/store', [SuratController::class, 'storeSuratTugas'])->name('surat.tugas.store');
    Route::get('/upload', [SuratController::class, 'uploadSuratTugas'])->name('surat.tugas.upload');
    Route::post('/upload', [SuratController::class, 'updateSuratTugas'])->name('surat.tugas.upload.store');
    Route::get('/{surat}/edit', [SuratController::class, 'editSuratTugas'])->name('surat.tugas.edit')->middleware('auth');
    Route::put('/{surat}', [SuratController::class, 'editedSuratTugas'])->name('surat.tugas.update')->middleware('auth');
    Route::delete('/{surat}', [SuratController::class, 'destroySuratTugas'])->name('surat.tugas.destroy')->middleware('auth');
});

// ─── Surat Undangan ───────────────────────────────────────────────────────────
Route::prefix('surat/undangan')->group(function () {
    Route::get('/', [SuratController::class, 'optionSuratUndangan'])->name('surat.undangan.index');
    Route::get('/form', [SuratController::class, 'formSuratUndangan'])->name('surat.undangan.form');
    Route::post('/store', [SuratController::class, 'storeSuratUndangan'])->name('surat.undangan.store');
    Route::get('/upload', [SuratController::class, 'uploadSuratUndangan'])->name('surat.undangan.upload');
    Route::post('/upload', [SuratController::class, 'updateSuratUndangan'])->name('surat.undangan.upload.store');
    Route::get('/{surat}/edit', [SuratController::class, 'editSuratUndangan'])->name('surat.undangan.edit')->middleware('auth');
    Route::put('/{surat}', [SuratController::class, 'editedSuratUndangan'])->name('surat.undangan.update')->middleware('auth');
    Route::delete('/{surat}', [SuratController::class, 'destroySuratUndangan'])->name('surat.undangan.destroy')->middleware('auth');
});

// ─── Surat Dinas ──────────────────────────────────────────────────────────────
Route::prefix('surat/dinas')->group(function () {
    Route::get('/', [SuratController::class, 'optionSuratDinas'])->name('surat.dinas.index');
    Route::get('/form', [SuratController::class, 'formSuratDinas'])->name('surat.dinas.form');
    Route::post('/store', [SuratController::class, 'storeSuratDinas'])->name('surat.dinas.store');
    Route::get('/upload', [SuratController::class, 'uploadSuratDinas'])->name('surat.dinas.upload');
    Route::post('/upload', [SuratController::class, 'updateSuratDinas'])->name('surat.dinas.upload.store');
    Route::get('/{surat}/edit', [SuratController::class, 'editSuratDinas'])->name('surat.dinas.edit')->middleware('auth');
    Route::put('/{surat}', [SuratController::class, 'editedSuratDinas'])->name('surat.dinas.update')->middleware('auth');
    Route::delete('/{surat}', [SuratController::class, 'destroySuratDinas'])->name('surat.dinas.destroy')->middleware('auth');
});

// ─── ATK ──────────────────────────────────────────────────────────────────────
Route::prefix('atk')->group(function () {
    Route::get('/form', [App\Http\Controllers\AtkController::class, 'form'])->name('atk.form');
    Route::post('/store', [App\Http\Controllers\AtkController::class, 'store'])->name('atk.store');
});